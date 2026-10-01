@foreach($memo->inserted_items ?? [] as $itemIndex => $item)
    @if($item['kind'] === 'field')
        @if(filled($item['value'] ?? null))
            <table class="fields"><tr><td class="caption">{{ $item['label'] }}</td><td class="value">{{ $item['value'] }}</td></tr></table>
        @endif
    @else
        @if(filled($item['label'] ?? null))<h2>{{ $item['label'] }}</h2>@endif
        <table class="charges">
            <thead><tr>@foreach($item['columns'] as $column)<th>{{ $column['column_label'] }}</th>@endforeach</tr></thead>
            <tbody>@foreach($item['rows'] ?? [] as $rowIndex => $row)<tr>@foreach($item['columns'] as $column)<td style="{{ $memo->tableCellStyle('inserted_items['.$itemIndex.'][rows]['.$rowIndex.']['.$column['column_name'].']') }}">{{ $row[$column['column_name']] ?? '' }}</td>@endforeach</tr>@endforeach</tbody>
        </table>
    @endif
@endforeach
