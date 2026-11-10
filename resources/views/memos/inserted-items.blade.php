@foreach($memo->inserted_items ?? [] as $itemIndex => $item)
    @if($item['kind'] === 'field')
        @if(filled($item['value'] ?? null))
            <table class="fields"><tr><td class="caption">{{ $item['label'] }}</td><td class="value">{{ $item['value'] }}</td></tr></table>
        @endif
    @else
        @if(filled($item['label'] ?? null))<h2>{{ $item['label'] }}</h2>@endif
        <table class="charges">
            <thead><tr>@foreach($item['columns'] as $column)<th style="{{ in_array($column['column_type'] ?? '', ['number', 'decimal'], true) ? 'text-align:right' : '' }}">{{ $column['column_label'] }}</th>@endforeach</tr></thead>
            <tbody>@foreach($item['rows'] ?? [] as $rowIndex => $row)<tr>@foreach($item['columns'] as $column)
                @php $cellValue = $row[$column['column_name']] ?? ''; $numericColumn = in_array($column['column_type'] ?? '', ['number', 'decimal'], true); @endphp
                <td style="{{ $memo->tableCellStyle('inserted_items['.$itemIndex.'][rows]['.$rowIndex.']['.$column['column_name'].']') }}{{ $numericColumn ? ';text-align:right' : '' }}">{{ $numericColumn && is_numeric(str_replace(',', '', (string) $cellValue)) ? number_format((float) str_replace(',', '', (string) $cellValue), ($column['column_type'] ?? '') === 'number' ? 0 : max(0, strlen(rtrim(substr(strrchr((string) $cellValue, '.') ?: '', 1), '0'))), '.', ',') : $cellValue }}</td>
            @endforeach</tr>@endforeach</tbody>
        </table>
    @endif
@endforeach
