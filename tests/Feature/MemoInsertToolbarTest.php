<?php

namespace Tests\Feature;

use App\Models\{Department, Memo, MemoTemplate, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoInsertToolbarTest extends TestCase
{
    use RefreshDatabase;

    private function template(): MemoTemplate
    {
        $department = Department::create(['department_name' => 'IT', 'department_code' => 'IT', 'status' => true]);
        $this->actingAs(User::factory()->create(['username' => 'toolbar', 'department_id' => $department->id]));
        return MemoTemplate::create(['department_id' => $department->id, 'template_name' => 'Payment', 'template_code' => 'PAY', 'status' => true]);
    }

    public function test_insertions_are_saved_only_on_the_memo_and_survive_editing(): void
    {
        $template = $this->template();
        $items = [
            ['kind' => 'field', 'label' => 'One time reference', 'type' => 'text', 'value' => 'Special reference'],
            ['kind' => 'table', 'label' => 'One time costs', 'columns' => [
                ['column_name' => 'column_0', 'column_label' => 'Amount', 'column_type' => 'decimal'],
            ], 'rows' => [['column_0' => '12.50']]],
        ];
        $payload = ['template_id' => $template->id, 'subject' => 'Memo only', 'action' => 'draft', 'inserted_items' => $items];
        $this->post(route('memos.store'), $payload)->assertSessionHasNoErrors();
        $memo = Memo::latest('id')->firstOrFail();
        $this->assertSame($items, $memo->inserted_items);
        $this->assertSame(0, $template->fields()->count());
        $this->assertSame(0, $template->tables()->count());
        $this->get(route('memos.create.template', $template))->assertOk()->assertDontSee('One time costs');
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('Special reference')->assertSee('12.50');
        $items[0]['value'] = 'Edited reference';
        $this->put(route('memos.update', $memo), array_merge($payload, ['inserted_items' => $items]))->assertSessionHasNoErrors();
        $this->assertSame($items, $memo->fresh()->inserted_items);
        $html = view('memos.pdf', ['memo' => $memo->fresh(), 'values' => collect(), 'approvals' => collect(), 'signatureImages' => []])->render();
        $this->assertStringContainsString('Edited reference', $html);
        $this->assertStringContainsString('12.50', $html);
        $this->post(route('memos.store'), ['template_id' => $template->id, 'subject' => 'Next memo', 'action' => 'draft'])->assertSessionHasNoErrors();
        $this->assertSame([], Memo::latest('id')->firstOrFail()->inserted_items);
    }

    public function test_table_names_are_optional_but_field_names_are_required(): void
    {
        $template = $this->template();
        $payload = ['template_id' => $template->id, 'subject' => 'Unnamed table', 'action' => 'draft',
            'inserted_items' => [['kind' => 'table', 'label' => '', 'columns' => [
                ['column_name' => 'column_0', 'column_label' => 'Amount', 'column_type' => 'decimal'],
            ], 'rows' => [['column_0' => '25.00']]]]];
        $this->post(route('memos.store'), $payload)->assertSessionHasNoErrors();
        $memo = Memo::latest('id')->firstOrFail();
        $this->get(route('memos.edit', $memo))->assertOk()->assertSee('25.00');
        $this->put(route('memos.update', $memo), $payload)->assertSessionHasNoErrors();
        $html = view('memos.inserted-items', ['memo' => $memo->fresh()])->render();
        $this->assertStringContainsString('25.00', $html);
        $this->assertStringNotContainsString('<h2>', $html);
        $payload['inserted_items'] = [['kind' => 'field', 'label' => '', 'type' => 'text']];
        $this->post(route('memos.store'), $payload)->assertSessionHasErrors('inserted_items.0.label');
    }

    public function test_invalid_insertions_are_rejected_without_changing_template(): void
    {
        $template = $this->template();
        $this->post(route('memos.store'), [
            'template_id' => $template->id, 'subject' => 'Invalid', 'action' => 'draft',
            'inserted_items' => [['kind' => 'table', 'label' => 'Invalid', 'columns' => []]],
        ])->assertSessionHasErrors('inserted_items.0.columns');
        $this->assertSame(0, Memo::count());
        $this->assertSame(0, $template->tables()->count());
        $this->postJson('/templates/'.$template->id.'/insert', ['kind' => 'field', 'label' => 'Old endpoint', 'type' => 'text'])->assertNotFound();
    }
}
