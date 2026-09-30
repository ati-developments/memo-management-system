<div data-text-position="{{ $position }}">
@foreach(($memo->text_blocks ?? []) as $textBlock)
    @if($textBlock['position'] === $position)
        <div style="white-space:pre-wrap;overflow-wrap:break-word;margin:12px 0">@if(!empty($textBlock['html'])){!! \App\Support\MemoTextFormatting::sanitize($textBlock['html']) !!}@else{{ $textBlock['text'] }}@endif</div>
    @endif
@endforeach
</div>
