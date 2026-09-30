<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\MemoTemplate;
use App\Models\TemplateField;
use Illuminate\Database\Seeder;

class MemoTemplateSeeder extends Seeder
{
    public function run(): void
    {


        $it = Department::firstOrCreate(
            ['department_code' => 'IT'],
            [
                'department_name' => 'Information Technology',
                'description' => 'Information Technology Department',
                'status' => true,
            ]
        );

        $hr = Department::firstOrCreate(
            ['department_code' => 'HR'],
            [
                'department_name' => 'Human Resources',
                'description' => 'Human Resources Department',
                'status' => true,
            ]
        );

        $finance = Department::firstOrCreate(
            ['department_code' => 'FIN'],
            [
                'department_name' => 'Finance',
                'description' => 'Finance Department',
                'status' => true,
            ]
        );


        $template = MemoTemplate::create([
            'department_id' => $it->id,
            'template_name' => 'Awissawella Dialog',
            'template_code' => 'AWISSAWELLA-DIALOG',
            'description' => 'Awissawella Dialog Router Bill Payment.',
            'status' => true,
        ]);


       // Template Fields
       
        TemplateField::create([
            'template_id' => $template->id,
            'field_name' => 'employee_name',
            'field_label' => 'Employee Name',
            'field_type' => 'text',
            'placeholder' => 'Enter employee name',
            'is_required' => true,
            'is_active' => true,
            'field_order' => 1,
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_name' => 'employee_id',
            'field_label' => 'Employee ID',
            'field_type' => 'text',
            'placeholder' => 'Enter employee ID',
            'is_required' => true,
            'is_active' => true,
            'field_order' => 2,
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_name' => 'equipment_type',
            'field_label' => 'Equipment Type',
            'field_type' => 'select',
            'placeholder' => 'Select equipment',
            'options' => [
                'Laptop',
                'Desktop Computer',
                'Monitor',
                'Keyboard',
                'Mouse',
                'Printer',
                'Other',
            ],
            'is_required' => true,
            'is_active' => true,
            'field_order' => 3,
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_name' => 'quantity',
            'field_label' => 'Quantity',
            'field_type' => 'number',
            'placeholder' => 'Enter quantity',
            'is_required' => true,
            'is_active' => true,
            'field_order' => 4,
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_name' => 'reason',
            'field_label' => 'Reason for Request',
            'field_type' => 'textarea',
            'placeholder' => 'Explain the reason for this request',
            'is_required' => true,
            'is_active' => true,
            'field_order' => 5,
        ]);
    }
}