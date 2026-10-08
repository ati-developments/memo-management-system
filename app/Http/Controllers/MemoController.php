<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Memo;
use App\Models\MemoTemplate;
use App\Models\MemoApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemoController extends Controller
{
    public function create()
    {
        $departments = Department::where('status', true)
            ->orderBy('department_name')
            ->get();

        return view('memos.create', compact('departments'));
    }

    public function templates(Request $request)
    {
        $templates = MemoTemplate::where(
            'department_id',
            $request->department_id
        )
        ->where('status', true)
        ->orderBy('template_name')
        ->get();

        return response()->json($templates);
    }
    public function createFromTemplate(\App\Models\MemoTemplate $template, Request $request)
    {
        $template->load([
            'department',

            'fields' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('field_order');
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

        $approvalWorkflow = \App\Models\ApprovalWorkflow::with('steps.approver')
            ->where('template_id', $template->id)
            ->where('is_active', true)
            ->first();

        $memo = null;
        $values = collect();
        if ($request->filled('memo_id')) {
            $memo = Memo::with(['fieldValues', 'tableRows', 'attachments'])->findOrFail($request->integer('memo_id'));
            $isAdmin = in_array(strtolower((string) auth()->user()?->role?->role_name), ['admin', 'administrator'], true);
            abort_unless((int) $memo->template_id === (int) $template->id, 404);
            abort_unless($isAdmin || ((int) $memo->created_by === (int) auth()->id() && $memo->status === 'draft'), 403);
            $values = $memo->fieldValues->pluck('field_value', 'field_name');
        }

        $workflowUsers = \App\Models\User::orderBy('name')->get(['id', 'name', 'designation']);
        return view('memos.create', compact('template', 'approvalWorkflow', 'workflowUsers', 'memo', 'values'));
    }
    public function store(Request $request)
    {
        return $this->saveMemo($request);
    }

    public function saveMemo(Request $request, ?Memo $editingMemo = null)
    {
        $user = Auth::user();

        $template = MemoTemplate::with([
            'fields' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('field_order');
            },

            'tables' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('table_order');
            },

            'tables.columns' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('column_order');
            },
        ])->findOrFail($editingMemo?->template_id ?? $request->template_id);

        $rules = [
            'workflow_config' => ['sometimes', 'array:approval_type,approval_rule,minimum_approvals,steps'],
            'workflow_config.approval_type' => ['required_with:workflow_config', 'in:sequential,open'],
            'workflow_config.approval_rule' => ['required_with:workflow_config', 'in:all,any,minimum'],
            'workflow_config.minimum_approvals' => ['nullable', 'required_if:workflow_config.approval_rule,minimum', 'integer', 'min:1'],
            'workflow_config.steps' => ['required_with:workflow_config', 'array', 'min:1', 'max:50'],
            'workflow_config.steps.*' => ['array:approver_id'],
            'workflow_config.steps.*.approver_id' => ['required', 'integer', 'exists:users,id', 'distinct'],
            'table_formats' => ['nullable', 'array', 'max:5000'],
            'table_formats.*' => ['array:cell,bold,italic,underline'],
            'table_formats.*.cell' => ['required', 'string', 'max:255', 'regex:/^(tables|inserted_items)\[[a-zA-Z0-9_]+\]\[rows\]\[[0-9]+\]\[[a-zA-Z0-9_]+\]$/'],
            'table_formats.*.bold' => ['required', 'boolean'],
            'table_formats.*.italic' => ['required', 'boolean'],
            'table_formats.*.underline' => ['required', 'boolean'],
            'inserted_items' => ['nullable', 'array', 'max:100'],
            'inserted_items.*' => ['array:kind,label,type,value,columns,rows'],
            'inserted_items.*.kind' => ['required', 'in:field,table'],
            'inserted_items.*.label' => ['required_if:inserted_items.*.kind,field', 'nullable', 'string', 'max:255'],
            'inserted_items.*.type' => ['required_if:inserted_items.*.kind,field', 'in:text,textarea,number,date'],
            'inserted_items.*.value' => ['nullable', 'string', 'max:20000'],
            'inserted_items.*.columns' => ['required_if:inserted_items.*.kind,table', 'array', 'min:1', 'max:20'],
            'inserted_items.*.columns.*' => ['array:column_label,column_type,column_name'],
            'inserted_items.*.columns.*.column_label' => ['required', 'string', 'max:255'],
            'inserted_items.*.columns.*.column_name' => ['required', 'regex:/^column_[0-9]+$/'],
            'inserted_items.*.columns.*.column_type' => ['required', 'in:text,textarea,number,decimal,date'],
            'inserted_items.*.rows' => ['nullable', 'array', 'max:500'],
            'inserted_items.*.rows.*' => ['array'],
            'inserted_items.*.rows.*.*' => ['nullable', 'string', 'max:10000'],
            'subject' => ['required', 'string', 'max:255'],
            'to' => ['nullable', 'string', 'max:1000'],
            'from' => ['nullable', 'string', 'max:1000'],
            'through' => ['nullable', 'string', 'max:1000'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'action' => ['required', 'in:draft,submit'],
            'text_blocks' => ['nullable', 'array', 'max:100'],
            'text_blocks.*' => ['array:position,text,html'],
            'text_blocks.*.html' => ['nullable', 'string', 'max:50000'],
            'text_blocks.*.position' => ['required', \Illuminate\Validation\Rule::in(array_keys(\App\Support\MemoTextPositions::forTemplate($template)))],
            'text_blocks.*.text' => ['nullable', 'string', 'max:10000'],
            'tables' => ['nullable', 'array'],
            'tables.*' => ['array'],
            'tables.*.rows' => ['array'],
            'tables.*.rows.*' => ['array'],
            'tables.*.rows.*.*' => ['nullable', 'string', 'max:10000'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,png,jpg,jpeg'],
        ];
        foreach ($template->fields as $field) {
            if (!array_key_exists($field->field_name, $rules)) {
                $rules[$field->field_name] = ['nullable', 'string', 'max:20000'];
            }
        }
        $request->validate($rules);
        $workflowConfig = $request->input('workflow_config', $editingMemo?->workflow_config);
        if (!$workflowConfig) {
            $defaultWorkflow = \App\Models\ApprovalWorkflow::with('steps')->where('template_id', $template->id)->where('is_active', true)->first();
            if ($defaultWorkflow) {
                $workflowConfig = [
                    'approval_type' => $defaultWorkflow->approval_type,
                    'approval_rule' => $defaultWorkflow->approval_rule,
                    'minimum_approvals' => $defaultWorkflow->minimum_approvals,
                    'steps' => $defaultWorkflow->steps->map(fn ($step) => ['approver_id' => $step->approver_id])->all(),
                ];
            }
        }
        if ($workflowConfig && $workflowConfig['approval_rule'] === 'minimum'
            && (int) $workflowConfig['minimum_approvals'] > count($workflowConfig['steps'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['workflow_config.minimum_approvals' => 'Minimum approvals cannot exceed the number of approvers.']);
        }
        if ($workflowConfig && $workflowConfig['approval_rule'] !== 'minimum') $workflowConfig['minimum_approvals'] = null;
        if ($request->input('action') === 'submit' && !$workflowConfig) {
            throw \Illuminate\Validation\ValidationException::withMessages(['action' => 'Configure an active approval workflow before submitting this memo.']);
        }

        $action = $request->input('action', 'draft');

        /*
        |--------------------------------------------------------------------------
        | VALIDATE ACTION
        |--------------------------------------------------------------------------
        */

        if (!in_array($action, ['draft', 'submit'])) {
            return back()
                ->with('error', 'Invalid memo action.');
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE MEMO
        |--------------------------------------------------------------------------
        */

        $memo = DB::transaction(function () use (
            $request,
            $user,
            $template,
            $action,
            $workflowConfig,
            $editingMemo
        ) {

            /*
            |--------------------------------------------------------------------------
            | MEMO NUMBER
            |--------------------------------------------------------------------------
            */


            /*
            |--------------------------------------------------------------------------
            | MEMO STATUS
            |--------------------------------------------------------------------------
            */

            $status = $action === 'submit'
                ? 'pending'
                : 'draft';


            /*
            |--------------------------------------------------------------------------
            | CREATE MEMO
            |--------------------------------------------------------------------------
            */

            $attributes = [
                'template_id'   => $template->id,
                'department_id' => $user->department_id,
                'created_by'    => $user->id,
                'subject'       => $request->input('subject'),
                'status'        => $status,
                'creation_type' => 'template',
                'content'       => null,
                'inserted_items' => $request->input('inserted_items', $editingMemo?->inserted_items ?? []),
                'table_formats' => $request->input('table_formats', $editingMemo?->table_formats ?? []),
                'workflow_config' => $workflowConfig,
                'text_blocks'   => collect($request->input('text_blocks', []))->filter(fn ($block) => filled($block['text'] ?? null))->map(function ($block) {
                    if (isset($block['html'])) $block['html'] = \App\Support\MemoTextFormatting::sanitize($block['html']);
                    return $block;
                })->values()->all(),
            ];
            if ($editingMemo) {
                $memo = Memo::query()->lockForUpdate()->findOrFail($editingMemo->id);
                $isAdmin = in_array(strtolower((string) $user?->role?->role_name), ['admin', 'administrator'], true);
                abort_unless($isAdmin || (int) $memo->created_by === (int) $user->id, 403);
                abort_unless($isAdmin || $memo->status === 'draft', 403, 'Only draft memos can be edited.');
                if ($isAdmin && $memo->status !== 'draft') {
                    foreach ($memo->approvals()->whereNotNull('signature_path')->pluck('signature_path') as $signaturePath) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($signaturePath);
                    }
                    $memo->approvals()->delete();
                }
                $memo->update([
                    'subject' => $attributes['subject'],
                    'status' => $status,
                    'submitted_at' => null,
                    'completed_at' => null,
                    'text_blocks' => $attributes['text_blocks'],
                    'inserted_items' => $attributes['inserted_items'],
                    'table_formats' => $attributes['table_formats'],
                    'workflow_config' => $workflowConfig,
                ]);
                $memo->fieldValues()->whereIn('field_name', $template->fields->pluck('field_name')->merge(['to', 'from', 'through', 'date', 'subject']))->delete();
                $memo->tableRows()->whereIn('template_table_id', $template->tables->pluck('id'))->delete();
            } else {
                $date = now();
                $departmentCode = strtoupper(trim((string) ($user->department?->department_code ?: 'DEP')));
                // Allocate a daily sequence inside the transaction so generated
                // numbers remain unique while following the department/date format.
                DB::table('memo_number_sequences')->insertOrIgnore([
                    'date' => $date->toDateString(),
                    'last_number' => 0,
                ]);
                $sequence = DB::table('memo_number_sequences')
                    ->where('date', $date->toDateString())
                    ->lockForUpdate()
                    ->first();
                $number = $sequence->last_number + 1;
                DB::table('memo_number_sequences')
                    ->where('date', $date->toDateString())
                    ->update(['last_number' => $number]);
                $attributes['memo_number'] = 'MEMO/' . $departmentCode . '/' . $date->format('Y/m/d') . '-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
                $memo = Memo::create($attributes);
            }
            if ($action === 'submit') {
                $memo->update(['submitted_at' => now()]);
            }

            foreach (['to', 'from', 'through', 'date'] as $name) {
                if (!$template->fields->contains('field_name', $name)) {
                    $memo->fieldValues()->create([
                        'field_name' => $name,
                        'field_value' => $request->input($name),
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE TEMPLATE FIELD VALUES
            |--------------------------------------------------------------------------
            */

            foreach ($template->fields as $field) {

                /*
                * total_charges is calculated separately.
                */
                if ($field->field_name === 'total_charges') {
                    continue;
                }


                $value = $request->input($field->field_name);


                if ($value === null || $value === '') {
                    continue;
                }


                $memo->fieldValues()->create([
                    'template_field_id' => $field->id,
                    'field_name'        => $field->field_name,
                    'field_value'       => $value,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | TOTAL CHARGES
            |--------------------------------------------------------------------------
            */

            $voiceVpn = (float) (
                $request->input('voice_vpn') ?? 0
            );

            $governmentTaxes = (float) (
                $request->input('government_taxes_levies') ?? 0
            );

            $vat = (float) (
                $request->input('vat') ?? 0
            );


            $total = $voiceVpn
                + $governmentTaxes
                + $vat;


            $totalField = $template->fields
                ->firstWhere('field_name', 'total_charges');


            if ($totalField) {

                $memo->fieldValues()->create([
                    'template_field_id' => $totalField->id,
                    'field_name'        => 'total_charges',
                    'field_value'       => $total,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE DYNAMIC TABLE DATA
            |--------------------------------------------------------------------------
            */
            foreach ($request->input('tables', []) as $tableId => $tableData) {
                $table = $template->tables->firstWhere('id', (int) $tableId);

                if (!$table) {
                    continue;
                }

                $columnNames = $table->columns->pluck('column_name')->all();

                foreach ($tableData['rows'] ?? [] as $rowIndex => $rowData) {
                    $rowData = collect($rowData)
                        ->only($columnNames)
                        ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                        ->all();

                    if (collect($rowData)->filter(fn ($value) => $value !== null && $value !== '')->isEmpty()) {
                        continue;
                    }

                    $memo->tableRows()->create([
                        'template_table_id' => $table->id,
                        'row_data' => $rowData,
                        'row_order' => $rowIndex,
                    ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SUBMIT FOR APPROVAL
            |--------------------------------------------------------------------------
            */

            if ($action === 'submit') {

                /*
                * Find active approval workflow for this template.
                */

                // Keep this memo's workflow independent of future template edits.
                $workflow = \App\Models\ApprovalWorkflow::create([
                    'template_id' => $template->id,
                    'workflow_name' => 'Workflow for '.$memo->memo_number,
                    'approval_type' => $workflowConfig['approval_type'],
                    'approval_rule' => $workflowConfig['approval_rule'],
                    'minimum_approvals' => $workflowConfig['minimum_approvals'] ?? null,
                    'is_active' => false,
                ]);
                foreach (array_values($workflowConfig['steps']) as $index => $step) {
                    $workflow->steps()->create([
                        'approver_id' => $step['approver_id'],
                        'step_order' => $index + 1,
                        'is_required' => true,
                    ]);
                }
                $workflow->load('steps');
                // The submitter's saved signature is applied automatically at submission.
                $preparedSignature = $user->signature()->where('is_active', true)->first();
                $memo->approvals()->create([
                    'workflow_id'      => $workflow->id,
                    'approver_id'      => $user->id,
                    'approval_step_id' => null,
                    'approval_role'    => 'prepared',
                    'action'           => 'approved',
                    'comment'          => null,
                    'signature_path'   => $preparedSignature?->signature_path,
                    'approved_at'      => now(),
                    'approval_ip'      => request()->ip(),
                ]);


                /*
                |--------------------------------------------------------------------------
                | SEQUENTIAL APPROVAL
                |--------------------------------------------------------------------------
                */

                if ($workflow->approval_type === 'sequential') {

                    foreach ($workflow->steps as $step) {

                        $memo->approvals()->create([
                            'workflow_id'      => $workflow->id,
                            'approver_id'      => $step->approver_id,
                            'approval_step_id' => $step->id,
                            'approval_role'    => 'approval',
                            'action'           => 'pending',
                            'comment'          => null,
                            'signature_path'   => null,
                            'approved_at'      => null,
                            'approval_ip'      => null,
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | OPEN APPROVAL
                |--------------------------------------------------------------------------
                */

                elseif ($workflow->approval_type === 'open') {

                    foreach ($workflow->steps as $step) {

                        $memo->approvals()->create([
                            'workflow_id'      => $workflow->id,
                            'approver_id'      => $step->approver_id,
                            'approval_step_id' => $step->id,
                            'approval_role'    => 'approval',
                            'action'           => 'pending',
                            'comment'          => null,
                            'signature_path'   => null,
                            'approved_at'      => null,
                            'approval_ip'      => null,
                        ]);
                    }
                }
            }


            return $memo;
        });

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('memo-attachments/' . $memo->id, 'local');
            if (!$path) {
                throw new \RuntimeException('An attachment could not be saved. Please try again.');
            }

            $memo->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        if ($action === 'submit') {

            app(\App\Services\MemoApprovalNotifier::class)->notifyReadyApprovers($memo);

            return redirect()
                ->route('memos.my')
                ->with(
                    'success',
                    'Memo ' . $memo->memo_number . ' has been submitted for approval.'
                );
        }


        return redirect()
            ->route('memos.my')
            ->with(
                'success',
                'Memo ' . $memo->memo_number . ' saved as draft successfully.'
            );
    }
    public function myMemos(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower((string) $user?->role?->role_name), ['admin', 'administrator'], true);

        $baseQuery = Memo::query();
        if (!$isAdmin) {
            $baseQuery->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', fn ($approvalQuery) => $approvalQuery
                        ->where('approver_id', $user->id));
            });
        }

        $query = (clone $baseQuery)->with([
            'template',
            'fieldValues' => fn ($query) => $query->where('field_name', 'to'),
            'creator',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->input('search');

            $query->where(function ($q) use ($search) {

                $q->where('memo_number', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%');

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('status') &&
            $request->status !== 'all'
        ) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $counts = [

            'all' => (clone $baseQuery)->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'approved' => (clone $baseQuery)
                ->where('status', 'approved')
                ->count(),

            'draft' => (clone $baseQuery)
                ->where('status', 'draft')
                ->count(),

            'rejected' => (clone $baseQuery)
                ->where('status', 'rejected')
                ->count(),

        ];

        $memos = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();


        return view(
            'memos.my',
            compact(
                'memos',
                'counts',
                'isAdmin'
            )
        );
    }

    /** Display the organization-wide memo register. */
    public function allMemos(Request $request)
    {
        $query = Memo::query()
            ->with(['template', 'department', 'creator'])
            ->withCount([
                'approvals as approved_approvals_count' => fn ($query) => $query->where('action', 'approved'),
                'approvals as total_approvals_count',
            ]);

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($query) use ($search) {
                $query->where('memo_number', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhereHas('creator', fn ($query) => $query->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('department', fn ($query) => $query->where('department_name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $baseQuery = Memo::query();
        $counts = [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $memos = $query->latest('created_at')->paginate(12)->withQueryString();

        return view('memos.all', compact('memos', 'counts'));
    }


    public function approvals(Request $request)
    {
        $user = Auth::user();

        $query = Memo::with([
            'template',
            'department',
            'creator',
            'approvals.approver',
            'approvals.approvalStep',
            'approvals.workflow',
        ])
        ->whereHas('approvals', function ($query) use ($user) {
            $query->where('approver_id', $user->id)
                ->where('approval_role', 'approval');
        })
        ->where('status', '!=', 'approved');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('memo_number', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%');
            });
        }

        // Filter by approval action
        if (
            $request->filled('status') &&
            $request->status !== 'all'
        ) {
            $query->whereHas('approvals', function ($q) use ($user, $request) {
                $q->where('approver_id', $user->id)
                ->where('approval_role', 'approval')
                ->where('action', $request->status);
            });
        }

        // Counts for the approval tabs
        $baseQuery = Memo::where('status', '!=', 'approved')
            ->whereHas('approvals', fn ($query) => $query
                ->where('approver_id', $user->id)
                ->where('approval_role', 'approval'));

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->whereHas('approvals', fn ($query) => $query
                ->where('approver_id', $user->id)->where('approval_role', 'approval')->where('action', 'pending'))->count(),
            'approved' => (clone $baseQuery)->whereHas('approvals', fn ($query) => $query
                ->where('approver_id', $user->id)->where('approval_role', 'approval')->where('action', 'approved'))->count(),
            'rejected' => (clone $baseQuery)->whereHas('approvals', fn ($query) => $query
                ->where('approver_id', $user->id)->where('approval_role', 'approval')->where('action', 'rejected'))->count(),
        ];

        $memos = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view(
            'approvals.index',
            compact('memos', 'counts')
        );
    }

    public function reviewApproval(MemoApproval $approval)
    {
        $user = Auth::user();

        // Security check:
        // Only the assigned approver can access this approval.
        if ($approval->approver_id !== $user->id) {
            abort(403);
        }

        $approval->load([
            'memo.template',
            'memo.department',
            'memo.creator',
            'memo.fieldValues',
            'memo.tableRows.templateTable.columns',
            'memo.approvals.approver',
            'memo.approvals.approvalStep',
            'workflow',
            'approvalStep',
        ]);

        return view(
            'approvals.review',
            compact('approval')
        );
    }

    /** Record an assigned approver's decision and progress the workflow. */
    public function recordApprovalDecision(Request $request, MemoApproval $approval)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['decision'] === 'rejected' && blank($validated['comment'] ?? null)) {
            return back()
                ->withErrors(['comment' => 'Please provide a reason for rejecting this memo.'])
                ->withInput();
        }

        try {
            $message = DB::transaction(function () use ($approval, $request, $validated) {
                $lockedApproval = MemoApproval::query()
                    ->lockForUpdate()
                    ->with(['workflow', 'approvalStep', 'approver.signature'])
                    ->findOrFail($approval->id);

                if ($lockedApproval->approver_id !== Auth::id()) {
                    abort(403);
                }

                $memo = Memo::query()->lockForUpdate()->findOrFail($lockedApproval->memo_id);

                if ($memo->status !== 'pending' || $lockedApproval->action !== 'pending') {
                    throw new \RuntimeException('This approval has already been completed or is no longer available.');
                }

                $workflow = $lockedApproval->workflow;

                if (!$workflow) {
                    throw new \RuntimeException('The approval workflow for this memo is unavailable.');
                }

                $approvals = MemoApproval::query()
                    ->where('memo_id', $memo->id)
                    ->with('approvalStep')
                    ->lockForUpdate()
                    ->get()
                    ->sortBy(fn (MemoApproval $item) => $item->chain_order)
                    ->values();

                if ($workflow->approval_type === 'sequential') {
                    $currentApproval = $approvals->firstWhere('action', 'pending');

                    if (!$currentApproval || $currentApproval->id !== $lockedApproval->id) {
                        throw new \RuntimeException('This memo is waiting for an earlier approval step.');
                    }
                }

                $lockedApproval->update([
                    'action' => $validated['decision'],
                    'comment' => $validated['comment'] ?? null,
                    'signature_path' => $validated['decision'] === 'approved'
                        && $lockedApproval->approver?->signature?->is_active
                        ? $lockedApproval->approver->signature->signature_path
                        : null,
                    'approved_at' => now(),
                    'approval_ip' => $request->ip(),
                ]);

                if ($validated['decision'] === 'rejected') {
                    $memo->update(['status' => 'rejected', 'completed_at' => now()]);

                    return 'Memo ' . $memo->memo_number . ' has been rejected.';
                }

                $workflowApprovals = $approvals->where('approval_role', '!=', 'prepared');
                $workflowApprovedCount = $workflowApprovals->where('action', 'approved')->count()
                    + ($lockedApproval->approval_role === 'prepared' ? 0 : 1);
                $workflowComplete = match ($workflow->approval_rule) {
                    'any' => $workflowApprovedCount >= 1,
                    'minimum' => $workflowApprovedCount >= (int) $workflow->minimum_approvals,
                    default => $workflowApprovedCount >= $workflowApprovals->count(),
                };
                $isComplete = $workflowComplete;

                if ($isComplete) {
                    $memo->update(['status' => 'approved', 'completed_at' => now()]);

                    return 'Memo ' . $memo->memo_number . ' has been fully approved.';
                }

                if ($workflow->approval_type === 'sequential') {
                    app(\App\Services\MemoApprovalNotifier::class)->notifyReadyApprovers($memo);
                }

                return 'Your approval has been recorded. The memo is moving to the next approval step.';
            });
        } catch (\RuntimeException $exception) {
            return redirect()->route('approvals.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('approvals.index')->with('success', $message);
    }

    public function new()
    {
        $departments = Department::where('status', true)
            ->with(['memoTemplates' => function ($query) {
                $query->where('status', true)
                    ->orderBy('template_name');
            }])
            ->orderBy('department_name')
            ->get();

        return view('memos.new', compact('departments'));
    }
}
