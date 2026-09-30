<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\MemoTemplate;
use App\Models\TemplateField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function insert(Request $request, MemoTemplate $template)
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(['field', 'table'])],
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required_if:kind,field', Rule::in(['text', 'textarea', 'number', 'date'])],
            'columns' => ['required_if:kind,table', 'array', 'min:1', 'max:20'],
            'columns.*' => ['array:column_label,column_type'],
            'columns.*.column_label' => ['required', 'string', 'max:255'],
            'columns.*.column_type' => ['required', Rule::in(['text', 'textarea', 'number', 'decimal', 'date'])],
        ]);

        $item = DB::transaction(function () use ($template, $data) {
            MemoTemplate::whereKey($template->id)->lockForUpdate()->firstOrFail();
            if ($data['kind'] === 'field') {
                return $template->fields()->create([
                    'field_name' => 'custom_'.Str::uuid()->getHex(),
                    'field_label' => $data['label'], 'field_type' => $data['type'],
                    'is_required' => false, 'is_active' => true,
                    'field_order' => ($template->fields()->max('field_order') ?? 0) + 1,
                ]);
            }
            $table = $template->tables()->create([
                'table_name' => 'custom_'.Str::uuid()->getHex(), 'table_label' => $data['label'],
                'is_active' => true, 'table_order' => ($template->tables()->max('table_order') ?? 0) + 1,
            ]);
            foreach ($this->withNames($data['columns'], 'column_name', 'column_label', 'column') as $index => $column) {
                $table->columns()->create($column + [
                    'is_required' => false, 'is_active' => true, 'is_calculated' => false, 'column_order' => $index + 1,
                ]);
            }
            return $table->load('columns');
        });

        return response()->json(['kind' => $data['kind'], 'item' => $item], 201);
    }

    public function index()
    {
        $departments = Department::with([
            'memoTemplates' => function ($query) {
                $query->where('status', true)
                    ->withCount('fields')
                    ->orderBy('template_name');
            }
        ])
        ->where('status', true)
        ->orderBy('department_name')
        ->get();

        return view('templates.index', compact('departments'));
    }


    public function create()
    {
        $departments = Department::where('status', true)
            ->orderBy('department_name')
            ->get();

        return view('templates.create', compact('departments'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_name' => ['required', 'string', 'max:255'],
            'template_code' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ]);


        $template = DB::transaction(function () use ($validated) {

            return MemoTemplate::create([
                'department_id' => $validated['department_id'],
                'template_name' => $validated['template_name'],
                'template_code' => $validated['template_code'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'created_by' => Auth::id(),
            ]);

        });


        return redirect()
            ->route('templates.edit', $template->id)
            ->with('success', 'Template created successfully. Now configure the fields.');
    }


    public function edit(MemoTemplate $template)
    {
        $template->load([
            'department',

            'fields' => function ($query) {
                $query->orderBy('field_order');
            },

            'tables' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('table_order');
            },

            'tables.columns' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('column_order');
            },
        ]);

        $departments = Department::where('status', true)
            ->orderBy('department_name')
            ->get();

        return view('templates.edit', compact(
            'template',
            'departments'
        ));
    }

    public function storeFields(MemoTemplate $template, Request $request)
    {
        $validated = $request->validate([
            'fields' => ['nullable', 'array'],

            'fields.*.field_label' => [
                'required',
                'string',
                'max:255',
            ],

            'fields.*.field_name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],

            'fields.*.field_type' => [
                'required',
                Rule::in([
                    'text',
                    'textarea',
                    'number',
                    'date',
                    'datetime',
                    'email',
                    'select',
                    'radio',
                    'checkbox',
                    'file',
                    'richtext',
                ]),
            ],

            'fields.*.is_required' => [
                'nullable',
                'boolean',
            ],
            'allow_optional_text' => ['required', 'boolean'],
        ]);

        $validated['fields'] = $this->withNames($validated['fields'] ?? [], 'field_name', 'field_label', 'field');
        DB::transaction(function () use ($template, $validated) {
            $template->update(['allow_optional_text' => $validated['allow_optional_text']]);
            $kept = [];

            // Create the new fields
            foreach ($validated['fields'] ?? [] as $index => $field) {

                $saved = $template->fields()->updateOrCreate(['field_name' => $field['field_name']], [
                    'field_label' => $field['field_label'],
                    'field_type' => $field['field_type'],
                    'placeholder' => null,
                    'options' => null,
                    'is_required' => !empty($field['is_required']),
                    'is_active' => true,
                    'field_order' => $index + 1,
                ]);
                $kept[] = $saved->id;
            }
            $template->fields()->whereNotIn('id', $kept)->delete();
        });

        return redirect()
            ->route('templates.edit', $template->id)
            ->with('success', 'Template fields saved successfully.');
    }
    public function storeTables(MemoTemplate $template, Request $request)
{
    $validated = $request->validate([
        'tables' => ['nullable', 'array'],

        'tables.*.table_name' => [
            'nullable',
            'string',
            'max:255',
            'regex:/^[a-zA-Z0-9_]+$/',
        ],

        'tables.*.table_label' => [
            'nullable',
            'string',
            'max:255',
        ],

        'tables.*.columns' => [
            'nullable',
            'array',
        ],

        'tables.*.columns.*.column_name' => [
            'nullable',
            'string',
            'max:255',
            'regex:/^[a-zA-Z0-9_]+$/',
        ],

        'tables.*.columns.*.column_label' => [
            'required',
            'string',
            'max:255',
        ],

        'tables.*.columns.*.column_type' => [
            'required',
            Rule::in([
                'text',
                'number',
                'decimal',
                'date',
                'textarea',
            ]),
        ],

        'tables.*.columns.*.is_required' => [
            'nullable',
            'boolean',
        ],
    ]);

    $validated['tables'] = $this->withNames($validated['tables'] ?? [], 'table_name', 'table_label', 'table');
    foreach ($validated['tables'] as &$table) {
        $table['columns'] = $this->withNames($table['columns'] ?? [], 'column_name', 'column_label', 'column');
    }
    unset($table);
    DB::transaction(function () use ($template, $validated) {
        $keptTables = [];

        foreach ($validated['tables'] ?? [] as $tableIndex => $tableData) {

            $templateTable = $template->tables()->updateOrCreate(['table_name' => $tableData['table_name']], [
                'table_label' => $tableData['table_label'] ?? null,
                'is_active' => true,
                'table_order' => $tableIndex + 1,
            ]);

            $keptTables[] = $templateTable->id;
            $keptColumns = [];
            foreach ($tableData['columns'] ?? [] as $columnIndex => $columnData) {

                $saved = $templateTable->columns()->updateOrCreate(['column_name' => $columnData['column_name']], [
                    'column_label' => $columnData['column_label'],
                    'column_type' => $columnData['column_type'],
                    'placeholder' => null,
                    'is_required' => !empty($columnData['is_required']),
                    'is_calculated' => false,
                    'calculation' => null,
                    'column_order' => $columnIndex + 1,
                    'is_active' => true,
                ]);
                $keptColumns[] = $saved->id;
            }
            $templateTable->columns()->whereNotIn('id', $keptColumns)->delete();
        }
        $template->tables()->whereNotIn('id', $keptTables)->delete();
    });

    return redirect()
        ->route('templates.edit', $template->id)
        ->with('success', 'Template tables saved successfully.');
}

    private function withNames(array $items, string $key, string $label, string $fallback): array
    {
        // Keep existing keys stable when labels change.
        $used = array_filter(array_column($items, $key));
        foreach ($items as &$item) {
            if (!empty($item[$key])) {
                continue;
            }
            $base = substr(Str::slug($item[$label] ?? '', '_'), 0, 230) ?: $fallback;
            $name = $base;
            $suffix = 2;
            while (in_array($name, $used, true)) {
                $name = $base.'_'.$suffix++;
            }
            $item[$key] = $name;
            $used[] = $name;
        }
        unset($item);

        return array_values($items);
    }
}
