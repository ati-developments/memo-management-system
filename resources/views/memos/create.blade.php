@extends('layouts.app')

@section('title', isset($memo) && $memo ? 'Update Memo' : 'Create Memo')

@section('content')

<div class="create-memo-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    @section('header-title')
{{ isset($memo) && $memo ? 'Update ' . $memo->memo_number : $template->template_name }}
@endsection
@section('header-description')
{{ isset($memo) && $memo ? 'Update this memo using its original template.' : 'Create a new memo using this template.' }}
@endsection
@section('header-eyebrow')
Memo builder
@endsection
@section('header-back')
<x-back-link :fallback="route('memos.new')" />
@endsection
@section('header-actions')
@if(!isset($memo) || !$memo)<a href="{{ route('templates.edit', $template->id) }}" class="app-header-button secondary">Edit template</a>@endif
@endsection


    
    {{-- =========================================================
         MAIN BUILDER
    ========================================================== --}}

    @include('memos.insert-toolbar')

    <div class="memo-builder">


        {{-- =====================================================
             LEFT SIDE - FORM
        ====================================================== --}}

        <div class="memo-form-section">

            <div class="form-card">


                {{-- =================================================
                     MEMO FORM
                ================================================== --}}

                <form
                    id="memoForm"
                    method="POST"
                    enctype="multipart/form-data"
                    action="{{ isset($memo) && $memo ? route('memos.update', $memo) : route('memos.store') }}"
                >

                    @csrf
                    @if(isset($memo) && $memo) @method('PUT') @endif


                    <input
                        type="hidden"
                        name="template_id"
                        value="{{ $template->id }}"
                    >

                    <input id="memo-attachments" type="file" name="attachments[]" multiple hidden
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.png,.jpg,.jpeg">
                    <ul id="memo-attachment-list" aria-label="Selected attachments"></ul>
                    @if(isset($memo) && $memo && $memo->attachments->isNotEmpty())
                        <p>Current attachments:</p><ul>@foreach($memo->attachments as $attachment)<li><a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}">{{ $attachment->original_name }}</a></li>@endforeach</ul>
                    @endif


                    <div class="form-section">

                        <h3>
                            Memo Details
                        </h3>


                        {{-- =========================================================
                             FIXED MEMO FIELDS
                             These fields exist in every memo template.
                        ========================================================== --}}

                        <div class="form-group">

                            <label for="to">
                                To
                            </label>

                            <textarea
                                id="to"
                                name="to"
                                class="form-control"
                                rows="2"
                                maxlength="1000"
                                aria-describedby="to-help"
                                placeholder="Enter one recipient per line"
                            >{{ old('to', $values->get('to')) }}</textarea>
                           

                        </div>


                        <div class="form-group">

                            <label for="from">
                                From
                            </label>
 
                            <input
                                type="text"
                                id="from"
                                name="from"
                                class="form-control"
                                value="{{ old('from', $values->get('from', auth()->user()->name)) }}"
                                readonly
                            >

                        </div>


                        <div class="form-group">

                            <label for="through">
                                Through
                            </label>

                            <input
                                type="text"
                                id="through"
                                name="through"
                                value="{{ old('through', $values->get('through')) }}"
                                class="form-control"
                                placeholder="Enter through"
                            >

                        </div>


                        <div class="form-group">

                            <label for="subject">
                                Subject
                            </label>

                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                class="form-control"
                                value="{{ old('subject', $memo->subject ?? '') }}"
                                placeholder="Enter subject"
                            >

                        </div>


                        <div class="form-group">

                            <label for="date">
                                Date
                            </label>

                            <input
                                type="date"
                                id="date"
                                name="date"
                                class="form-control"
                                value="{{ old('date', $values->get('date', $memo?->created_at?->format('Y-m-d') ?? date('Y-m-d'))) }}"
                                readonly
                            >

                        </div>



                        {{-- =========================================================
                             ADDITIONAL TEMPLATE FIELDS
                             These come from Template Edit.
                        ========================================================== --}}

                        @foreach($template->fields as $field)

                            {{-- Do not duplicate fixed memo fields --}}
                            @if(in_array(
                                strtolower($field->field_name),
                                ['to', 'from', 'through', 'subject', 'date']
                            ))

                                @continue

                            @endif


                            {{-- =====================================
                                 TOTAL CHARGES
                            ====================================== --}}

                            @if($field->field_name === 'total_charges')

                                <div class="form-group">

                                    <label
                                        for="total_charges"
                                    >
                                        {{ $field->field_label }}
                                    </label>


                                    <input
                                        type="number"
                                        id="total_charges"
                                        name="total_charges"
                                        class="form-control calculated-field"
                                        step="0.01"
                                        value="{{ old($field->field_name, $values->get($field->field_name, '0.00')) }}"
                                        readonly
                                    >


                                    <small>
                                        Automatically calculated from
                                        Voice / VPN, Government taxes &
                                        Levies, and VAT.
                                    </small>

                                </div>

                                @continue

                            @endif



                            {{-- =====================================
                                 NORMAL FIELD
                            ====================================== --}}

                            <div class="form-group">

                                <label
                                    for="{{ $field->field_name }}"
                                >

                                    {{ $field->field_label }}


                                    @if($field->is_required)

                                        <span class="required">
                                            *
                                        </span>

                                    @endif

                                </label>



                                {{-- TEXTAREA --}}
                                @if($field->field_type === 'textarea')

                                    <textarea
                                        id="{{ $field->field_name }}"
                                        name="{{ $field->field_name }}"
                                        class="form-control"
                                        rows="4"
                                        placeholder="{{ $field->placeholder }}"
                                        @if($field->is_required)
                                            required
                                        @endif
                                    >{{ old($field->field_name, $values->get($field->field_name)) }}</textarea>



                                {{-- DATE --}}
                                @elseif($field->field_type === 'date')

                                    <input
                                        type="date"
                                        id="{{ $field->field_name }}"
                                        name="{{ $field->field_name }}"
                                        class="form-control"
                                        value="{{ old($field->field_name, $values->get($field->field_name, now()->format('Y-m-d'))) }}"
                                        @if($field->is_required)
                                            required
                                        @endif
                                    >



                                {{-- NUMBER --}}
                                @elseif($field->field_type === 'number')

                                    <input
                                        type="number"
                                        id="{{ $field->field_name }}"
                                        name="{{ $field->field_name }}"
                                        class="form-control"
                                        step="0.01"
                                        value="{{ old($field->field_name, $values->get($field->field_name)) }}"
                                        placeholder="{{ $field->placeholder }}"
                                        @if($field->is_required)
                                            required
                                        @endif
                                    >



                                {{-- SELECT --}}
                                @elseif($field->field_type === 'select')

                                    <select
                                        id="{{ $field->field_name }}"
                                        name="{{ $field->field_name }}"
                                        class="form-control"
                                        @if($field->is_required)
                                            required
                                        @endif
                                    >

                                        <option value="">
                                            Select {{ $field->field_label }}
                                        </option>


                                        @if($field->options)

                                            @foreach($field->options as $option)

                                                <option value="{{ $option }}" @selected(old($field->field_name, $values->get($field->field_name)) == $option)>
                                                    {{ $option }}
                                                </option>

                                            @endforeach

                                        @endif

                                    </select>



                                {{-- DEFAULT TEXT --}}
                                @else

                                    <input
                                        type="text"
                                        id="{{ $field->field_name }}"
                                        name="{{ $field->field_name }}"
                                        class="form-control"
                                        value="{{ old($field->field_name, $values->get($field->field_name)) }}"
                                        placeholder="{{ $field->placeholder }}"
                                        @if($field->is_required)
                                            required
                                        @endif
                                    >

                                @endif

                            </div>

                        @endforeach



                        {{-- =================================================
                             DYNAMIC TABLES
                        ================================================== --}}

                        <div id="inserted-memo-fields"></div>
                        @foreach($template->tables as $table)

                            <div class="memo-table-form-section">

                                @if($table->table_label)

                                    <h4 class="memo-table-title">
                                        {{ $table->table_label }}
                                    </h4>

                                @endif


                                <div class="memo-table-wrapper">

                                    <table class="memo-entry-table">

                                        <thead>

                                            <tr class="table-input-template-row">

                                                @foreach($table->columns as $column)

                                                    <th>

                                                        {{ $column->column_label }}

                                                        @if($column->is_required)

                                                            <span class="required">
                                                                *
                                                            </span>

                                                        @endif

                                                    </th>

                                                @endforeach

                                            </tr>

                                        </thead>


                                        <tbody
                                            id="table-body-{{ $table->id }}"
                                        >
                                            @php
                                                $tableRows = old('tables.'.$table->id.'.rows', isset($memo) && $memo ? $memo->tableRows->where('template_table_id', $table->id)->pluck('row_data', 'row_order')->all() : []);
                                                if (!$tableRows) $tableRows = [[]];
                                            @endphp
                                            @foreach($tableRows as $rowIndex => $rowData)
                                            <tr>

                                                @foreach($table->columns as $column)

                                                    <td>

                                                        @if($column->column_type === 'textarea')

                                                            <textarea 
                                                                name="tables[{{ $table->id }}][rows][{{ $rowIndex }}][{{ $column->column_name }}]" 
                                                                class="form-control table-input"
                                                                data-table-id="{{ $table->id }}"
                                                                data-row-index="{{ $rowIndex }}"
                                                                data-column-name="{{ $column->column_name }}"
                                                                rows="2" 
                                                                placeholder="{{ $column->placeholder }}" 
                                                                @if($column->is_required) 
                                                                    required 
                                                                @endif 
                                                            >{{ $rowData[$column->column_name] ?? '' }}</textarea>


                                                        @elseif($column->column_type === 'date')

                                                            <input 
                                                                type="date"
                                                                name="tables[{{ $table->id }}][rows][{{ $rowIndex }}][{{ $column->column_name }}]" 
                                                                class="form-control table-input"
                                                                data-table-id="{{ $table->id }}"
                                                                data-row-index="{{ $rowIndex }}"
                                                                data-column-name="{{ $column->column_name }}"
                                                                @if($column->is_required) 
                                                                    required 
                                                                @endif 
                                                                value="{{ $rowData[$column->column_name] ?? '' }}"
                                                            >


                                                        @elseif($column->column_type === 'number')

                                                            <input 
                                                                type="number"
                                                                name="tables[{{ $table->id }}][rows][{{ $rowIndex }}][{{ $column->column_name }}]" 
                                                                class="form-control table-input"
                                                                data-table-id="{{ $table->id }}"
                                                                data-row-index="{{ $rowIndex }}"
                                                                data-column-name="{{ $column->column_name }}"
                                                                step="1"
                                                                placeholder="{{ $column->placeholder }}" 
                                                                value="{{ $rowData[$column->column_name] ?? '' }}"
                                                                @if($column->is_required) 
                                                                    required 
                                                                @endif 
                                                            >


                                                        @elseif($column->column_type === 'decimal')

                                                            <input 
                                                                type="number"
                                                                name="tables[{{ $table->id }}][rows][{{ $rowIndex }}][{{ $column->column_name }}]" 
                                                                class="form-control table-input"
                                                                data-table-id="{{ $table->id }}"
                                                                data-row-index="{{ $rowIndex }}"
                                                                data-column-name="{{ $column->column_name }}"
                                                                step="0.01"
                                                                placeholder="{{ $column->placeholder }}" 
                                                                value="{{ $rowData[$column->column_name] ?? '' }}"
                                                                @if($column->is_required) 
                                                                    required 
                                                                @endif 
                                                            >


                                                        @else

                                                            <input 
                                                                type="text"
                                                                name="tables[{{ $table->id }}][rows][{{ $rowIndex }}][{{ $column->column_name }}]" 
                                                                class="form-control table-input"
                                                                data-table-id="{{ $table->id }}"
                                                                data-row-index="{{ $rowIndex }}"
                                                                data-column-name="{{ $column->column_name }}"
                                                                placeholder="{{ $column->placeholder }}" 
                                                                value="{{ $rowData[$column->column_name] ?? '' }}"
                                                                @if($column->is_required) 
                                                                    required 
                                                                @endif 
                                                            >

                                                        @endif

                                                    </td>

                                                @endforeach

                                            </tr>
                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>


                                <button
                                    type="button"
                                    class="btn btn-secondary add-table-row"
                                    onclick="addTableRow({{ $table->id }})"
                                >
                                    + Add Row
                                </button>

                            </div>

                        @endforeach

                        <div id="inserted-memo-tables"></div>

                    </div>







                    {{-- =================================================
                         ACTION BUTTONS
                    ================================================== --}}

                    @include('memos.text-block-editor', ['compact' => true])
                    @include('memos.workflow-editor')

                    <div class="form-actions">

                        <button
                            type="submit"
                            name="action"
                            value="draft"
                            class="btn btn-secondary"
                        >
                            {{ isset($memo) && $memo ? 'Update' : 'Save ' . ($memoStatusLabels['draft'] ?? 'Draft') }}
                        </button>


                        <button
                            type="submit"
                            name="action"
                            value="submit"
                            class="btn btn-primary"
                        >
                            {{ isset($memo) && $memo ? 'Update and Submit for Approval' : 'Submit for Approval' }}
                        </button>

                    </div>


                </form>

            </div>

        </div>



        {{-- =========================================================
             RIGHT SIDE - DOCUMENT PREVIEW
        ========================================================== --}}

        <div class="preview-section">

            <div class="preview-card">


                {{-- Preview header --}}
                <div class="preview-header">

                    <div>

                        <h2>
                            Document Preview
                        </h2>

                        <span>
                            Updates as you write
                        </span>

                    </div>


                    <span class="preview-status">
                        {{ $memoStatusLabels['draft'] ?? 'Draft' }}
                    </span>

                </div>



                {{-- =================================================
                     DOCUMENT
                ================================================== --}}

                <div
                    class="document-preview"
                    id="documentPreview"
                >


                    {{-- =============================================
                         DOCUMENT TITLE
                    ============================================== --}}

                    <div class="document-title">

                        <div class="document-brand"><img src="{{ asset('images/Amana-Takaful-logo.jpeg') }}" alt="Amana Takaful Insurance" style="width:187px;max-width:100%;height:auto"></div>

                        <h1>
                            MEMO
                        </h1>

                        <div class="department-name">
                            {{ $template->department->department_name }}
                        </div>

                    </div>



                    {{-- =========================================================
                         FIXED MEMO FIELDS
                         These are present in every template.
                    ========================================================== --}}

                    @include('memos.text-blocks', ['position' => 'start'])
                    <div class="memo-details">


                        <div class="memo-detail-row">

                            <div class="memo-label">
                                To
                            </div>

                            <div
                                class="memo-value"
                                id="preview_to"
                            >
                                —
                            </div>

                        </div>


                        <div class="memo-detail-row">

                            <div class="memo-label">
                                From
                            </div>

                            <div
                                class="memo-value"
                                id="preview_from"
                            >
                                {{ auth()->user()->name }}
                            </div>

                        </div>


                        <div class="memo-detail-row">

                            <div class="memo-label">
                                Through
                            </div>

                            <div
                                class="memo-value"
                                id="preview_through"
                            >
                                —
                            </div>

                        </div>


                        <div class="memo-detail-row">

                            <div class="memo-label">
                                Subject
                            </div>

                            <div
                                class="memo-value"
                                id="preview_subject"
                            >
                                —
                            </div>

                        </div>


                        <div class="memo-detail-row">

                            <div class="memo-label">
                                Date
                            </div>

                            <div
                                class="memo-value"
                                id="preview_date"
                            >
                                {{ date('Y-m-d') }}
                            </div>

                        </div>


                    </div>



                    {{-- =========================================================
                         ADDITIONAL TEMPLATE FIELDS
                         These come from Template Edit.
                    ========================================================== --}}

                    @include('memos.text-blocks', ['position' => 'after_subject'])
                    <div class="memo-details additional-preview-fields">

                        @foreach($template->fields as $field)

                            {{-- Do not duplicate fixed fields --}}
                            @if(in_array(
                                strtolower($field->field_name),
                                ['to', 'from', 'through', 'subject', 'date']
                            ))

                                @continue

                            @endif


                            @include('memos.text-blocks', ['position' => 'field_'.$field->id])
                            <div class="memo-detail-row {{ $field->field_type === 'textarea' ? 'memo-prose-row' : '' }}">

                                <div class="memo-label">
                                    {{ $field->field_label }}
                                </div>

                                <div
                                    class="memo-value"
                                    id="preview_{{ $field->field_name }}"
                                >
                                    —
                                </div>

                            </div>

                        @endforeach

                    </div>



                    {{-- =================================================
                        DYNAMIC TABLE PREVIEW
                    ================================================== --}}

                    @foreach($template->tables as $table)

                        @include('memos.text-blocks', ['position' => 'table_'.$table->id])
                        <div class="preview-table-section">

                            @if($table->table_label)

                                <div class="document-section-title">
                                    {{ $table->table_label }}
                                </div>

                            @endif


                            <table
                                id="preview-table-{{ $table->id }}"
                                class="charges-table dynamic-preview-table"
                                data-table-id="{{ $table->id }}"
                            >

                                <thead>

                                    <tr>

                                        @foreach($table->columns as $column)

                                            <th>
                                                {{ $column->column_label }}
                                            </th>

                                        @endforeach

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr data-row="0">

                                        @foreach($table->columns as $column)

                                            <td
                                                data-row="0"
                                                data-column="{{ $column->column_name }}"
                                            >
                                                —
                                            </td>

                                        @endforeach

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    @endforeach


                    {{-- =============================================
                         RECOMMENDATION
                    ============================================== --}}

                    @include('memos.text-blocks', ['position' => 'before_recommendation'])
                    <div class="recommendation">

                        We recommend and seek your approval to make
                        the above payment.

                    </div>



                    {{-- =============================================
                         SIGNATURE SECTION
                    ============================================== --}}

                    @include('memos.text-blocks', ['position' => 'before_signatures'])
                    <div class="signature-section">


                        {{-- Prepared --}}
                        <div class="signature-block">

                            <div class="signature-title">
                                Prepared by
                            </div>

                            <div class="signature-space"></div>

                            <div class="signature-name">
                                {{ auth()->user()->name }}
                            </div>

                            <div class="signature-designation">
                                {{ auth()->user()->designation }}
                            </div>

                        </div>


                        @php($approvalLabels = \App\Support\ApprovalSignatureLabels::forCount($approvalWorkflow?->steps->count() ?? 0))
                        @foreach($approvalWorkflow?->steps ?? [] as $index => $step)
                            <div class="signature-block" data-workflow-signature>
                                <div class="signature-title">{{ $approvalLabels[$index] ?? 'Approved by' }}</div>
                                <div class="signature-space"></div>
                                <div class="signature-name">{{ $step->approver?->name ?? 'Approver' }}</div>
                                <div class="signature-designation">{{ $step->approver?->designation }}</div>
                            </div>
                        @endforeach


                    </div>
                    @include('memos.text-blocks', ['position' => 'end'])


                </div>

            </div>

        </div>

    </div>

</div>





<style>




        .create-memo-page {

            padding: 20px;

            min-height: calc(100vh - 70px);

        }



        /* =============================================================
        PAGE HEADER
        ============================================================= */

        .page-header {

            margin-bottom: 25px;

        }


        .back-link {

            display: inline-block;

            margin-bottom: 8px;

            color: #4f46e5;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

        }


        .back-link:hover {

            text-decoration: underline;

        }


        .page-header h1 {

            margin: 5px 0;

            font-size: 28px;

            color: #1f2937;

        }


        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .page-actions {
            display: flex;
            align-items: center;
        }

        /* =============================================================
        MAIN TWO COLUMN LAYOUT
        ============================================================= */

        .memo-builder {

            display: grid;

            grid-template-columns:
                minmax(420px, 0.9fr)
                minmax(500px, 1.1fr);

            gap: 25px;

            align-items: start;

        }



        /* =============================================================
        CARDS
        ============================================================= */

        .form-card,
        .preview-card {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.04);

        }


        .form-card {

            padding: 25px;

        }



        /* =============================================================
        FORM CARD HEADER
        ============================================================= */

        .card-header {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            padding-bottom: 20px;

            margin-bottom: 25px;

            border-bottom: 1px solid #eeeeee;

        }


        .card-header h2 {

            margin: 0 0 6px;

            font-size: 19px;

            color: #1f2937;

        }


        .card-header p {

            margin: 0;

            color: #6b7280;

            font-size: 13px;

            line-height: 1.5;

        }


        .template-badge {

            width: 42px;

            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            background: #f1f3ff;

            font-size: 23px;

        }



        /* =============================================================
        FORM SECTION
        ============================================================= */

        .form-section h3,
        .approval-section h3 {

            margin: 0 0 18px;

            font-size: 17px;

            color: #1f2937;

        }



        /* =============================================================
        FORM GROUP
        ============================================================= */

        .form-group {

            margin-bottom: 18px;

        }


        .form-group label {

            display: block;

            margin-bottom: 7px;

            font-size: 13px;

            font-weight: 600;

            color: #374151;

        }


        .required {

            color: #dc2626;

        }


        .form-control {

            width: 100%;

            box-sizing: border-box;

            padding: 11px 13px;

            border: 1px solid #d9dde5;

            border-radius: 7px;

            background: #ffffff;

            color: #1f2937;

            font-family: inherit;

            font-size: 14px;

            transition:
                border-color 0.2s,
                box-shadow 0.2s;

        }


        .form-control:focus {

            outline: none;

            border-color: #6366f1;

            box-shadow:
                0 0 0 3px rgba(99, 102, 241, 0.10);

        }


        textarea.form-control {

            resize: vertical;

        }


        .calculated-field {

            background: #f5f6f8;

            font-weight: 700;

        }


        .form-group small {

            display: block;

            margin-top: 6px;

            color: #8a8f98;

            font-size: 11px;

            line-height: 1.4;

        }



        /* =============================================================
        APPROVAL SECTION
        ============================================================= */

        .approval-section {

            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #eeeeee;

        }


        .section-description {

            margin-top: -8px;

            margin-bottom: 20px;

            color: #777;

            font-size: 13px;

        }


        .approval-chain {

            display: flex;

            flex-direction: column;

        }


        .approval-step {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        .step-number {

            width: 32px;

            height: 32px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #eef0ff;

            color: #4f46e5;

            font-size: 13px;

            font-weight: 700;

        }


        .step-content strong {

            display: block;

            color: #374151;

            font-size: 13px;

        }


        .step-content span {

            display: block;

            margin-top: 2px;

            color: #8b9099;

            font-size: 11px;

        }


        .approval-line {

            width: 2px;

            height: 18px;

            margin-left: 15px;

            background: #ddd;

        }



        /* =============================================================
        ACTION BUTTONS
        ============================================================= */

        .form-actions {

            display: flex;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 30px;

            padding-top: 20px;

            border-top: 1px solid #eeeeee;

        }


        .btn {

            border: none;

            border-radius: 7px;

            padding: 11px 18px;

            cursor: pointer;

            font-family: inherit;

            font-size: 13px;

            font-weight: 600;

            transition:
                transform 0.1s,
                opacity 0.2s;

        }


        .btn:hover {

            opacity: 0.9;

        }


        .btn:active {

            transform: translateY(1px);

        }


        .btn-secondary {

            background: #f1f2f4;

            color: #374151;

        }


        .btn-primary {

            background: #4f46e5;

            color: #ffffff;

        }



        /* =============================================================
        PREVIEW CARD
        ============================================================= */

        .preview-card {

            overflow: hidden;

        }


        .preview-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 18px 20px;

            border-bottom: 1px solid #eeeeee;

        }


        .preview-header h2 {

            margin: 0;

            color: #1f2937;

            font-size: 18px;

        }


        .preview-header span {

            color: #888;

            font-size: 11px;

        }


        .preview-status {

            padding: 5px 10px;

            border-radius: 20px;

            background: #fff4d6;

            color: #8a6500 !important;

            font-size: 11px !important;

            font-weight: 600;

        }



        /* =============================================================
        DOCUMENT
        ============================================================= */

        .document-preview {

            margin: 25px;

            padding: 45px 50px;

            background: #ffffff;

            border: 1px solid #dcdcdc;

            min-height: 850px;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.05);

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            font-size: 13px;

            line-height: 1.45;

            color: #111111;

        }



        /* =============================================================
        DOCUMENT TITLE
        ============================================================= */

        .document-title {

            text-align: center;

            margin-bottom: 40px;

        }


        .document-title h1 {

            margin: 0;

            font-size: 24px;

            font-weight: 700;

        }


        .department-name {

            margin-top: 7px;

            font-size: 14px;

        }



        /* =============================================================
        MEMO DETAILS
        ============================================================= */

        .memo-details {

            margin-bottom: 30px;

        }


        .memo-detail-row {

            display: grid;

            grid-template-columns: 90px 1fr;

            column-gap: 15px;

            margin-bottom: 11px;

            min-height: 18px;

        }


        .memo-label {

            font-weight: 600;

        }


        .memo-value {

            min-height: 18px;

            word-break: break-word;

        }



        /* =============================================================
        ADDITIONAL PREVIEW FIELDS
        ============================================================= */

        .additional-preview-fields {

            margin-top: 5px;

        }



        /* =============================================================
        PAYMENT DESCRIPTION
        ============================================================= */

        .payment-description {

            margin-top: 25px;

            margin-bottom: 25px;

        }


        .document-section-title {

            margin-bottom: 12px;

            font-weight: 600;

        }


        .description-content {

            min-height: 25px;

            line-height: 1.6;

            white-space: pre-wrap;

            word-break: break-word;

        }



        /* =============================================================
        VENDOR / BILL PERIOD
        ============================================================= */

        .vendor-details {

            margin-top: 20px;

            margin-bottom: 25px;

        }



        /* =============================================================
        CHARGES TABLE / DYNAMIC TABLE
        ============================================================= */

        .charges-table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 15px;

            margin-bottom: 30px;

        }


        .charges-table th,
        .charges-table td {

            padding: 9px 10px;

            border: 1px solid #333333;

        }


        .charges-table th {

            font-weight: 700;

            text-align: left;

        }


        .charges-table th:last-child {

            text-align: right;

        }


        .charges-table .amount {

            text-align: right;

            white-space: nowrap;

        }


        .charges-table .total-row td {

            font-weight: 700;

            border-top: 2px solid #111111;

        }


        .dynamic-preview-table th,
        .dynamic-preview-table td {

            vertical-align: top;

        }


        .preview-table-section {

            margin-top: 25px;

            margin-bottom: 25px;

        }



        /* =============================================================
        RECOMMENDATION
        ============================================================= */

        .recommendation {

            margin-top: 30px;

            margin-bottom: 55px;

            line-height: 1.6;

        }



        /* =============================================================
        SIGNATURE SECTION
        ============================================================= */

        .signature-section {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 35px;

            margin-top: 35px;

        }


        .signature-block {

            text-align: left;

        }


        .signature-title {

            font-weight: 600;

        }


        .signature-space {

            height: 55px;

        }


        .signature-name {

            font-weight: 500;

        }


        .signature-designation {

            margin-top: 4px;

        }



        /* =============================================================
        DYNAMIC TABLE FORM
        ============================================================= */

        .memo-table-form-section {

            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #eeeeee;

        }


        .memo-table-title {

            margin: 0 0 15px;

            font-size: 16px;

            color: #1f2937;

        }


        .memo-table-wrapper {

            width: 100%;

            overflow-x: auto;

        }


        .memo-entry-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 650px;

        }


        .memo-entry-table th,
        .memo-entry-table td {

            border: 1px solid #d9dde5;

            padding: 8px;

            vertical-align: middle;

        }


        .memo-entry-table th {

            background: #f5f6f8;

            color: #374151;

            font-size: 12px;

            font-weight: 700;

            text-align: left;

        }


        .memo-entry-table td {

            background: #ffffff;

        }


        .memo-entry-table .form-control {

            margin: 0;

        }


        .add-table-row {

            margin-top: 12px;

            padding: 8px 14px;

            font-size: 12px;

        }



        /* =============================================================
        RESPONSIVE
        ============================================================= */

        @media (max-width: 1100px) {

            .memo-builder {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 700px) {

            .create-memo-page {

                padding: 15px;

            }


            .document-preview {

                margin: 15px;

                padding: 25px;

            }


            .memo-detail-row {

                grid-template-columns: 75px 1fr;

                column-gap: 10px;

            }


            .signature-section {

                grid-template-columns: 1fr;

                gap: 30px;

            }


            .form-actions {

                flex-direction: column;

            }


            .btn {

                width: 100%;

            }

        }

        /* Memo document presentation matching the supplied reference. */
        .preview-section { min-width: 0; }
        .preview-card { background: #e9e9e9; border: 1px solid #ddd; border-radius: 8px; }
        .preview-header { background: #fafafa; padding: 18px 24px; }
        .preview-header h2 { font-size: 15px; margin-bottom: 4px; }
        .document-preview { box-sizing: border-box; width: calc(100% - 40px); max-width: 794px; min-height: 1000px; margin: 20px auto; padding: 36px 36px 60px; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 8px #0000000d; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.4; color: #111; }
        .document-preview .document-title { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 42%); column-gap: 16px; align-items: start; text-align: left; margin-bottom: 20px; padding-right: 0; }
        .document-preview .document-title h1 { grid-column: 1; grid-row: 1; }
        .document-preview .document-title .department-name { grid-column: 1; grid-row: 2; overflow-wrap: anywhere; }
        .document-preview .document-title h1 { font-size: 36px; font-weight: 700; line-height: 1.1; color: #595959; margin: 0; }
        .document-preview .department-name { margin-top: 8px; font-size: 15px; font-weight: 700; color: #808080; }
        .document-brand { position: static; grid-column: 2; grid-row: 1 / 3; min-width: 0; justify-self: end; color: #626e79; text-align: center; line-height: 1; }
        .document-brand img { display: block; }
        .document-brand strong { display: block; font-size: clamp(19px, 2.4vw, 29px); letter-spacing: 1px; }
        .document-brand span { display: block; margin-top: 3px; font-size: 9px; font-weight: 700; }
        .document-preview .memo-details:not(.additional-preview-fields) { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); column-gap: 15px; margin-bottom: 26px; }
        .document-preview .memo-detail-row { margin: 0; min-width: 0; }
        .document-preview .memo-details:not(.additional-preview-fields) .memo-detail-row { display: flex; flex-direction: column; border: 1px solid #111; }
        .document-preview .memo-detail-row:has(#preview_to) { grid-area: 1 / 1; }
        .document-preview .memo-detail-row:has(#preview_from) { grid-area: 1 / 2; }
        .document-preview .memo-details .memo-detail-row:has(#preview_through) { grid-area: 2 / 1; border-top: 0; }
        .document-preview .memo-details .memo-detail-row:has(#preview_date) { grid-area: 2 / 2; border-top: 0; }
        .document-preview .memo-details:not(.additional-preview-fields) .memo-detail-row:has(#preview_subject) { grid-area: 3 / 1 / 4 / 3; display: grid; grid-template-columns: 88px minmax(0, 1fr); gap: 0; margin-top: 7px; }
        .document-preview .memo-label { background: #d0cece; color: #111; font-size: 12px; font-weight: 700; padding: 3px 6px; border-bottom: 1px solid #111; }
        .document-preview .memo-value { padding: 4px 6px; min-height: 25px; white-space: pre-line; overflow-wrap: anywhere; }
        .document-preview .memo-detail-row:has(#preview_subject) .memo-label { border-bottom: 0; border-right: 1px solid #111; }
        .document-preview .additional-preview-fields { margin: 0 0 26px; }
        .document-preview .additional-preview-fields .memo-detail-row { display: grid; grid-template-columns: 33.333% minmax(0, 1fr); gap: 0; border: 1px solid #111; }
        .document-preview .additional-preview-fields .memo-detail-row + .memo-detail-row { border-top: 0; }
        .document-preview .additional-preview-fields .memo-label { text-align: center; font-weight: 700; border-bottom: 0; border-right: 1px solid #111; }
        .document-preview .additional-preview-fields .memo-prose-row { display: block; border: 0; }
        .document-preview .additional-preview-fields .memo-prose-row .memo-label { width: 33.333%; box-sizing: border-box; border: 1px solid #111; padding: 6px; }
        .document-preview .memo-prose-row .memo-value { min-height: 34px; border: 1px solid #111; margin-top: -1px; }
        .document-preview .preview-table-section { overflow-x: auto; margin: 26px 0; }
        .document-preview .document-section-title { font-size: 12px; margin-bottom: 8px; }
        .document-preview .charges-table { margin: 0; font-size: 11px; width: 100%; }
        .document-preview .charges-table th, .document-preview .charges-table td { border: 1px solid #111; padding: 6px; overflow-wrap: anywhere; }
        .document-preview .charges-table th { background: #d0cece; font-weight: 700; }
        .document-preview .charges-table th:last-child { text-align: center; }
        .document-preview .recommendation { margin: 36px 0 26px; font-size: 11px; line-height: 1.6; }
        .document-preview .signature-section { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); column-gap: 16px; row-gap: 32px; margin: 0; padding: 0 6px; }
        .document-preview .signature-block { min-width: 0; overflow-wrap: anywhere; }
        .document-preview .signature-title { font-size: 11px; font-weight: 400; color: #111; }
        .document-preview .signature-space { height: 46px; border: 0; margin: 0; }
        .document-preview .signature-name { font-size: 11px; font-weight: 700; color: #111; }
        .document-preview .signature-designation { font-size: 11px; color: #595959; }
        @media (max-width: 700px) {
            .document-preview { width: calc(100% - 20px); margin: 10px auto; padding: 24px 16px 40px; min-height: 0; }
            .document-preview .document-title h1 { font-size: 28px; }
            .document-preview .department-name { font-size: 12px; }
            .document-preview .signature-section { column-gap: 10px; }
        }
        .document-preview, .document-preview * { font-family: Arial, Helvetica, sans-serif !important; font-size: 12px !important; }
        #memoForm input[readonly] { background:#f0f2f5; color:#667085; cursor:default; }
        html[data-theme=dark] #memoForm input[readonly] { background:#1e2c43 !important; color:#a3b1c7 !important; }
</style>



{{-- =============================================================
     JAVASCRIPT
============================================================= --}}

<script>

const templateTables = [

@foreach($template->tables as $table)

    {

        id: {{ $table->id }},

        columns: [

        @foreach($table->columns as $column)

            {

                name: @json($column->column_name),

                type: @json($column->column_type)

            }@if(!$loop->last),@endif

        @endforeach

        ]

    }@if(!$loop->last),@endif

@endforeach

];


/* =============================================================
   UPDATE PREVIEW
============================================================= */

function updateTablePreview(input) {

    const tableId =
        input.dataset.tableId;

    const rowIndex =
        input.dataset.rowIndex;

    const columnName =
        input.dataset.columnName;

    const previewCell =
        document.querySelector(
            '#preview-table-' +
            tableId +
            ' td[data-row="' +
            rowIndex +
            '"][data-column="' +
            columnName +
            '"]'
        );

    if (!previewCell) {
        return;
    }

    previewCell.textContent =
        input.value || '—';
}



/* =============================================================
   TOTAL CHARGES
============================================================= */

function calculateTotal() {

    const voiceVpn =
        parseFloat(
            document.getElementById('voice_vpn')?.value
        ) || 0;


    const taxes =
        parseFloat(
            document.getElementById(
                'government_taxes_levies'
            )?.value
        ) || 0;


    const vat =
        parseFloat(
            document.getElementById('vat')?.value
        ) || 0;


    const total =
        voiceVpn + taxes + vat;


    const totalInput =
        document.getElementById(
            'total_charges'
        );


    const totalPreview =
        document.getElementById(
            'preview_total_charges'
        );


    if (totalInput) {

        totalInput.value =
            total.toFixed(2);

    }


    if (totalPreview) {

        totalPreview.textContent =
            total.toFixed(2);

    }

}

/* =============================================================
   UPDATE NORMAL FIELD PREVIEW
============================================================= */

function updatePreview(fieldId) {

    const input = document.getElementById(fieldId);

    if (!input) {
        return;
    }

    const previewElement = document.getElementById(
        'preview_' + fieldId
    );

    if (!previewElement) {
        return;
    }

    const value = input.value.trim();

    previewElement.textContent = value || '—';
}

/* =============================================================
   INITIALIZE FORM EVENTS
============================================================= */

document
    .querySelectorAll(
        '#memoForm input, #memoForm textarea, #memoForm select'
    )
    .forEach(function(input) {

        input.addEventListener(
            'input',
            function() {

                if (
                    this.classList.contains('table-input')
                ) {

                    updateTablePreview(this);

                } else {

                    updatePreview(this.id);

                }

                calculateTotal();

            }
        );


        input.addEventListener(
            'change',
            function() {

                if (
                    this.classList.contains('table-input')
                ) {

                    updateTablePreview(this);

                } else {

                    updatePreview(this.id);

                }

                calculateTotal();

            }
        );

    });





/* =============================================================
   INITIAL CALCULATION
============================================================= */

calculateTotal();



/* =============================================================
   DYNAMIC TABLE ROWS
============================================================= */

function addTableRow(tableId) {

    const table = templateTables.find(
        item => item.id === tableId
    );

    if (!table) {
        return;
    }

    const tbody =
        document.getElementById(
            'table-body-' + tableId
        );

    if (!tbody) {
        return;
    }

    const rowIndex = Math.max(
        Number(tbody.dataset.nextRowIndex || 0),
        ...Array.from(tbody.querySelectorAll('[data-row-index]'), input => Number(input.dataset.rowIndex) + 1)
    );
    tbody.dataset.nextRowIndex = rowIndex + 1;

    let rowHtml = '<tr>';

    table.columns.forEach(function(column) {

        let inputHtml = '';

        if (column.type === 'textarea') {

            inputHtml = `
                <textarea
                    name="tables[${tableId}][rows][${rowIndex}][${column.name}]"
                    class="form-control table-input"
                    data-table-id="${tableId}"
                    data-row-index="${rowIndex}"
                    data-column-name="${column.name}"
                    rows="2"
                ></textarea>
            `;

        } else if (column.type === 'date') {

            inputHtml = `
                <input
                    type="date"
                    name="tables[${tableId}][rows][${rowIndex}][${column.name}]"
                    class="form-control table-input"
                    data-table-id="${tableId}"
                    data-row-index="${rowIndex}"
                    data-column-name="${column.name}"
                >
            `;

        } else if (column.type === 'number') {

            inputHtml = `
                <input
                    type="number"
                    name="tables[${tableId}][rows][${rowIndex}][${column.name}]"
                    class="form-control table-input"
                    data-table-id="${tableId}"
                    data-row-index="${rowIndex}"
                    data-column-name="${column.name}"
                    step="1"
                >
            `;

        } else if (column.type === 'decimal') {

            inputHtml = `
                <input
                    type="number"
                    name="tables[${tableId}][rows][${rowIndex}][${column.name}]"
                    class="form-control table-input"
                    data-table-id="${tableId}"
                    data-row-index="${rowIndex}"
                    data-column-name="${column.name}"
                    step="0.01"
                >
            `;

        } else {

                inputHtml = ` 
                    <input 
                        type="text" 
                        name="tables[${tableId}][rows][${rowIndex}][${column.name}]" 
                        class="form-control table-input"
                        data-table-id="${tableId}"
                        data-row-index="${rowIndex}"
                        data-column-name="${column.name}"
                    > 
                `;

            }

        rowHtml += `<td>${inputHtml}</td>`;
    });

    rowHtml += '</tr>';

    tbody.insertAdjacentHTML(
        'beforeend',
        rowHtml
    );

    /*
     * Attach live preview events to the newly
     * created table inputs.
     */
    const newRow =
        tbody.querySelector(
            'tr:last-child'
        );

    if (newRow) {

        newRow
            .querySelectorAll('.table-input')
            .forEach(function(input) {

                input.addEventListener(
                    'input',
                    function() {
                        updateTablePreview(this);
                    }
                );

                input.addEventListener(
                    'change',
                    function() {
                        updateTablePreview(this);
                    }
                );

            });
    }

    /*
     * Create the corresponding empty row
     * in the Live Document Preview.
     */
    addPreviewTableRow(
        tableId,
        rowIndex,
        table
    );
}

/* =============================================================
   UPDATE TABLE PREVIEW CELL
============================================================= */

function updateTablePreview(input) {

    const tableId =
        input.dataset.tableId;

    const rowIndex =
        input.dataset.rowIndex;

    const columnName =
        input.dataset.columnName;

    const previewCell =
        document.querySelector(
            '#preview-table-' +
            tableId +
            ' [data-row="' +
            rowIndex +
            '"][data-column="' +
            columnName +
            '"]'
        );

    if (!previewCell) {
        return;
    }

    previewCell.textContent =
        input.value || '—';
}


/* =============================================================
   ADD ROW TO LIVE TABLE PREVIEW
============================================================= */

function addPreviewTableRow(
    tableId,
    rowIndex,
    table
) {

    const previewTable =
        document.getElementById(
            'preview-table-' + tableId
        );

    if (!previewTable) {
        return;
    }

    const tbody =
        previewTable.querySelector('tbody');

    if (!tbody) {
        return;
    }

    let rowHtml = `
        <tr data-row="${rowIndex}">
    `;

    table.columns.forEach(function(column) {

        rowHtml += `
            <td
                data-row="${rowIndex}"
                data-column="${column.name}"
            >—</td>
        `;

    });

    rowHtml += '</tr>';

    tbody.insertAdjacentHTML(
        'beforeend',
        rowHtml
    );
}

</script>

@include('memos.table-formatting')
@endsection
