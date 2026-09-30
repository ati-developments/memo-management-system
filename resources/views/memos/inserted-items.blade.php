@foreach($memo->inserted_items ?? [] as $item)
    @if($item['kind'] === 'field')
        @if(filled($item['value'] ?? null))
            <table class="fields"><tr><td class="caption">{{ $item['label'] }}</td><td class="value">{{ $item['value'] }}</td></tr></table>
        @endif
    @else
        <h2>{{ $item['label'] }}</h2>
        <table class="charges">
            <thead><tr>@foreach($item['columns'] as $column)<th>{{ $column['column_label'] }}</th>@endforeach</tr></thead>
            <tbody>@foreach($item['rows'] ?? [] as $row)<tr>@foreach($item['columns'] as $column)<td>{{ $row[$column['column_name']] ?? '' }}</td>@endforeach</tr>@endforeach</tbody>
        </table>
    @endif
@endforeach
