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

    public function test_inserted_fields_and_tables_preserve_existing_items_and_save_with_memo(): void
    {
        $template = $this->template();
        $existing = $template->fields()->create(['field_name' => 'reference', 'field_label' => 'Reference', 'field_type' => 'text', 'field_order' => 1, 'is_active' => true]);
        $field = $this->postJson(route('templates.insert', $template), ['kind' => 'field', 'label' => 'Reference', 'type' => 'text'])
            ->assertCreated()->json('item');
        $this->assertNotSame($existing->field_name, $field['field_name']);
        $this->assertSame(2, $template->fields()->count());
        $table = $this->postJson(route('templates.insert', $template), ['kind' => 'table', 'label' => 'Costs', 'columns' => [
            ['column_label' => 'Amount', 'column_type' => 'decimal'],
            ['column_label' => 'Amount', 'column_type' => 'text'],
        ]])->assertCreated()->json('item');
        $this->assertSame(['amount', 'amount_2'], array_column($table['columns'], 'column_name'));
        $this->post(route('memos.store'), [
            'template_id' => $template->id, 'subject' => 'Toolbar memo', 'action' => 'draft',
            'reference' => 'Existing value', $field['field_name'] => 'New value',
            'tables' => [$table['id'] => ['rows' => [['amount' => '12.50', 'amount_2' => 'Notes']]]],
            'text_blocks' => [['position' => 'after_subject', 'text' => 'Memo only text']],
        ])->assertSessionHasNoErrors();
        $memo = Memo::latest('id')->firstOrFail();
        $this->assertSame('New value', $memo->fieldValues()->where('template_field_id', $field['id'])->value('field_value'));
        $this->assertSame('12.50', $memo->tableRows()->firstOrFail()->row_data['amount']);
        $this->get(route('memos.create.template', $template))->assertOk()->assertSee('memo-insert-bar')->assertSee('Costs');
    }

    public function test_invalid_insertions_do_not_change_template(): void
    {
        $template = $this->template();
        $this->postJson(route('templates.insert', $template), ['kind' => 'table', 'label' => 'Invalid', 'columns' => []])
            ->assertUnprocessable()->assertJsonValidationErrors('columns');
        $this->postJson(route('templates.insert', $template), ['kind' => 'field', 'label' => 'Invalid', 'type' => 'script'])
            ->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->assertSame(0, $template->fields()->count());
        $this->assertSame(0, $template->tables()->count());
        auth()->logout();
        $this->postJson(route('templates.insert', $template), ['kind' => 'field', 'label' => 'Unauthorized', 'type' => 'text'])
            ->assertUnauthorized();
    }
}
