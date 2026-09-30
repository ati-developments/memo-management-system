@if($preview = $attachment->imagePreview())
    <a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}"><img src="{{ $preview }}" alt="Attachment preview" style="display:block;max-width:100%;max-height:690pt;width:auto;height:auto"></a>
@else
    <p><a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}">Download attachment</a></p>
@endif
