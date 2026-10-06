@extends('layouts.app')
@section('title', 'View Memo')
@section('content')
<div class="memo-details-page" style="margin-block:24px">
    @section('header-title')
{{ $memo->memo_number }}
@endsection
@section('header-description')
View, download, or print your memo.
@endsection
@section('header-eyebrow')
Memo details
@endsection
@section('header-back')
<a href="{{ route('memos.my') }}">&larr; Back to Memos</a>
@endsection
@section('header-actions')
@if(in_array(strtolower((string) auth()->user()?->role?->role_name), ['admin', 'administrator'], true))<a href="{{ route('memos.all') }}" class="app-header-button secondary">All memos</a>@endif
@if((int) $memo->created_by === (int) auth()->id() && $memo->status === 'draft')
<a href="{{ route('memos.edit', $memo) }}" class="app-header-button secondary">Edit draft</a>
@endif
<a href="{{ route('memos.pdf', [$memo, 'download' => 1]) }}" class="app-header-button">Download PDF</a>
<a href="{{ route('memos.pdf', $memo) }}" class="app-header-button secondary" target="_blank" rel="noopener">Open PDF / Print</a>
@endsection

    <p>Use the PDF toolbar to print or save this memo.</p>
    <iframe src="{{ route('memos.pdf', $memo) }}" title="PDF of {{ $memo->memo_number }}" style="width:100%;height:80vh;border:1px solid #d0d5dd;border-radius:8px;background:#eee"></iframe>
    @if($memo->attachments->isNotEmpty())
        <section aria-labelledby="memo-attachments-title" style="margin-top:24px;padding:20px;background:#fff;border:1px solid #d0d5dd;border-radius:8px">
            <h2 id="memo-attachments-title">Attachments</h2>
            <ul>
                @foreach($memo->attachments as $attachment)
                    <li><a href="{{ route('memos.attachments.download', [$memo, $attachment]) }}">{{ $attachment->original_name }}</a> <small>({{ number_format($attachment->size / 1024, 1) }} KB)</small></li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
