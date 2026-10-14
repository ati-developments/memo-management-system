<table style="margin-bottom:18pt; width:100%;"><tr>
    <td style="vertical-align:top;"><h1 style="font-size:20pt; color:#111; margin:0 0 12pt;">Memo : &nbsp; {{ $memo->department?->department_name }}</h1></td>
    <td style="width:150pt; text-align:right; vertical-align:top;">@if($brandImage ?? null)<img src="{{ $brandImage }}" alt="Amana Takaful Insurance" style="width:140pt; height:auto;">@endif</td>
</tr></table>
<table style="margin-bottom:18pt; font-size:9pt;">
    @foreach(['To' => $values->get('to'), 'From' => $values->get('from') ?: $memo->creator?->name, 'Through' => $values->get('through'), 'Subject' => $memo->subject, 'Date' => $values->get('date') ?: $memo->created_at?->format('Y-m-d')] as $label => $value)
        <tr><td style="width:65pt; padding:5pt 0; vertical-align:top; font-weight:bold;">{{ $label }}</td><td style="width:16pt; vertical-align:top; padding:5pt 0;">:</td><td style="padding:5pt 0; white-space:pre-line;">{{ $value }}</td></tr>
    @endforeach
</table>
<div class="freeform-body" style="font-size:9pt; line-height:1.5; overflow-wrap:anywhere;">{!! \App\Support\MemoTextFormatting::sanitize($memo->content ?: '<p></p>') !!}</div>
