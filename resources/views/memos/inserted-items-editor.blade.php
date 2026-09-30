@foreach(old('inserted_items', $memo->inserted_items ?? []) as $index => $item)
    <input type="hidden" name="inserted_items[{{ $index }}][kind]" value="{{ $item['kind'] }}">
    <input type="hidden" name="inserted_items[{{ $index }}][label]" value="{{ $item['label'] }}">
    @if($item['kind'] === 'field')
        <input type="hidden" name="inserted_items[{{ $index }}][type]" value="{{ $item['type'] ?? 'text' }}">
        <label for="inserted-field-{{ $index }}">{{ $item['label'] }}</label>
        @if(($item['type'] ?? 'text') === 'textarea')
            <textarea id="inserted-field-{{ $index }}" name="inserted_items[{{ $index }}][value]">{{ $item['value'] ?? '' }}</textarea>
        @else
            <input id="inserted-field-{{ $index }}" name="inserted_items[{{ $index }}][value]" type="{{ $item['type'] ?? 'text' }}" step="any" value="{{ $item['value'] ?? '' }}">
        @endif
    @else
        <h2>{{ $item['label'] }}</h2>
        @foreach($item['columns'] as $columnIndex => $column)
            @foreach($column as $key => $value)
                <input type="hidden" name="inserted_items[{{ $index }}][columns][{{ $columnIndex }}][{{ $key }}]" value="{{ $value }}">
            @endforeach
        @endforeach
        <table id="inserted-table-{{ $index }}">
            <thead><tr>@foreach($item['columns'] as $column)<th>{{ $column['column_label'] }}</th>@endforeach<th>Action</th></tr></thead>
            <tbody>@foreach(($item['rows'] ?? []) ?: [[]] as $rowIndex => $row)
                <tr>@foreach($item['columns'] as $column)<td>
                    @if($column['column_type'] === 'textarea')
                        <textarea aria-label="{{ $column['column_label'] }}" name="inserted_items[{{ $index }}][rows][{{ $rowIndex }}][{{ $column['column_name'] }}]">{{ $row[$column['column_name']] ?? '' }}</textarea>
                    @else
                        <input aria-label="{{ $column['column_label'] }}" name="inserted_items[{{ $index }}][rows][{{ $rowIndex }}][{{ $column['column_name'] }}]" type="{{ $column['column_type'] === 'decimal' ? 'number' : $column['column_type'] }}" step="any" value="{{ $row[$column['column_name']] ?? '' }}">
                    @endif
                </td>@endforeach<td><button type="button" data-remove-row>Remove</button></td></tr>
            @endforeach</tbody>
        </table>
        <button type="button" data-add-row="inserted-table-{{ $index }}">+ Add row</button>
    @endif
@endforeach
