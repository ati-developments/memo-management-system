<?php

namespace Tests\Feature;

use App\Models\{ApprovalWorkflow, Department, Memo, MemoTemplate, TemplateTable, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_text_formatting_survives_save_and_edit_and_removes_unsafe_html(): void
    {
        $memo = $this->draft();
        $html = '<b onclick="alert(1)">Bold</b> <i>Italic</i> <u>Underline</u><script>alert(1)</script><img src=x onerror="alert(1)">';
        $safe = '<strong>Bold</strong> <em>Italic</em> <u>Underline</u>';
        $this->put(route('memos.update', $memo), [
            'subject' => 'Formatted memo', 'action' => 'draft',
            'text_blocks' => [['position' => 'after_subject', 'text' => 'Bold Italic Underline', 'html' => $html]],
        ])->assertSessionHasNoErrors();
        $this->assertSame($safe, $memo->fresh()->text_blocks[0]['html']);
        $rendered = view('memos.pdf', ['memo' => $memo->fresh(), 'values' => collect(), 'approvals' => collect(), 'signatureImages' => []])->render();
        $this->assertStringContainsString($safe, $rendered);
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('data-rich-text', false);
        $this->get(route('memos.pdf', $memo))->assertOk();
    }

    public function test_memo_numbers_increment_daily_and_are_preserved_when_editing(): void
    {
        $draft = $this->draft();
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-09-17 10:00:00'));
        $payload = ['template_id' => $draft->template_id, 'subject' => 'Numbered memo', 'action' => 'draft'];
        $this->post(route('memos.store'), $payload)->assertSessionHasNoErrors();
        $first = Memo::latest('id')->first();
        $this->assertSame('MEMO/2026/09/17-001', $first->memo_number);
        $this->post(route('memos.store'), $payload)->assertSessionHasNoErrors();
        $this->assertSame('MEMO/2026/09/17-002', Memo::latest('id')->first()->memo_number);
        $this->put(route('memos.update', $first), $payload)->assertSessionHasNoErrors();
        $this->assertSame('MEMO/2026/09/17-001', $first->fresh()->memo_number);
        $this->assertSame(2, \Illuminate\Support\Facades\DB::table('memo_number_sequences')->value('last_number'));
        $this->get(route('memos.show', $first))->assertOk();
        $this->get(route('memos.pdf', [$first, 'download' => 1]))->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="MEMO-2026-09-17-001.pdf"');
        $this->travel(1)->days();
        $this->post(route('memos.store'), $payload)->assertSessionHasNoErrors();
        $this->assertSame('MEMO/2026/09/18-001', Memo::latest('id')->first()->memo_number);
        $this->travelBack();
    }

    private function draft(): Memo
    {
        $department = Department::create(['department_name' => 'Information Technology', 'department_code' => 'IT', 'status' => true]);
        $user = User::factory()->create(['username' => 'author', 'department_id' => $department->id]);
        $template = MemoTemplate::create(['department_id' => $department->id, 'template_name' => 'Payment', 'template_code' => 'PAY', 'status' => true]);
        $memo = Memo::create(['memo_number' => 'MEMO-TEST', 'template_id' => $template->id, 'department_id' => $department->id, 'created_by' => $user->id, 'subject' => 'Original payment', 'status' => 'draft', 'creation_type' => 'template']);
        $memo->fieldValues()->create(['field_name' => 'to', 'field_value' => 'Finance Manager']);
        $this->actingAs($user);

        return $memo;
    }

    public function test_optional_text_is_saved_only_on_the_memo_and_rendered_between_tables(): void
    {
        $draft = $this->draft();
        $tables = collect([1, 2])->map(function ($order) use ($draft) {
            $table = TemplateTable::create(['template_id' => $draft->template_id, 'table_name' => 'table_'.$order, 'table_label' => 'Table '.$order, 'table_order' => $order, 'is_active' => true]);
            $table->columns()->create(['column_name' => 'description', 'column_label' => 'Description', 'column_type' => 'text', 'is_active' => true]);
            return $table;
        });
        $blocks = [
            ['position' => 'after_subject', 'text' => "Opening sentence\nSecond line"],
            ['position' => 'table_'.$tables[1]->id, 'text' => '<script>alert(1)</script> Between tables'],
            ['position' => 'table_'.$tables[1]->id, 'text' => 'Another sentence'],
            ['position' => 'before_signatures', 'text' => 'Closing sentence'],
        ];
        $payload = ['template_id' => $draft->template_id, 'subject' => 'With text', 'action' => 'draft', 'text_blocks' => $blocks,
            'tables' => [$tables[1]->id => ['rows' => [['description' => 'Second table row']]], $tables[0]->id => ['rows' => [['description' => 'First table row']]]]];
        $this->post(route('memos.store'), $payload)->assertSessionHasNoErrors();
        $memo = Memo::latest('id')->first();
        $this->assertSame($blocks, $memo->text_blocks);
        $this->assertNull($draft->fresh()->text_blocks);
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('Another sentence')->assertSee('Before table: Table 2');
        $this->get(route('memos.create.template', $draft->template))->assertOk()->assertSee('Optional memo text')->assertDontSee('Another sentence');
        $html = view('memos.pdf', ['memo' => $memo, 'values' => collect(), 'approvals' => collect(), 'signatureImages' => []])->render();
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertLessThan(strpos($html, 'Between tables'), strpos($html, 'First table row'));
        $this->assertLessThan(strpos($html, 'Second table row'), strpos($html, 'Another sentence'));
        $this->get(route('memos.pdf', $memo))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $approval = $memo->approvals()->create(['approver_id' => $memo->created_by, 'approval_role' => 'prepared', 'action' => 'pending']);
        $this->get(route('approvals.review', $approval))->assertOk()
            ->assertSeeInOrder(['First table row', 'Between tables', 'Another sentence', 'Second table row'])
            ->assertDontSee('<iframe', false);

        $payload['text_blocks'] = [['position' => 'end', 'text' => 'Moved sentence']];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasNoErrors();
        $this->assertSame($payload['text_blocks'], $memo->fresh()->text_blocks);
        $payload['text_blocks'] = '';
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasNoErrors();
        $this->assertSame([], $memo->fresh()->text_blocks);
    }

    public function test_optional_text_validates_positions_and_retains_input_on_failure(): void
    {
        $memo = $this->draft();
        $payload = ['subject' => 'Test', 'action' => 'draft', 'text_blocks' => [['position' => 'table_99999', 'text' => 'Keep this sentence']]];
        $this->from(route('memos.edit', $memo))->put(route('memos.update', $memo), $payload)
            ->assertSessionHasErrors('text_blocks.0.position')->assertSessionHasInput('text_blocks.0.text', 'Keep this sentence');
        $this->assertNull($memo->fresh()->text_blocks);
        $payload['text_blocks'] = [['position' => 'after_subject', 'text' => str_repeat('a', 10001)]];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasErrors('text_blocks.0.text');
    }

    public function test_text_is_not_lost_when_its_field_or_table_has_no_value(): void
    {
        $memo = $this->draft();
        $field = $memo->template->fields()->create(['field_name' => 'note', 'field_label' => 'Note', 'field_type' => 'text', 'is_active' => true]);
        $table = $memo->template->tables()->create(['table_name' => 'empty', 'table_label' => 'Empty table', 'is_active' => true]);
        $this->put(route('memos.update', $memo), ['subject' => 'Empty sections', 'action' => 'draft', 'text_blocks' => [
            ['position' => 'field_'.$field->id, 'text' => 'Before empty field'],
            ['position' => 'table_'.$table->id, 'text' => 'Before empty table'],
        ]])->assertSessionHasNoErrors();
        $html = view('memos.document-body', ['memo' => $memo->fresh()])->render();
        $this->assertStringContainsString('Before empty field', $html);
        $this->assertStringContainsString('Before empty table', $html);
    }

    public function test_saved_recipients_appear_in_pdf_and_approval_document(): void
    {
        $draft = $this->draft();
        $this->post(route('memos.store'), [
            'template_id' => $draft->template_id, 'subject' => 'Recipient verification',
            'to' => 'Finance Director', 'through' => 'Operations Manager', 'action' => 'draft',
        ])->assertSessionHasNoErrors();
        $memo = Memo::latest('id')->first();
        $this->assertSame('Finance Director', $memo->fieldValues->firstWhere('field_name', 'to')->field_value);
        $this->assertSame('Operations Manager', $memo->fieldValues->firstWhere('field_name', 'through')->field_value);
        $pdfData = [];
        \Illuminate\Support\Facades\View::composer('memos.pdf', function ($view) use (&$pdfData) {
            $pdfData = $view->getData();
        });
        $this->get(route('memos.pdf', $memo))->assertOk();
        $html = view('memos.pdf', $pdfData)->render();
        $this->assertStringContainsString('Finance Director', $html);
        $this->assertStringContainsString('Operations Manager', $html);
        $approval = $memo->approvals()->create([
            'approver_id' => $memo->created_by, 'approval_role' => 'prepared', 'action' => 'pending',
        ]);
        $this->get(route('approvals.review', $approval))->assertOk()
            ->assertSee('Finance Director')->assertSee('Operations Manager')
            ->assertSee('class="letterhead"', false)
            ->assertDontSee('<iframe', false)
            ->assertSee('href="'.route('memos.pdf', $memo).'" class="print-button" target="_blank" rel="noopener"', false)
            ->assertDontSee('window.print()', false);
        $this->get(route('memos.pdf', $memo))->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename="'.preg_replace('/[^A-Za-z0-9_-]/', '-', $memo->memo_number).'.pdf"');
    }

    public function test_lists_link_to_working_view_and_edit_pages_and_pdf(): void
    {
        $memo = $this->draft();
        $this->get(route('memos.my'))->assertOk()->assertSee(route('memos.pdf', $memo))->assertSee('target="_blank"', false)->assertSee(route('memos.edit', $memo));
        $this->get(route('memos.all'))->assertOk()->assertSee(route('memos.pdf', $memo))->assertSee('target="_blank"', false);
        $this->get(route('memos.show', $memo))->assertOk()->assertSee('Download PDF')->assertSee('Open PDF / Print');
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('Finance Manager');
        $response = $this->get(route('memos.pdf', $memo))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->get(route('memos.pdf', [$memo, 'download' => 1]))->assertOk()->assertHeader('Content-Disposition', 'attachment; filename="MEMO-TEST.pdf"');
    }

    public function test_edit_updates_same_memo_and_replaces_rows_without_duplicates(): void
    {
        $memo = $this->draft();
        $table = TemplateTable::create(['template_id' => $memo->template_id, 'table_name' => 'charges', 'table_label' => 'Charges', 'is_active' => true]);
        $table->columns()->create(['column_name' => 'description', 'column_label' => 'Description', 'column_type' => 'text', 'is_active' => true]);
        $memo->tableRows()->create(['template_table_id' => $table->id, 'row_data' => ['description' => 'Old row'], 'row_order' => 0]);
        $payload = ['subject' => 'Updated payment', 'to' => 'New recipient', 'through' => 'Head of IT', 'date' => '2026-09-15', 'action' => 'draft', 'tables' => [$table->id => ['rows' => [['description' => 'Updated row'], ['description' => 'Second row']]]]];
        for ($i = 0; $i < 2; $i++) {
            $this->put(route('memos.update', $memo), $payload)->assertSessionHasNoErrors()->assertRedirect(route('memos.my'));
        }
        $this->assertDatabaseCount('memos', 1);
        $this->assertDatabaseHas('memos', ['id' => $memo->id, 'memo_number' => 'MEMO-TEST', 'subject' => 'Updated payment']);
        $this->assertSame(2, $memo->tableRows()->count());
        $this->assertSame(1, $memo->fieldValues()->where('field_name', 'to')->count());
        $this->assertDatabaseHas('memo_field_values', ['memo_id' => $memo->id, 'field_name' => 'through', 'field_value' => 'Head of IT']);
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('Second row');
        $this->get(route('memos.pdf', $memo))->assertOk();
    }

    public function test_only_owner_can_edit_and_only_while_draft(): void
    {
        $memo = $this->draft();
        $other = User::factory()->create(['username' => 'other']);
        $this->actingAs($other)->get(route('memos.edit', $memo))->assertForbidden();
        $this->put(route('memos.update', $memo), ['subject' => 'Tampered', 'action' => 'draft'])->assertForbidden();
        $this->get(route('memos.show', $memo))->assertOk();
        $this->actingAs($memo->creator);
        foreach (['pending', 'approved', 'rejected'] as $status) {
            $memo->update(['status' => $status]);
            $this->get(route('memos.edit', $memo))->assertForbidden();
            $this->put(route('memos.update', $memo), ['subject' => 'Tampered', 'action' => 'draft'])->assertForbidden();
        }
        $this->assertSame('Original payment', $memo->fresh()->subject);
    }

    public function test_draft_can_be_submitted_once_using_existing_workflow(): void
    {
        $memo = $this->draft();
        ApprovalWorkflow::create(['template_id' => $memo->template_id, 'workflow_name' => 'Approval', 'approval_type' => 'sequential', 'approval_rule' => 'all', 'is_active' => true]);
        $payload = ['subject' => 'Submitted payment', 'action' => 'submit'];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasNoErrors()->assertRedirect(route('memos.my'));
        $this->assertSame('pending', $memo->fresh()->status);
        $this->assertNotNull($memo->fresh()->submitted_at);
        $this->assertDatabaseHas('memo_approvals', ['memo_id' => $memo->id, 'approval_role' => 'prepared', 'action' => 'pending']);
        $this->put(route('memos.update', $memo), $payload)->assertForbidden();
    }

    public function test_validation_failure_keeps_draft_unchanged(): void
    {
        $memo = $this->draft();
        $this->from(route('memos.edit', $memo))->put(route('memos.update', $memo), ['subject' => '', 'action' => 'draft'])->assertSessionHasErrors('subject');
        $this->put(route('memos.update', $memo), ['subject' => 'No workflow', 'action' => 'submit'])->assertSessionHasErrors('action');
        $this->assertSame('Original payment', $memo->fresh()->subject);
    }

    public function test_create_saves_fixed_fields_and_guests_cannot_view_pdf(): void
    {
        $memo = $this->draft();
        $this->post(route('memos.store'), ['template_id' => $memo->template_id, 'subject' => 'New memo', 'to' => 'Director', 'through' => 'Manager', 'date' => '2026-09-15', 'action' => 'draft'])->assertSessionHasNoErrors()->assertRedirect(route('memos.my'));
        $created = Memo::latest('id')->first();
        $this->assertNotEquals($memo->id, $created->id);
        $this->assertDatabaseHas('memo_field_values', ['memo_id' => $created->id, 'field_name' => 'to', 'field_value' => 'Director']);
        auth()->logout();
        $this->get(route('memos.pdf', $memo))->assertRedirect(route('login'));
    }

    public function test_pdf_embeds_only_completed_approval_signatures_and_marks(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $memo = $this->draft();
        $image = \Illuminate\Http\UploadedFile::fake()->image('signature.png', 150, 45);
        $path = $image->store('signatures', 'public');
        $approval = $memo->approvals()->create([
            'approver_id' => $memo->created_by, 'approval_role' => 'prepared',
            'action' => 'approved', 'signature_path' => $path, 'approved_at' => now(),
        ]);
        $memo->update(['status' => 'approved']);
        $rendered = '';
        \Illuminate\Support\Facades\View::composer('memos.pdf', function ($view) use (&$rendered) {
            $rendered = $view->getData();
        });
        $response = $this->get(route('memos.pdf', $memo))->assertOk();
        $this->assertStringContainsString('/Subtype /Image', $response->getContent());
        $html = view('memos.pdf', $rendered)->render();
        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringNotContainsString('>APPROVED</div>', $html);
        $this->assertSame(1, substr_count($html, 'class="approval-confirmation"'));
        $this->assertStringContainsString('Approved &ndash; ' . $approval->approved_at->format('d M Y, h:i A'), $html);
        $this->assertMatchesRegularExpression('/class="approval-confirmation">Approved.*?<\/div>\s*<strong>/s', $html);

        $approval->update(['action' => 'pending', 'approved_at' => null]);
        $memo->update(['status' => 'pending']);
        $response = $this->get(route('memos.pdf', $memo))->assertOk();
        $this->assertStringNotContainsString('data:image/png;base64,', view('memos.pdf', $rendered)->render());
        $this->assertStringContainsString('data:image/jpeg;base64,', view('memos.pdf', $rendered)->render());
        $this->assertStringNotContainsString('class="approval-confirmation"', view('memos.pdf', $rendered)->render());

        $approval->update(['action' => 'approved', 'signature_path' => 'signatures/missing.png']);
        $this->get(route('memos.pdf', $memo))->assertOk();
        $this->assertStringContainsString('class="approval-confirmation">Approved', view('memos.pdf', $rendered)->render());
    }
}
