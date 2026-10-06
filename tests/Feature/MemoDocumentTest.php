<?php

namespace Tests\Feature;

use App\Models\{ApprovalWorkflow, Department, Memo, MemoTemplate, TemplateTable, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_emails_follow_workflow_order_and_stop_after_completion(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        $memo = $this->draft();
        $role = \App\Models\Role::create(['role_name' => 'Approver', 'status' => true]);
        $first = User::factory()->create(['username' => 'email-first', 'role_id' => $role->id]);
        $second = User::factory()->create(['username' => 'email-second', 'role_id' => $role->id]);
        $this->put(route('memos.update', $memo), [
            'subject' => 'Email approval test', 'action' => 'submit',
            'workflow_config' => ['approval_type' => 'sequential', 'approval_rule' => 'all',
                'steps' => [['approver_id' => $first->id], ['approver_id' => $second->id]]],
        ])->assertSessionHasNoErrors();
        $notification = \App\Notifications\MemoApprovalRequested::class;
        \Illuminate\Support\Facades\Notification::assertSentTo($first, $notification, function ($mail) use ($first, $memo) {
            $this->assertTrue($mail->afterCommit);
            $this->assertTrue($mail->shouldSend($first, 'mail'));
            $this->assertSame(route('approvals.review', $mail->approval), $mail->toMail($first)->actionUrl);
            return $mail->approval->memo_id === $memo->id;
        });
        \Illuminate\Support\Facades\Notification::assertNotSentTo($second, $notification);
        $approval = $memo->approvals()->where('approver_id', $first->id)->first();
        $this->actingAs($first)->post(route('approvals.decision', $approval), ['decision' => 'approved'])
            ->assertSessionHasNoErrors();
        \Illuminate\Support\Facades\Notification::assertSentToTimes($second, $notification, 1);
        $this->post(route('approvals.decision', $approval), ['decision' => 'approved']);
        \Illuminate\Support\Facades\Notification::assertSentToTimes($second, $notification, 1);

        $memo->update(['status' => 'rejected']);
        $pending = $memo->approvals()->where('approver_id', $second->id)->first();
        $this->assertFalse((new $notification($pending))->shouldSend($second, 'mail'));

        \Illuminate\Support\Facades\Notification::fake();
        $memo->update(['status' => 'draft']);
        app(\App\Services\MemoApprovalNotifier::class)->notifyReadyApprovers($memo);
        \Illuminate\Support\Facades\Notification::assertNothingSent();
        $memo->update(['status' => 'pending']);
        $approval->refresh()->update(['action' => 'pending']);
        $approval->workflow->update(['approval_type' => 'open']);
        app(\App\Services\MemoApprovalNotifier::class)->notifyReadyApprovers($memo);
        \Illuminate\Support\Facades\Notification::assertSentTo($first, $notification);
        \Illuminate\Support\Facades\Notification::assertSentTo($second, $notification);
    }

    public function test_memos_list_includes_each_assigned_approver_and_keeps_filters_scoped(): void
    {
        $memo = $this->draft();
        $creator = $memo->creator;
        $first = User::factory()->create(['username' => 'list-first', 'menu_access' => ['memos']]);
        $second = User::factory()->create(['username' => 'list-second', 'menu_access' => ['memos']]);
        $role = \App\Models\Role::create(['role_name' => 'Staff', 'status' => true]);
        foreach ([$creator, $first, $second] as $approver) {
            $approver->update(['role_id' => $role->id]);
            $memo->approvals()->create([
                'approver_id' => $approver->id, 'approval_role' => 'approval', 'action' => 'pending',
            ]);
        }
        $unrelated = $memo->replicate();
        $unrelated->memo_number = 'UNRELATED-MEMO';
        $unrelated->save();

        foreach (['pending', 'approved', 'rejected'] as $status) {
            $memo->update(['status' => $status]);
            foreach ([$first, $second] as $approver) {
                $this->actingAs($approver)->get(route('memos.my'))
                    ->assertOk()
                    ->assertViewHas('memos', fn ($memos) => $memos->pluck('id')->all() === [$memo->id])
                    ->assertViewHas('counts', fn ($counts) => $counts['all'] === 1 && $counts[$status] === 1);
                $this->get(route('memos.show', $memo))->assertOk();
                $this->get(route('memos.my', ['status' => 'draft']))
                    ->assertOk()->assertViewHas('memos', fn ($memos) => $memos->isEmpty());
                $this->get(route('memos.my', ['search' => 'UNRELATED-MEMO']))
                    ->assertOk()->assertViewHas('memos', fn ($memos) => $memos->isEmpty());
                $this->get(route('memos.show', $unrelated))->assertForbidden();
            }
        }

        $this->actingAs($creator)->get(route('memos.my'))->assertOk()
            ->assertViewHas('memos', fn ($memos) => $memos->total() === 2);
    }

    public function test_memo_workflow_is_saved_with_draft_and_submission_without_changing_template(): void
    {
        $memo = $this->draft();
        $original = User::factory()->create(['username' => 'original-approver']);
        $replacement = User::factory()->create(['username' => 'replacement-approver']);
        $workflow = ApprovalWorkflow::create(['template_id' => $memo->template_id, 'workflow_name' => 'Permanent workflow', 'approval_type' => 'sequential', 'approval_rule' => 'all', 'is_active' => true]);
        $step = $workflow->steps()->create(['approver_id' => $original->id, 'step_order' => 1, 'is_required' => true]);
        $this->get(route('memos.create.template', $memo->template))->assertOk()->assertSee('Approval Workflow')->assertSee('data-workflow-reset', false);
        $config = ['approval_type' => 'open', 'approval_rule' => 'minimum', 'minimum_approvals' => 1,
            'steps' => [['approver_id' => $replacement->id], ['approver_id' => $original->id]]];
        $this->put(route('memos.update', $memo), ['subject' => 'Custom workflow', 'action' => 'draft', 'workflow_config' => $config])->assertSessionHasNoErrors();
        $this->assertSame($config, $memo->fresh()->workflow_config);
        $this->assertSame(0, $memo->approvals()->count());
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('data-workflow-steps', false);
        $this->put(route('memos.update', $memo), ['subject' => 'Custom workflow', 'action' => 'submit'])->assertSessionHasNoErrors();
        $assigned = $memo->approvals()->where('approval_role', 'approval')->with('approvalStep')->get()->sortBy('chain_order');
        $this->assertSame([$replacement->id, $original->id], $assigned->pluck('approver_id')->all());
        $snapshot = $assigned->first()->workflow;
        $this->assertNotSame($workflow->id, $snapshot->id);
        $this->assertFalse($snapshot->is_active);
        $this->assertSame('open', $snapshot->approval_type);
        $this->assertSame('minimum', $snapshot->approval_rule);
        $this->assertSame('sequential', $workflow->fresh()->approval_type);
        $this->assertSame($original->id, $step->fresh()->approver_id);
        $workflow->update(['approval_rule' => 'any']);
        $step->update(['approver_id' => $replacement->id]);
        $this->assertSame('minimum', $snapshot->fresh()->approval_rule);
        $this->assertSame([$replacement->id, $original->id], $snapshot->steps()->pluck('approver_id')->all());
    }

    public function test_memo_workflow_rejects_invalid_approvers_and_minimum(): void
    {
        $memo = $this->draft();
        $config = ['approval_type' => 'open', 'approval_rule' => 'minimum', 'minimum_approvals' => 2,
            'steps' => [['approver_id' => $memo->created_by]]];
        $payload = ['subject' => 'Invalid workflow', 'action' => 'submit', 'workflow_config' => $config];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasErrors('workflow_config.minimum_approvals');
        $payload['workflow_config']['steps'][] = ['approver_id' => $memo->created_by];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasErrors('workflow_config.steps.0.approver_id');
        $payload['workflow_config']['steps'] = [['approver_id' => 999999]];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasErrors('workflow_config.steps.0.approver_id');
        $this->assertSame('draft', $memo->fresh()->status);
        $this->assertSame(0, $memo->approvals()->count());
    }

    public function test_table_cell_formatting_survives_save_edit_and_document_rendering(): void
    {
        $memo = $this->draft();
        $table = TemplateTable::create(['template_id' => $memo->template_id, 'table_name' => 'costs', 'table_label' => 'Costs', 'is_active' => true]);
        $table->columns()->create(['column_name' => 'amount', 'column_label' => 'Amount', 'column_type' => 'decimal', 'is_active' => true]);
        $formats = [
            ['cell' => 'tables['.$table->id.'][rows][2][amount]', 'bold' => 1, 'italic' => 1, 'underline' => 1],
            ['cell' => 'inserted_items[0][rows][0][column_0]', 'bold' => 1, 'italic' => 0, 'underline' => 1],
        ];
        $payload = ['subject' => 'Formatted cells', 'action' => 'draft', 'table_formats' => $formats,
            'tables' => [$table->id => ['rows' => [2 => ['amount' => '125.50']]]],
            'inserted_items' => [['kind' => 'table', 'columns' => [
                ['column_name' => 'column_0', 'column_label' => 'Notes', 'column_type' => 'text'],
            ], 'rows' => [['column_0' => 'Special payment']]]]];
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasNoErrors();
        $this->assertSame($formats, $memo->fresh()->table_formats);
        $this->assertSame('125.50', $memo->fresh()->tableRows->first()->row_data['amount']);
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('tables['.$table->id.'][rows][2][amount]', false);
        $html = view('memos.pdf', ['memo' => $memo->fresh(), 'values' => collect(), 'approvals' => collect(), 'signatureImages' => []])->render();
        $this->assertStringContainsString('style="font-weight:bold;font-style:italic;text-decoration:underline;">125.50', $html);
        $this->assertStringContainsString('style="font-weight:bold;text-decoration:underline;">Special payment', $html);
        $this->get(route('memos.pdf', $memo))->assertOk();
        $payload['table_formats'][0]['bold'] = 'font-size:999px';
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasErrors('table_formats.0.bold');
    }

    public function test_attachments_appear_in_the_pdf_and_approval_document(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        $image = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aD1sAAAAASUVORK5CYII=');
        \Illuminate\Support\Facades\Storage::disk('local')->put('memo-attachments/test.png', $image);
        $memo = $this->draft();
        $attachment = $memo->attachments()->create([
            'original_name' => 'Supporting screenshot.png',
            'storage_path' => 'memo-attachments/test.png',
            'mime_type' => 'image/png',
            'size' => 2048,
        ]);
        $url = route('memos.attachments.download', [$memo, $attachment]);
        $html = view('memos.pdf', [
            'memo' => $memo->fresh(), 'values' => collect(),
            'approvals' => collect(), 'signatureImages' => [],
        ])->render();
        $this->assertStringNotContainsString('Supporting screenshot.png', $html);
        $this->assertStringNotContainsString(' KB)', $html);
        $this->assertStringNotContainsString('&#x20;', $html);
        $this->assertStringContainsString('page-break-before:always', $html);
        $this->assertStringContainsString('@page attachment { margin: 0; }', $html);
        $this->assertStringContainsString('display:block;width:100%;height:auto', $html);
        $this->assertStringContainsString($url, $html);
        $this->assertStringContainsString('data:image/png;base64,'.base64_encode($image), $html);
        $response = $this->get(route('memos.pdf', $memo))->assertOk();
        $this->assertStringContainsString($url, $response->getContent());
        $this->assertMatchesRegularExpression('/\/Type\s*\/Pages\b[^>]*\/Count\s+2\b/s', $response->getContent());
        $approval = $memo->approvals()->create([
            'approver_id' => $memo->created_by, 'approval_role' => 'prepared', 'action' => 'pending',
        ]);
        $this->get(route('approvals.review', $approval))->assertOk()
            ->assertDontSee('Supporting screenshot.png')->assertSee($url)
            ->assertSee('Page 1 of 2')->assertSee('aria-label="Next page"', false)
            ->assertSee('aria-label="Previous page"', false)
            ->assertSee('data:image/png;base64,'.base64_encode($image), false);
        \Illuminate\Support\Facades\Storage::disk('local')->delete('memo-attachments/test.png');
        $this->assertNull($attachment->imagePreview());
    }

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
