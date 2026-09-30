@if($preview = $attachment->imagePreview())
    <a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}"><img src="{{ $preview }}" alt="Attachment preview" style="display:block;width:100%;height:auto"></a>
@else
    <p><a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}">Download attachment</a></p>
@endif
