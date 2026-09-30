@extends('layouts.app')

@section('title', 'Approval Workflow')

@section('styles')
@include('templates.builder-styles')
<style>
    .workflow-page .template-info-card { display:flex; align-items:center; gap:16px; padding:20px 24px; margin-bottom:24px; border:1px solid #e1ded8; border-radius:12px; background:#fff; }
    .workflow-page .template-icon { display:grid; place-items:center; width:42px; height:48px; flex-shrink:0; border-radius:9px; color:#3156e8; background:#eef2ff; }
    .workflow-page .template-icon svg { width:24px; height:24px; }
    .workflow-page .template-label { margin-bottom:7px; font-size:10px; font-weight:600; letter-spacing:1px; color:#85838d; }
    .workflow-page .template-name { font-size:15px; font-weight:600; overflow-wrap:anywhere; }
    .workflow-page .template-department { margin-top:6px; color:#79756e; font-size:12px; }
    .workflow-page .workflow-card { background:#fff; border:1px solid #e1ded8; border-radius:14px; margin-bottom:24px; box-shadow:0 3px 12px #232f5a04; overflow:hidden; }
    .workflow-page .card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:18px; padding:25px 28px 20px; border-bottom:1px solid #eeece7; }
    .workflow-page .card-header h2 { margin:0 0 8px; font:400 21px Georgia,serif; }
    .workflow-page .card-header p { margin:0; color:#79756e; font-size:13px; line-height:1.6; }
    .workflow-page .card-body { padding:26px 28px; }
    .workflow-page .form-group { margin-bottom:22px; min-width:0; }
    .workflow-page .card-body > .form-group:last-child { margin-bottom:0; }
    .workflow-page .form-group > label { display:block; margin-bottom:8px; font-size:12px; font-weight:600; color:#494c58; }
    .workflow-page .required { color:#be5252; margin-left:3px; }
    .workflow-page .form-group small { display:block; margin-top:9px; color:#817d75; font-size:11px; line-height:1.7; max-width:520px; }
    .workflow-page .form-row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:22px; }
    .workflow-page .approvers-container { display:grid; gap:14px; }
    .workflow-page .approver-row { display:flex; align-items:flex-start; gap:16px; padding:20px; border:1px solid #e5e5eb; border-radius:10px; background:#fafbfe; }
    .workflow-page .step-number { display:grid; place-items:center; width:30px; height:30px; flex-shrink:0; border-radius:50%; background:#e8eeff; color:#3156e8; font-size:12px; font-weight:700; margin-top:25px; }
    .workflow-page .approver-fields { flex:1; min-width:0; }
    .workflow-page .approver-fields .form-group { margin-bottom:12px; }
    .workflow-page .required-checkbox { display:flex; align-items:center; gap:8px; font-size:12px; color:#626779; cursor:pointer; }
    .workflow-page .remove-approver { display:grid; place-items:center; width:40px; height:43px; flex-shrink:0; margin-top:22px; border:1px solid #f1dddd; border-radius:8px; background:#fff6f5; color:#be5252; font-size:20px; cursor:pointer; }
    .workflow-page .remove-approver:hover { background:#fee2e2; }
    .workflow-page .empty-approvers-message { padding:32px 20px; border:1px dashed #deddd8; border-radius:10px; color:#85818c; font-size:13px; line-height:1.8; text-align:center; }
    .workflow-page .workflow-info { display:flex; align-items:flex-start; gap:13px; padding:20px 24px; border:1px solid #dce4ff; border-radius:12px; background:#eef2ff; color:#42568f; }
    .workflow-page .info-icon { display:grid; place-items:center; width:24px; height:24px; flex-shrink:0; border-radius:50%; background:#dce5ff; color:#3156e8; font:italic 600 14px Georgia,serif; }
    .workflow-page .workflow-info strong { display:block; margin-bottom:7px; font-size:13px; }
    .workflow-page .workflow-info p { margin:0; font-size:12px; line-height:1.8; }
    .workflow-page .alert { display:flex; align-items:flex-start; gap:10px; }
    .workflow-page .alert ul { margin:8px 0 0; padding-left:20px; }
    @media(max-width:900px) { .workflow-page .form-row { grid-template-columns:1fr; gap:0; } }
    @media(max-width:520px) { .workflow-page .card-header,.workflow-page .card-body { padding:20px 14px; } .workflow-page .approver-row { padding:14px 10px; gap:9px; flex-wrap:wrap; } .workflow-page .approver-fields { flex-basis:calc(100% - 40px); } .workflow-page .remove-approver { margin:0 0 0 auto; } .workflow-page .template-info-card,.workflow-page .workflow-info { padding:18px 14px; } }
</style>
@endsection
@section('content')

<div class="template-editor workflow-page">
    @section('header-title')
Set up your approval workflow
@endsection
@section('header-description')
Choose who reviews your team's memos and how each approval moves forward.
@endsection
@section('header-eyebrow')
Template builder
@endsection
@section('header-back')
<a href="{{ route('templates.edit', $template->id) }}">&larr; Back to template configuration</a>
@endsection

    <div class="editor-body">
        <ol class="editor-steps" aria-label="Template setup">
            <li><span aria-label="Completed">&check;</span> Template details</li>
            <li><span>2</span> Fields &amp; tables</li>
            <li class="current" aria-current="step"><span>3</span> Approval workflow</li>
        </ol>
    @if(session('success'))

        <div class="alert alert-success" role="status">

            <span class="alert-icon">
                ✓
            </span>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =========================================================
         VALIDATION ERRORS
    ========================================================== --}}

    @if($errors->any())

        <div class="alert alert-danger" role="alert">

            <div>

                <strong>
                    Please correct the following:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
         TEMPLATE INFORMATION
    ========================================================== --}}

    <div class="template-info-card">

        <div class="template-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/></svg></div>

        <div>

            <div class="template-label">
                MEMO TEMPLATE
            </div>

            <div class="template-name">
                {{ $template->template_name }}
            </div>

            @if($template->department)

                <div class="template-department">
                    {{ $template->department->department_name }}
                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         WORKFLOW FORM
    ========================================================== --}}

    <form
        method="POST"
        action="{{ route(
            'templates.approval-workflow.update',
            $template->id
        ) }}"
        id="workflowForm"
    >

        @csrf


        {{-- =====================================================
             WORKFLOW SETTINGS
        ====================================================== --}}

        <div class="workflow-card">

            <div class="card-header">

                <div>

                    <h2>
                        <span class="section-number">01</span>Workflow settings
                    </h2>

                    <p>
                        Define how this memo should move through
                        the approval process.
                    </p>

                </div>

            </div>


            <div class="card-body">


                {{-- Workflow Name --}}

                <div class="form-group">

                    <label for="workflow_name">
                        Workflow Name
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="workflow_name"
                        name="workflow_name"
                        class="form-control"
                        value="{{ old(
                            'workflow_name',
                            $workflow->workflow_name
                                ?? $template->template_name . ' Approval'
                        ) }}"
                        placeholder="Example: Financial Request Approval"
                        required
                    >

                </div>


                {{-- Approval Type --}}

                <div class="form-row">

                    <div class="form-group">

                        <label for="approval_type">
                            Approval Type
                            <span class="required">*</span>
                        </label>

                        <select
                            id="approval_type"
                            name="approval_type"
                            class="form-control"
                            required
                        >

                            <option
                                value="sequential"
                                {{ old(
                                    'approval_type',
                                    $workflow->approval_type ?? 'sequential'
                                ) === 'sequential'
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                Sequential
                            </option>

                            <option
                                value="open"
                                {{ old(
                                    'approval_type',
                                    $workflow->approval_type ?? ''
                                ) === 'open'
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                Open
                            </option>

                        </select>

                        <small>
                            Sequential means approvers act in order.
                            Open means configured approvers can act
                            without waiting for a specific order.
                        </small>

                    </div>


                    {{-- Approval Rule --}}

                    <div class="form-group">

                        <label for="approval_rule">
                            Approval Rule
                            <span class="required">*</span>
                        </label>

                        <select
                            id="approval_rule"
                            name="approval_rule"
                            class="form-control"
                            required
                        >

                            <option
                                value="all"
                                {{ old(
                                    'approval_rule',
                                    $workflow->approval_rule ?? 'all'
                                ) === 'all'
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                All Approvers
                            </option>

                            <option
                                value="any"
                                {{ old(
                                    'approval_rule',
                                    $workflow->approval_rule ?? ''
                                ) === 'any'
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                Any One Approver
                            </option>

                            <option
                                value="minimum"
                                {{ old(
                                    'approval_rule',
                                    $workflow->approval_rule ?? ''
                                ) === 'minimum'
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                Minimum Number of Approvals
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Minimum Approvals --}}

                <div
                    class="form-group minimum-approvals-group"
                    id="minimumApprovalsGroup"
                >

                    <label for="minimum_approvals">
                        Minimum Approvals
                    </label>

                    <input
                        type="number"
                        id="minimum_approvals"
                        name="minimum_approvals"
                        class="form-control"
                        min="1"
                        value="{{ old(
                            'minimum_approvals',
                            $workflow->minimum_approvals ?? ''
                        ) }}"
                        placeholder="Example: 2"
                    >

                    <small>
                        The memo will be considered approved after
                        this number of approvers approve it.
                    </small>

                </div>

            </div>

        </div>


        {{-- =====================================================
             APPROVERS
        ====================================================== --}}

        <div class="workflow-card approvers-card">

            <div class="card-header">

                <div>

                    <h2>
                        <span class="section-number">02</span>Approval steps
                    </h2>

                    <p>
                        Select the users who will approve this memo.
                    </p>

                </div>

                <button
                    type="button"
                    class="add-field-btn"
                    id="addApproverButton"
                >
                    + Add Approver
                </button>

            </div>


            <div class="card-body">

                <div
                    id="approversContainer"
                    class="approvers-container"
                >

                    @php

                        $existingSteps = [];

                        if ($workflow) {
                            $existingSteps = $workflow->steps
                                ->sortBy('step_order')
                                ->values()
                                ->all();
                        }

                    @endphp


                    @if(count($existingSteps) > 0)

                        @foreach($existingSteps as $index => $step)

                            <div
                                class="approver-row"
                                data-index="{{ $index }}"
                            >

                                <div class="step-number">
                                    {{ $index + 1 }}
                                </div>


                                <div class="approver-fields">

                                    <div class="form-group">

                                        <label>
                                            Approver
                                            <span class="required">*</span>
                                        </label>

                                        <select
                                            name="steps[{{ $index }}][approver_id]"
                                            class="form-control"
                                            required
                                        >

                                            <option value="">
                                                Select Approver
                                            </option>

                                            @foreach($users as $user)

                                                <option
                                                    value="{{ $user->id }}"
                                                    {{ $step->approver_id == $user->id
                                                        ? 'selected'
                                                        : ''
                                                    }}
                                                >
                                                    {{ $user->name }}
                                                    @if($user->designation)
                                                        — {{ $user->designation }}
                                                    @endif
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    <label class="required-checkbox">

                                        <input
                                            type="checkbox"
                                            name="steps[{{ $index }}][is_required]"
                                            value="1"
                                            {{ $step->is_required
                                                ? 'checked'
                                                : ''
                                            }}
                                        >

                                        Required approval

                                    </label>

                                </div>


                                <button
                                    type="button"
                                    class="remove-approver"
                                    onclick="removeApprover(this)"
                                    title="Remove approver" aria-label="Remove approver"
                                >
                                    &times;
                                </button>

                            </div>

                        @endforeach


                    @else

                        {{-- Initial approver row --}}

                        <div
                            class="approver-row"
                            data-index="0"
                        >

                            <div class="step-number">
                                1
                            </div>


                            <div class="approver-fields">

                                <div class="form-group">

                                    <label>
                                        Approver
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        name="steps[0][approver_id]"
                                        class="form-control"
                                        required
                                    >

                                        <option value="">
                                            Select Approver
                                        </option>

                                        @foreach($users as $user)

                                            <option
                                                value="{{ $user->id }}"
                                            >
                                                {{ $user->name }}

                                                @if($user->designation)

                                                    —
                                                    {{ $user->designation }}

                                                @endif

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <label class="required-checkbox">

                                    <input
                                        type="checkbox"
                                        name="steps[0][is_required]"
                                        value="1"
                                        checked
                                    >

                                    Required approval

                                </label>

                            </div>


                            <button
                                type="button"
                                class="remove-approver"
                                onclick="removeApprover(this)"
                                title="Remove approver" aria-label="Remove approver"
                            >
                                &times;
                            </button>

                        </div>

                    @endif

                </div>


                <div
                    class="empty-approvers-message"
                    id="emptyApproversMessage"
                    style="display:none;"
                >

                    No approvers have been added.

                    Click
                    <strong>+ Add Approver</strong>
                    to add one.

                </div>

            </div>

        </div>


        {{-- =====================================================
             INFORMATION BOX
        ====================================================== --}}

        <div class="workflow-info">

            <div class="info-icon" aria-hidden="true">i</div>

            <div>

                <strong>
                    How this workflow works
                </strong>

                <p id="workflowExplanation" aria-live="polite">
                    All selected approvers must approve the memo.
                    For sequential workflows, they will approve
                    according to the order shown above.
                </p>

            </div>

        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}

        <div class="form-actions">

            <a
                href="{{ route('templates.edit', $template->id) }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Approval Workflow
            </button>

        </div>


    </form>
    </div>
</div>


{{-- =============================================================
     JAVASCRIPT
============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const approvalType =
        document.getElementById('approval_type');

    const approvalRule =
        document.getElementById('approval_rule');

    const minimumGroup =
        document.getElementById('minimumApprovalsGroup');

    const minimumInput =
        document.getElementById('minimum_approvals');

    const explanation =
        document.getElementById('workflowExplanation');

    const container =
        document.getElementById('approversContainer');

    const addButton =
        document.getElementById('addApproverButton');

    const emptyMessage =
        document.getElementById('emptyApproversMessage');

    let approverIndex =
        container.querySelectorAll('.approver-row').length;


    /* =========================================================
       USERS
    ========================================================== */

    const users = @json($usersJson);


    /* =========================================================
       MINIMUM APPROVAL VISIBILITY
    ========================================================== */

    function updateMinimumVisibility() {

        if (
            approvalRule.value === 'minimum'
        ) {

            minimumGroup.style.display = 'block';

            minimumInput.required = true;

        } else {

            minimumGroup.style.display = 'none';

            minimumInput.required = false;

            minimumInput.value = '';

        }

    }


    /* =========================================================
       WORKFLOW EXPLANATION
    ========================================================== */

    function updateExplanation() {

        const type =
            approvalType.value;

        const rule =
            approvalRule.value;


        let text = '';


        if (type === 'sequential') {

            if (rule === 'all') {

                text =
                    'All selected approvers must approve the memo. ' +
                    'They will approve according to the order shown above.';

            }

            else if (rule === 'any') {

                text =
                    'Any one of the configured approvers can approve ' +
                    'the memo.';

            }

            else if (rule === 'minimum') {

                text =
                    'The memo will be approved after the configured ' +
                    'minimum number of approvers approve it.';

            }

        } else {

            if (rule === 'all') {

                text =
                    'All configured approvers can review the memo ' +
                    'without waiting for a specific sequence.';

            }

            else if (rule === 'any') {

                text =
                    'Any one of the configured approvers can approve ' +
                    'the memo.';

            }

            else if (rule === 'minimum') {

                text =
                    'Any configured approvers can approve the memo. ' +
                    'The memo will be approved once the minimum number ' +
                    'of approvals is reached.';

            }

        }


        explanation.textContent = text;

    }


    /* =========================================================
       UPDATE STEP NUMBERS
    ========================================================== */

    function updateStepNumbers() {

        const rows =
            container.querySelectorAll('.approver-row');


        rows.forEach(function (row, index) {

            const number =
                row.querySelector('.step-number');

            if (number) {

                number.textContent =
                    index + 1;

            }

        });


        if (rows.length === 0) {

            emptyMessage.style.display = 'block';

        } else {

            emptyMessage.style.display = 'none';

        }

    }


    /* =========================================================
       CREATE USER OPTIONS
    ========================================================== */

    function userOptions() {

        let options =
            '<option value="">Select Approver</option>';


        users.forEach(function (user) {

            options += `
                <option value="${user.id}">
                    ${escapeHtml(user.name)}
                    ${user.designation
                        ? ' — ' + escapeHtml(user.designation)
                        : ''
                    }
                </option>
            `;

        });


        return options;

    }


    /* =========================================================
       ADD APPROVER
    ========================================================== */

    function addApprover() {

        const index =
            approverIndex++;

        const row =
            document.createElement('div');

        row.className =
            'approver-row';

        row.dataset.index =
            index;


        row.innerHTML = `

            <div class="step-number">
                1
            </div>

            <div class="approver-fields">

                <div class="form-group">

                    <label>
                        Approver
                        <span class="required">*</span>
                    </label>

                    <select
                        name="steps[${index}][approver_id]"
                        class="form-control"
                        required
                    >

                        ${userOptions()}

                    </select>

                </div>

                <label class="required-checkbox">

                    <input
                        type="checkbox"
                        name="steps[${index}][is_required]"
                        value="1"
                        checked
                    >

                    Required approval

                </label>

            </div>

            <button
                type="button"
                class="remove-approver"
                title="Remove approver" aria-label="Remove approver"
            >
                &times;
            </button>

        `;


        row
            .querySelector('.remove-approver')
            .addEventListener(
                'click',
                function () {

                    removeApprover(this);

                }
            );


        container.appendChild(row);

        updateStepNumbers();

    }


    /* =========================================================
       REMOVE APPROVER
    ========================================================== */

    window.removeApprover = function (button) {

        const row =
            button.closest('.approver-row');

        if (!row) {
            return;
        }


        row.remove();

        updateStepNumbers();

    };


    /* =========================================================
       ESCAPE HTML
    ========================================================== */

    function escapeHtml(value) {

        if (!value) {
            return '';
        }


        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /* =========================================================
       EVENTS
    ========================================================== */

    approvalType.addEventListener(
        'change',
        updateExplanation
    );


    approvalRule.addEventListener(
        'change',
        function () {

            updateMinimumVisibility();

            updateExplanation();

        }
    );


    addButton.addEventListener(
        'click',
        addApprover
    );


    /* =========================================================
       INITIAL STATE
    ========================================================== */

    updateMinimumVisibility();

    updateExplanation();

    updateStepNumbers();

});

</script>

@endsection