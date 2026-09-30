<?php

namespace Tests\Feature;

use App\Models\{Department, MemoTemplate, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_labels_generate_unique_keys_and_existing_entries_survive_edits(): void
    {
        $department = Department::create(['department_name' => 'IT', 'department_code' => 'IT', 'status' => true]);
        $this->actingAs(User::factory()->create(['username' => 'builder', 'department_id' => $department->id]));
        $template = MemoTemplate::create(['department_id' => $department->id, 'template_name' => 'Payment', 'template_code' => 'PAY', 'status' => true]);
        $this->post(route('templates.fields.store', $template), ['allow_optional_text' => false, 'fields' => [
            ['field_label' => 'Bill Period', 'field_type' => 'text'],
            ['field_label' => 'Bill Period', 'field_type' => 'text'],
        ]])->assertSessionHasNoErrors();
        $this->assertSame(['bill_period', 'bill_period_2'], $template->fields()->pluck('field_name')->all());
        $field = $template->fields()->first();
        $this->post(route('templates.fields.store', $template), ['allow_optional_text' => false, 'fields' => [
            ['field_name' => $field->field_name, 'field_label' => 'Period', 'field_type' => 'text'],
        ]])->assertSessionHasNoErrors();
        $this->assertSame($field->id, $template->fields()->first()->id);
        $tables = [
            ['table_label' => '', 'columns' => [
                ['column_label' => 'Amount', 'column_type' => 'decimal'],
                ['column_label' => 'Amount', 'column_type' => 'decimal'],
            ]],
            ['table_label' => '', 'columns' => []],
        ];
        $this->post(route('templates.tables.store', $template), ['tables' => $tables])->assertSessionHasNoErrors();
        $this->assertSame(['table', 'table_2'], $template->tables()->pluck('table_name')->all());
        $table = $template->tables()->first();
        $this->assertNull($table->table_label);
        $this->assertSame(['amount', 'amount_2'], $table->columns()->pluck('column_name')->all());
        $column = $table->columns()->first();
        $tables[0]['table_name'] = $table->table_name;
        $tables[0]['columns'][0]['column_name'] = $column->column_name;
        $tables[0]['columns'][0]['column_label'] = 'Total Amount';
        $this->post(route('templates.tables.store', $template), ['tables' => $tables])->assertSessionHasNoErrors();
        $this->assertSame($table->id, $template->tables()->first()->id);
        $this->assertSame($column->id, $table->columns()->first()->id);
        $this->get(route('templates.edit', $template))->assertOk()->assertSee('Table title (optional)')->assertDontSee('<label>Field Name</label>', false);
    }
}
