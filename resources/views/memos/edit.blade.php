@extends('layouts.app')
@section('title', 'Edit Memo')
@section('content')
<style>
    .memo-editor { max-width: 950px; margin: 24px auto; }
    .memo-editor form { padding: 28px; background: white; border: 1px solid #ddd; border-radius: 10px; }
    .memo-editor label { display: block; margin: 16px 0 6px; font-size: 13px; font-weight: 600; }
    .memo-editor input, .memo-editor textarea, .memo-editor select { width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font: inherit; }
    .memo-editor textarea { min-height: 120px; }
    .memo-editor table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    .memo-editor th, .memo-editor td { padding: 6px; text-align: left; }
    .memo-editor button { padding: 10px 16px; border: 1px solid #ccd3df; border-radius: 6px; cursor: pointer; }
    .memo-editor .primary { background: #2856a8; color: white; }
    .memo-editor .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 26px; }
</style>
<div class="memo-editor">
@section('header-title')
Edit {{ $memo->memo_number }}
@endsection
@section('header-description')
{{ $memo->template->template_name }} &middot; Draft
@endsection
@section('header-eyebrow')
Memo builder
@endsection
@section('header-back')
<a href="{{ route('memos.my') }}">&larr; Back to my memos</a>
@endsection
    @if($errors->any())<div role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ route('memos.update', $memo) }}">
        @csrf
        @method('PUT')
        <label for="memo-attachments">Add attachments (up to 10 files, 10 MB each)</label>
        <input id="memo-attachments" type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.png,.jpg,.jpeg">
        @if($memo->attachments->isNotEmpty())
            <p>Current attachments:</p>
            <ul>@foreach($memo->attachments as $attachment)<li><a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}">{{ $attachment->original_name }}</a></li>@endforeach</ul>
        @endif
        @if($memo->template->allow_optional_text || !empty($memo->text_blocks))
            @include('memos.text-block-editor')
        @endif
        @foreach(['to' => 'To', 'from' => 'From', 'through' => 'Through', 'date' => 'Date', 'subject' => 'Subject'] as $name => $label)
            <label for="field_{{ $name }}">{{ $label }}</label>
            <input id="field_{{ $name }}" name="{{ $name }}" type="{{ $name === 'date' ? 'date' : 'text' }}"
                value="{{ old($name, $name === 'subject' ? $memo->subject : ($values->get($name) ?? ($name === 'from' ? auth()->user()->name : ($name === 'date' ? $memo->created_at->format('Y-m-d') : '')))) }}" @required($name === 'subject')>
        @endforeach
        @foreach($memo->template->fields->where('is_active', true)->sortBy('field_order') as $field)
            @continue(in_array($field->field_name, ['to', 'from', 'through', 'date', 'subject']))
            @include('memos.add-text-button', ['position' => 'field_'.$field->id])
            <label for="field_{{ $field->field_name }}">{{ $field->field_label }}</label>
            @if($field->field_type === 'textarea')
                <textarea id="field_{{ $field->field_name }}" name="{{ $field->field_name }}">{{ old($field->field_name, $values->get($field->field_name)) }}</textarea>
            @elseif($field->field_type === 'select')
                <select id="field_{{ $field->field_name }}" name="{{ $field->field_name }}">
                    <option value="">Select an option</option>
                    @foreach($field->options ?? [] as $option)<option value="{{ $option }}" @selected(old($field->field_name, $values->get($field->field_name)) == $option)>{{ $option }}</option>@endforeach
                </select>
            @else
                <input id="field_{{ $field->field_name }}" name="{{ $field->field_name }}" type="{{ in_array($field->field_type, ['date', 'number']) ? $field->field_type : 'text' }}" step="any" value="{{ old($field->field_name, $values->get($field->field_name)) }}" @readonly($field->field_name === 'total_charges')>
            @endif
        @endforeach
        @foreach($memo->template->tables->where('is_active', true)->sortBy('table_order') as $table)
            @php
                $columns = $table->columns->where('is_active', true)->sortBy('column_order');
                $rows = old('tables.'.$table->id.'.rows', $memo->tableRows->where('template_table_id', $table->id)->pluck('row_data', 'row_order')->all());
                if (!$rows) $rows = [[]];
            @endphp
            @include('memos.add-text-button', ['position' => 'table_'.$table->id])
            <h2>{{ $table->table_label ?: 'Details' }}</h2>
            <div style="overflow-x:auto">
                <table id="edit-table-{{ $table->id }}"><thead><tr>@foreach($columns as $column)<th>{{ $column->column_label }}</th>@endforeach</tr></thead><tbody>
                @foreach($rows as $index => $row)
                    <tr>@foreach($columns as $column)<td @class(['memo-row-end' => $loop->last])>
                        @if($column->column_type === 'textarea')
                            <textarea aria-label="{{ $column->column_label }}" name="tables[{{ $table->id }}][rows][{{ $index }}][{{ $column->column_name }}]">{{ $row[$column->column_name] ?? '' }}</textarea>
                        @else
                            <input aria-label="{{ $column->column_label }}" name="tables[{{ $table->id }}][rows][{{ $index }}][{{ $column->column_name }}]" type="{{ $column->column_type === 'decimal' ? 'number' : (in_array($column->column_type, ['number', 'date']) ? $column->column_type : 'text') }}" step="any" value="{{ $row[$column->column_name] ?? '' }}">
                        @endif
                    @if($loop->last)<button type="button" class="remove-memo-row" data-remove-row aria-label="Remove row" title="Remove row">&times;</button>@endif
                    </td>@endforeach</tr>
                @endforeach
                </tbody></table>
            </div>
            <button type="button" data-add-row="edit-table-{{ $table->id }}">+ Add row</button>
        @endforeach
        @include('memos.add-text-button', ['position' => 'before_recommendation'])
        @include('memos.inserted-items-editor')
        <div class="actions">
            <button class="primary" type="submit" name="action" value="draft">Save changes</button>
            <button type="submit" name="action" value="submit">Submit for approval</button>
            <a href="{{ route('memos.pdf', $memo) }}" target="_blank" rel="noopener noreferrer">View saved PDF</a>
        </div>
    </form>
</div>
<script>
document.querySelectorAll('[data-add-row]').forEach(button => {
    const body = document.getElementById(button.dataset.addRow).tBodies[0];
    const prototype = body.rows[0].cloneNode(true);
    let nextIndex = Math.max(0, ...Array.from(body.querySelectorAll('input, textarea'), input => Number(input.name.match(/\[rows\]\[(\d+)\]/)[1]))) + 1;
    button.addEventListener('click', () => {
        const row = prototype.cloneNode(true);
        row.querySelectorAll('input, textarea').forEach(input => {
            input.name = input.name.replace(/\[rows\]\[\d+\]/, '[rows][' + nextIndex + ']');
            input.value = '';
        });
        nextIndex++;
        body.appendChild(row);
    });
});
document.querySelector('.memo-editor form').addEventListener('click', event => {
    if (event.target.matches('[data-remove-row]')) {
        const row = event.target.closest('tr');
        if (row.parentElement.rows.length > 1) row.remove();
        else row.querySelectorAll('input, textarea').forEach(input => input.value = '');
    }
});
const totalField = document.getElementById('field_total_charges');
const chargeFields = ['voice_vpn', 'government_taxes_levies', 'vat'].map(name => document.getElementById('field_' + name));
if (totalField) {
    const updateTotal = () => totalField.value = chargeFields.reduce((total, input) => total + (Number(input?.value) || 0), 0).toFixed(2);
    chargeFields.filter(Boolean).forEach(input => input.addEventListener('input', updateTotal));
}
</script>
@include('memos.table-formatting')
@endsection
