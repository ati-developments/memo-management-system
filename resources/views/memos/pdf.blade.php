<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $memo->memo_number }}</title>
<style>
    @page { margin: 35pt; }
    @page attachment { margin: 0; }
    @include('memos.document-styles')
</style>
</head>
<body class="memo-document">
@include('memos.document')
@foreach($memo->attachments as $attachment)
    <div style="page:attachment;page-break-before:always">
        @include('memos.attachment-page')
    </div>
@endforeach
</body>
</html>
