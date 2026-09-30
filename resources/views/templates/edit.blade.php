@extends('layouts.app')

@section('title', 'Configure Memo Template')

@section('styles')
    @include('templates.builder-styles')
@endsection

@section('content')

<div class="template-editor">
@section('header-title')
Configure your template
@endsection
@section('header-description')
Shape the information your team collects, from individual fields to detailed tables.
@endsection
@section('header-eyebrow')
Template builder
@endsection
@section('header-back')
<x-back-link :fallback="route('templates.index')" />
@endsection

<div class="editor-body">
    <ol class="editor-steps" aria-label="Template setup">
        <li><span aria-label="Completed">&check;</span> Template details</li>
        <li class="current" aria-current="step"><span>2</span> Fields &amp; tables</li>
        <li><span>3</span> Approval workflow</li>
    </ol>
    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
<div class="builder-card">

    {{-- =========================================================
         TEMPLATE INFORMATION
    ========================================================== --}}

    <h2 class="section-title">Template overview</h2>


    <div class="template-info">

        <div class="info-item">

            <span class="info-label">
                Template
            </span>

            <span class="info-value">
                {{ $template->template_name }}
            </span>

        </div>


        <div class="info-item">

            <span class="info-label">
                Code
            </span>

            <span class="info-value">
                {{ $template->template_code }}
            </span>

        </div>


        <div class="info-item">

            <span class="info-label">
                Department
            </span>

            <span class="info-value">
                {{ $template->department->department_name }}
            </span>

        </div>

    </div>


    {{-- =========================================================
         FIELDS
    ========================================================== --}}

    <h2 class="section-title"><span class="section-number">01</span>Memo fields</h2><p class="section-description">Choose the details employees will complete. Save your fields before moving to another section.</p>


    <form
    method="POST"
    action="{{ route('templates.fields.store', $template->id) }}"
    id="fields-form"
>

    @csrf


    <div class="field-builder">

            <div class="field-builder-header">

                Fields employees will complete

            </div>


            <div id="fields-container">

    @forelse($template->fields as $field)

        <div class="field-row">

            <div class="field-group">
                <label>Field name</label>

                <input
                    type="text"
                    name="fields[{{ $loop->index }}][field_label]"
                    class="form-control"
                    value="{{ $field->field_label }}"
                    placeholder="e.g. Subject"
                >
            </div>


            <input
                    type="hidden"
                    name="fields[{{ $loop->index }}][field_name]"
                    class="form-control"
                    value="{{ $field->field_name }}"
                    placeholder="e.g. subject"
                >


            <div class="field-group">
                <label>Field Type</label>

                <select
                    name="fields[{{ $loop->index }}][field_type]"
                    class="form-control"
                >

                    <option value="text"
                        {{ $field->field_type === 'text' ? 'selected' : '' }}>
                        Text
                    </option>

                    <option value="textarea"
                        {{ $field->field_type === 'textarea' ? 'selected' : '' }}>
                        Text Area
                    </option>

                    <option value="number"
                        {{ $field->field_type === 'number' ? 'selected' : '' }}>
                        Number
                    </option>

                    <option value="date"
                        {{ $field->field_type === 'date' ? 'selected' : '' }}>
                        Date
                    </option>

                    <option value="datetime"
                        {{ $field->field_type === 'datetime' ? 'selected' : '' }}>
                        Date & Time
                    </option>

                    <option value="email"
                        {{ $field->field_type === 'email' ? 'selected' : '' }}>
                        Email
                    </option>

                    <option value="select"
                        {{ $field->field_type === 'select' ? 'selected' : '' }}>
                        Select
                    </option>

                    <option value="radio"
                        {{ $field->field_type === 'radio' ? 'selected' : '' }}>
                        Radio
                    </option>

                    <option value="checkbox"
                        {{ $field->field_type === 'checkbox' ? 'selected' : '' }}>
                        Checkbox
                    </option>

                    <option value="file"
                        {{ $field->field_type === 'file' ? 'selected' : '' }}>
                        File
                    </option>

                    <option value="richtext"
                        {{ $field->field_type === 'richtext' ? 'selected' : '' }}>
                        Rich Text
                    </option>

                </select>
            </div>


            <div class="field-group">
                <label>Required</label>

                <div class="required-group">

                    <input
                        type="checkbox"
                        name="fields[{{ $loop->index }}][is_required]"
                        value="1"
                        {{ $field->is_required ? 'checked' : '' }}
                    >

                    <span>Yes</span>

                </div>
            </div>


            <div class="field-group">
                <label>Remove</label>

                <button
                    type="button"
                    class="remove-field"
                    aria-label="Remove field" onclick="removeField(this)"
                >
                    &times;
                </button>
            </div>

        </div>

    @empty

        <div class="empty-fields" id="empty-fields">
            No fields added yet.
        </div>

    @endforelse

</div>

            <div class="add-field-area">

                <button
                    type="button"
                    class="add-field-btn"
                    onclick="addField()"
                >
                    + Add Field
                </button>

            </div>

        </div>


        <div class="form-actions">

            <a
                href="{{ route('templates.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>

                <a
                    href="{{ route(
                        'templates.approval-workflow.edit',
                        $template->id
                    ) }}"
                    class="btn btn-approval-workflow"
                >
                    Approval workflow &rarr;
                </a>
            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Fields
            </button>

        </div>

    </form>

</div>

{{-- =========================================================
     TABLE BUILDER
========================================================== --}}

<form
    method="POST"
    action="{{ route('templates.tables.store', $template->id) }}"
    id="tables-form"
>

    @csrf

    <div class="table-builder">

        <div class="field-builder-header">

            <div>
                <h2><span class="section-number">02</span>Memo tables</h2>

                <p>
                    Group repeating information into tables. Save table changes separately below.
                </p>
            </div>

            <button
                type="button"
                class="add-field-btn"
                onclick="addTable()"
            >
                + Add Table
            </button>

        </div>


        <div id="tables-container">

    @forelse($template->tables as $table)

        <div class="template-table-card">

            <div class="table-header">

                <div>
                    <strong>Table {{ $loop->iteration }}</strong>
                </div>

                <button
                    type="button"
                    class="remove-field"
                    aria-label="Remove table" onclick="removeTable(this)"
                >
                    &times;
                </button>

            </div>


            <div class="table-info">

                <input
                        type="hidden"
                        name="tables[{{ $loop->index }}][table_name]"
                        class="form-control"
                        value="{{ $table->table_name }}"
                        placeholder="e.g. payment_details"
                    >


                <div class="field-group">

                    <label>Table title (optional)</label>

                    <input
                        type="text"
                        name="tables[{{ $loop->index }}][table_label]"
                        class="form-control"
                        value="{{ $table->table_label }}"
                        placeholder="Leave blank for no title"
                    >

                </div>

            </div>


            <div class="columns-section">

                <div class="columns-header">

                    <strong>Columns</strong>

                    <button
                        type="button"
                        class="add-column-btn"
                        onclick="addColumn(this, {{ $loop->index }})"
                    >
                        + Add Column
                    </button>

                </div>


                <div class="columns-container">

                    @forelse($table->columns as $column)

                        <div class="table-column-row">

                            <div class="field-group">

                                <label>Column name</label>

                                <input
                                    type="text"
                                    name="tables[{{ $loop->parent->index }}][columns][{{ $loop->index }}][column_label]"
                                    class="form-control"
                                    value="{{ $column->column_label }}"
                                >

                            </div>


                            <input
                                    type="hidden"
                                    name="tables[{{ $loop->parent->index }}][columns][{{ $loop->index }}][column_name]"
                                    class="form-control"
                                    value="{{ $column->column_name }}"
                                >


                            <div class="field-group">

                                <label>Column Type</label>

                                <select
                                    name="tables[{{ $loop->parent->index }}][columns][{{ $loop->index }}][column_type]"
                                    class="form-control"
                                >

                                    <option value="text"
                                        {{ $column->column_type === 'text' ? 'selected' : '' }}>
                                        Text
                                    </option>

                                    <option value="number"
                                        {{ $column->column_type === 'number' ? 'selected' : '' }}>
                                        Number
                                    </option>

                                    <option value="decimal"
                                        {{ $column->column_type === 'decimal' ? 'selected' : '' }}>
                                        Decimal
                                    </option>

                                    <option value="date"
                                        {{ $column->column_type === 'date' ? 'selected' : '' }}>
                                        Date
                                    </option>

                                    <option value="textarea"
                                        {{ $column->column_type === 'textarea' ? 'selected' : '' }}>
                                        Text Area
                                    </option>

                                </select>

                            </div>


                            <div class="field-group">

                                <label>Required</label>

                                <div class="required-group">

                                    <input
                                        type="checkbox"
                                        name="tables[{{ $loop->parent->index }}][columns][{{ $loop->index }}][is_required]"
                                        value="1"
                                        {{ $column->is_required ? 'checked' : '' }}
                                    >

                                    <span>Yes</span>

                                </div>

                            </div>


                            <div class="field-group">

                                <label>Remove</label>

                                <button
                                    type="button"
                                    class="remove-field"
                                    aria-label="Remove column" onclick="removeColumn(this)"
                                >
                                    &times;
                                </button>

                            </div>

                        </div>

                    @empty

                        <div class="empty-fields">
                            No columns added yet.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    @empty

        <div class="empty-fields" id="empty-tables">
            No tables added yet.
        </div>

    @endforelse

</div>


        <div class="form-actions">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Tables
            </button>

        </div>

    </div>

</form>

</div>
</div>

<script>

document.querySelectorAll('.columns-container').forEach(container => {
    container.dataset.nextColumnIndex = container.querySelectorAll('.table-column-row').length;
});

let tableIndex = {{ $template->tables->count() }};

function addTable()
{
    const container = document.getElementById('tables-container');
    const emptyMessage = document.getElementById('empty-tables');

    if (emptyMessage) {
        emptyMessage.remove();
    }

    const table = document.createElement('div');

    table.className = 'template-table-card';

    table.innerHTML = `
        <div class="table-header">

            <div>
                <strong>Table ${tableIndex + 1}</strong>
            </div>

            <button
                type="button"
                class="remove-field"
                aria-label="Remove table" onclick="removeTable(this)"
            >
                &times;
            </button>

        </div>


        <div class="table-info">

            


            <div class="field-group">

                <label>Table title (optional)</label>

                <input
                    type="text"
                    name="tables[${tableIndex}][table_label]"
                    class="form-control"
                    placeholder="Leave blank for no title"
                >

            </div>

        </div>


        <div class="columns-section">

            <div class="columns-header">

                <strong>Columns</strong>

                <button
                    type="button"
                    class="add-column-btn"
                    onclick="addColumn(this, ${tableIndex})"
                >
                    + Add Column
                </button>

            </div>


            <div class="columns-container">

                <div class="empty-fields">
                    No columns added yet.
                </div>

            </div>

        </div>

    `;

    container.appendChild(table);

    tableIndex++;
}


function removeTable(button)
{
    const table = button.closest('.template-table-card');

    if (table) {
        table.remove();
    }

    const container = document.getElementById('tables-container');

    if (!container.querySelector('.template-table-card')) {

        const emptyMessage = document.createElement('div');

        emptyMessage.className = 'empty-fields';
        emptyMessage.id = 'empty-tables';

        emptyMessage.textContent = 'No tables added yet.';

        container.appendChild(emptyMessage);
    }
}


function addColumn(button, currentTableIndex)
{
    const table = button.closest('.template-table-card');

    const container = table.querySelector('.columns-container');

    const emptyMessage = container.querySelector('.empty-fields');

    if (emptyMessage) {
        emptyMessage.remove();
    }

    const columnIndex = Number(container.dataset.nextColumnIndex ?? container.querySelectorAll('.table-column-row').length);
    container.dataset.nextColumnIndex = columnIndex + 1;

    const row = document.createElement('div');

    row.className = 'table-column-row';

    row.innerHTML = `

        <div class="field-group">

            <label>Column name</label>

            <input
                type="text"
                name="tables[${currentTableIndex}][columns][${columnIndex}][column_label]"
                class="form-control"
                placeholder="e.g. Description"
            >

        </div>


        


        <div class="field-group">

            <label>Column Type</label>

            <select
                name="tables[${currentTableIndex}][columns][${columnIndex}][column_type]"
                class="form-control"
            >

                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="decimal">Decimal</option>
                <option value="date">Date</option>
                <option value="textarea">Text Area</option>

            </select>

        </div>


        <div class="field-group">

            <label>Required</label>

            <div class="required-group">

                <input
                    type="checkbox"
                    name="tables[${currentTableIndex}][columns][${columnIndex}][is_required]"
                    value="1"
                >

                <span>Yes</span>

            </div>

        </div>


        <div class="field-group">

            <label>Remove</label>

            <button
                type="button"
                class="remove-field"
                aria-label="Remove column" onclick="removeColumn(this)"
            >
                &times;
            </button>

        </div>

    `;

    container.appendChild(row);
}


function removeColumn(button)
{
    const row = button.closest('.table-column-row');
    const container = button.closest('.columns-container');

    if (row) {
        row.remove();
    }

    if (!container.querySelector('.table-column-row')) {

        const emptyMessage = document.createElement('div');

        emptyMessage.className = 'empty-fields';

        emptyMessage.textContent =
            'No columns added yet.';

        container.appendChild(emptyMessage);
    }
}





let fieldIndex = {{ $template->fields->count() }};


function addField()
{
    const container = document.getElementById('fields-container');

    const emptyMessage = document.getElementById('empty-fields');

    if (emptyMessage) {
        emptyMessage.remove();
    }


    const row = document.createElement('div');

    row.className = 'field-row';

    row.innerHTML = `

        <div class="field-group">

            <label>
                Field name
            </label>

            <input
                type="text"
                name="fields[${fieldIndex}][field_label]"
                class="form-control"
                placeholder="e.g. Subject"
            >

        </div>


        


        <div class="field-group">

            <label>
                Field Type
            </label>

            <select
                name="fields[${fieldIndex}][field_type]"
                class="form-control"
            >

                <option value="text">
                    Text
                </option>

                <option value="textarea">
                    Text Area
                </option>

                <option value="number">
                    Number
                </option>

                <option value="date">
                    Date
                </option>

                <option value="datetime">
                    Date & Time
                </option>

                <option value="email">
                    Email
                </option>

                <option value="select">
                    Select
                </option>

                <option value="radio">
                    Radio
                </option>

                <option value="checkbox">
                    Checkbox
                </option>

                <option value="file">
                    File
                </option>

                <option value="richtext">
                    Rich Text
                </option>

            </select>

        </div>


        <div class="field-group">

            <label>
                Required
            </label>

            <div class="required-group">

                <input
                    type="checkbox"
                    name="fields[${fieldIndex}][is_required]"
                    value="1"
                >

                <span>
                    Yes
                </span>

            </div>

        </div>


        <div class="field-group">

            <label>
                Remove
            </label>

            <button
                type="button"
                class="remove-field"
                aria-label="Remove field" onclick="removeField(this)"
            >
                &times;
            </button>

        </div>

    `;


    container.appendChild(row);

    fieldIndex++;
}


function removeField(button)
{
    const row = button.closest('.field-row');

    if (row) {
        row.remove();
    }


    const container = document.getElementById('fields-container');

    if (!container.querySelector('.field-row')) {

        const emptyMessage = document.createElement('div');

        emptyMessage.className = 'empty-fields';

        emptyMessage.id = 'empty-fields';

        emptyMessage.textContent = 'No fields added yet.';

        container.appendChild(emptyMessage);

    }
}

</script>

@endsection
