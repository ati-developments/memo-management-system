<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $memo->memo_number }}</title>
<style>
    @page { margin: 35pt; }
    @include('memos.document-styles')
</style>
</head>
<body class="memo-document">
@include('memos.document')
</body>
</html>
