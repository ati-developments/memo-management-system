@include('memos.text-blocks', ['position' => 'after_subject'])
@foreach($memo->documentFields() as $field)
    @continue(in_array(strtolower($field->field_name), ['to', 'from', 'through', 'date', 'subject']))
    @include('memos.text-blocks', ['position' => 'field_'.$field->template_field_id])
    @continue($field->field_value === null || $field->field_value === '')
    @if($field->templateField?->field_type === 'textarea')
        <div class="prose-label">{{ $field->templateField?->field_label ?? Str::headline($field->field_name) }}</div>
        <div class="prose">{{ $field->field_value }}</div>
    @else
        <table class="fields"><tr><td class="caption">{{ $field->templateField?->field_label ?? Str::headline($field->field_name) }}</td><td class="value">{{ $field->field_value }}</td></tr></table>
    @endif
@endforeach
@if($memo->content)<div class="prose">{{ $memo->content }}</div>@endif
@foreach($memo->documentTables() as $tableId => $rows)
    @php
        $table = $rows->first()?->templateTable ?? $memo->template?->tables->firstWhere('id', $tableId);
        $columns = $table?->columns->sortBy('column_order')->pluck('column_label', 'column_name') ?? collect();
        foreach ($rows as $row) {
            foreach (array_keys($row->row_data) as $key) {
                if (!$columns->has($key)) $columns->put($key, Str::headline($key));
            }
        }
    @endphp
    @include('memos.text-blocks', ['position' => 'table_'.$tableId])
    @continue($rows->isEmpty())
    @if($table?->table_label)<h2>{{ $table->table_label }}</h2>@endif
    <table class="charges"><thead><tr>@foreach($columns as $label)<th>{{ $label }}</th>@endforeach</tr></thead>
        <tbody>@foreach($rows as $row)<tr>@foreach($columns as $key => $label)<td style="{{ $memo->tableCellStyle('tables['.$tableId.'][rows]['.$row->row_order.']['.$key.']') }}">{{ $row->row_data[$key] ?? '—' }}</td>@endforeach</tr>@endforeach</tbody>
    </table>
@endforeach
@include('memos.text-blocks', ['position' => 'before_recommendation'])
@include('memos.inserted-items')
<p class="recommendation">We recommend and seek your approval to make the above payment.</p>
@include('memos.text-blocks', ['position' => 'before_signatures'])
