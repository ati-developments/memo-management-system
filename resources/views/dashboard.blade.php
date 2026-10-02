@extends('layouts.app')

@section('title', 'Dashboard | Memo')
@section('header-title', '')
@section('hide-page-header', 'true')

@section('styles')
<style>
    body { background: #f7f6f2; font-family:'Segoe UI',Arial,sans-serif; color: #1b2940; }
    .dash { max-width:1440px; --muted:#68768b; --line:#e4e9f1; --blue:#275ddc; }
    .dash a { text-decoration:none; }
    .dash a:focus-visible { outline:3px solid #82aaff; outline-offset:4px; }
    .dash-topline { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; color:var(--muted); font-size:11px; }
    .dash-account { display:flex; align-items:center; gap:14px; margin-left:auto; }
    .dash-account .profile-link { flex-shrink:0; }
    .dash-eyebrow { font-size:11px; font-weight:700; letter-spacing:1.6px; text-transform:uppercase; }
    .dash-header { display:flex; justify-content:space-between; align-items:center; gap:24px; padding-bottom:16px; margin-bottom:14px; border-bottom:1px solid #e1e3e8; }
    .dash h1 { margin:0 0 6px; font-size:clamp(24px,2vw,30px); font-weight:650; letter-spacing:-1px; overflow-wrap:anywhere; }
    .dash-subtitle { margin:0; color:var(--muted); font-size:13px; line-height:1.5; }
    .dash-button { display:inline-flex; align-items:center; justify-content:center; gap:8px; flex-shrink:0; padding:10px 14px; border:1px solid #2153ca; border-radius:8px; background:var(--blue); color:white; font-size:12px; font-weight:600; box-shadow:0 3px 8px #275ddc18; }
    .dash-button:hover { background:#204fc0; }
    .dash-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:14px; }
    .dash-stat { display:block; padding:15px 16px; border:1px solid var(--line); border-radius:10px; background:white; color:inherit; transition:border-color .15s,box-shadow .15s; }
    .dash-stat:hover { border-color:#a8bce9; box-shadow:0 5px 18px #21375b09; }
    .dash-stat-top { display:flex; justify-content:space-between; align-items:center; gap:8px; font-size:12px; font-weight:600; color:#526177; }
    .dash-icon { display:grid; place-items:center; width:30px; height:30px; flex-shrink:0; border-radius:8px; background:#edf2ff; color:#3763c2; }
    .dash-icon svg { width:16px; height:16px; }
    .dash-stat strong { display:block; margin:10px 0 4px; font-size:27px; font-weight:650; letter-spacing:-.8px; font-variant-numeric:tabular-nums; }
    .dash-stat small { color:var(--muted); font-size:11px; line-height:1.4; }
    .dash-stat.amber .dash-icon { background:#fff4df; color:#a66a0c; }
    .dash-stat.green .dash-icon { background:#e8f6ee; color:#27805c; }
    .dash-stat.action { background:#edf3ff; border-color:#d1dffc; }
    .dash-grid { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(280px,1fr); gap:14px; align-items:start; }
    .dash-panel { background:white; border:1px solid var(--line); border-radius:11px; overflow:hidden; min-width:0; }
    .dash-panel-header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 18px; border-bottom:1px solid var(--line); }
    .dash h2 { margin:0; font-size:15px; font-weight:650; letter-spacing:-.2px; }
    .dash-panel-header p { margin:4px 0 0; font-size:11px; color:var(--muted); line-height:1.4; }
    .dash-link { color:var(--blue); font-size:12px; font-weight:600; }
    .dash-link:hover { text-decoration:underline; }
    .dash-panel-header > .dash-link { white-space:nowrap; }
    .dash-memo { display:flex; align-items:center; gap:11px; padding:14px 18px; border-bottom:1px solid #edf0f5; color:inherit; }
    .dash-memo:last-child { border-bottom:0; }
    .dash-memo:hover { background:#f8faff; }
    .dash-memo-copy { min-width:0; flex:1; }
    .dash-memo-title { display:block; font-size:13px; font-weight:600; line-height:1.4; overflow-wrap:anywhere; }
    .dash-meta { display:flex; flex-wrap:wrap; gap:4px 9px; margin-top:4px; color:var(--muted); font-size:11px; line-height:1.4; overflow-wrap:anywhere; }
    .dash-status { display:inline-flex; align-items:center; gap:5px; padding:5px 9px; border-radius:6px; background:#eff2f6; color:#58667a; font-size:10px; font-weight:600; white-space:nowrap; }
    .dash-status::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
    .dash-status.pending { background:#fff4de; color:#94620d; }
    .dash-status.approved { background:#e8f6ee; color:#23764e; }
    .dash-status.rejected { background:#fff0f0; color:#b13e4d; }
    .dash-count { padding:5px 9px; border-radius:7px; background:#edf3ff; color:var(--blue); font-size:12px; font-weight:700; }
    .dash-approval { padding:14px 18px; border-bottom:1px solid #edf0f5; }
    .dash-approval-bottom { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:8px; }
    .dash-awaiting { font-size:11px; color:#94620d; }
    .dash-panel-footer { display:block; padding:13px 18px; text-align:center; background:#fbfcfe; }
    .dash-empty { padding:30px 18px; text-align:center; }
    .dash-empty .dash-icon { margin:0 auto 11px; width:38px; height:38px; }
    .dash-empty h3 { margin:0 0 6px; font-size:14px; }
    .dash-empty p { margin:0 auto 13px; max-width:290px; color:var(--muted); font-size:11px; line-height:1.6; }
    .dash-shortcuts { margin-top:18px; }
    .dash-shortcuts > h2 { margin-bottom:10px; font-size:14px; }
    .dash-shortcut-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
    .dash-shortcut { display:flex; align-items:center; gap:11px; padding:14px; background:#fff; border:1px solid var(--line); border-radius:10px; color:inherit; }
    .dash-shortcut:hover { border-color:#a8bce9; }
    .dash-shortcut strong { display:block; font-size:13px; }
    .dash-shortcut small { display:block; margin-top:4px; color:var(--muted); font-size:11px; line-height:1.4; }
    .dash-arrow { margin-left:auto; color:#74839b; }
    @media(max-width:1150px) { .dash-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } .dash-grid { grid-template-columns:1fr; } .dash-shortcut-grid { grid-template-columns:1fr; } }
    @media(max-width:700px) { .main { padding:24px 16px; } .dash-header { align-items:flex-start; flex-direction:column; } .dash-topline { flex-wrap:wrap; margin-bottom:22px; } .dash-stats { gap:10px; } .dash-stat { padding:15px; } .dash-stat-top { flex-wrap:wrap-reverse; } .dash-panel-header,.dash-memo,.dash-approval { padding:18px 16px; } .dash-memo { flex-wrap:wrap; } .dash-memo > .dash-icon { display:none; } .dash-memo-copy { flex-basis:100%; } }
    @media(max-width:380px) { .dash-stats { grid-template-columns:1fr; } }
    @media(prefers-reduced-motion:reduce) { .dash * { transition:none !important; } }
</style>
@endsection

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $stats = [
        ['label' => 'My memos', 'value' => $myMemos, 'description' => 'Total memos you have created', 'class' => '', 'icon' => 'document', 'url' => route('memos.my')],
        ['label' => 'Pending approval', 'value' => $pendingApproval, 'description' => 'Your memos awaiting approval', 'class' => 'amber', 'icon' => 'clock', 'url' => route('memos.my', ['status' => 'pending'])],
        ['label' => 'Approved', 'value' => $approved, 'description' => 'Your approved memos', 'class' => 'green', 'icon' => 'check', 'url' => route('memos.my', ['status' => 'approved'])],
        ['label' => 'Needs my action', 'value' => $needsMyAction, 'description' => 'Assigned to you for review', 'class' => 'action', 'icon' => 'review', 'url' => route('approvals.index')],
    ];
@endphp
<div class="dash">
  
    
    <!-- <header class="dash-header">
        <div>
            
        </div>
        <a href="{{ route('memos.new') }}" class="dash-button"><span aria-hidden="true">+</span> New memo</a>
    </header> -->

    <section class="dash-stats" aria-label="Memo overview">
        @foreach($stats as $stat)
            <a class="dash-stat {{ $stat['class'] }}" href="{{ $stat['url'] }}">
                <div class="dash-stat-top"><span>{{ $stat['label'] }}</span><span class="dash-icon">@include('dashboard.icon', ['icon' => $stat['icon']])</span></div>
                <strong>{{ number_format($stat['value']) }}</strong><small>{{ $stat['description'] }}</small>
            </a>
        @endforeach
    </section>
    <div class="dash-grid">
        <section class="dash-panel" aria-labelledby="recent-heading">
            <div class="dash-panel-header"><div><h2 id="recent-heading">Recent memos</h2><p>Your latest memos, all in one place.</p></div><a class="dash-link" href="{{ route('memos.my') }}">View all &rarr;</a></div>
            @forelse($recentMemos as $memo)
                <a class="dash-memo" href="{{ route('memos.show', $memo) }}">
                    <span class="dash-icon">@include('dashboard.icon', ['icon' => 'document'])</span>
                    <div class="dash-memo-copy"><span class="dash-memo-title">{{ $memo->subject }}</span><div class="dash-meta"><span>{{ $memo->memo_number }}</span><span>{{ $memo->created_at->format('M j, Y') }}</span></div></div>
                    <span class="dash-status {{ in_array($memo->status, ['pending', 'approved', 'rejected', 'draft']) ? $memo->status : '' }}">{{ ucfirst($memo->status) }}</span>
                </a>
            @empty
                <div class="dash-empty"><span class="dash-icon">@include('dashboard.icon', ['icon' => 'document'])</span><h3>Your workspace starts here</h3><p>Create your first memo to start tracking its progress and approvals.</p><a class="dash-link" href="{{ route('memos.new') }}">Create a memo &rarr;</a></div>
            @endforelse
        </section>
        <section class="dash-panel" aria-labelledby="approval-heading">
            <div class="dash-panel-header"><div><h2 id="approval-heading">Needs your approval</h2><p>Memos waiting for your review.</p></div><span class="dash-count">{{ number_format($needsMyAction) }}</span></div>
            @forelse($approvalQueue as $approval)
                <div class="dash-approval">
                    <span class="dash-memo-title">{{ $approval->memo?->subject ?? 'Memo unavailable' }}</span>
                    <div class="dash-meta">From {{ $approval->memo?->creator?->name ?? 'Unknown' }}</div>
                    <div class="dash-approval-bottom"><span class="dash-awaiting">Awaiting review</span><a class="dash-link" href="{{ route('approvals.review', $approval) }}" aria-label="Review {{ $approval->memo?->subject ?? 'memo' }}">Review &rarr;</a></div>
                </div>
            @empty
                <div class="dash-empty"><span class="dash-icon">@include('dashboard.icon', ['icon' => 'check'])</span><h3>You are all caught up</h3><p>No memos need your approval right now. New requests will appear here.</p></div>
            @endforelse
            <a class="dash-panel-footer dash-link" href="{{ route('approvals.index') }}">Open approvals queue &rarr;</a>
        </section>
    </div>
    <section class="dash-shortcuts" aria-labelledby="shortcuts-heading">
        <h2 id="shortcuts-heading">Quick access</h2>
        <div class="dash-shortcut-grid">
            <a class="dash-shortcut" href="{{ route('templates.index') }}"><span class="dash-icon">@include('dashboard.icon', ['icon' => 'grid'])</span><span><strong>Browse templates</strong><small>Find a starting point for your memo</small></span><span class="dash-arrow" aria-hidden="true">&rarr;</span></a>
            <a class="dash-shortcut" href="{{ route('memos.my', ['status' => 'draft']) }}"><span class="dash-icon">@include('dashboard.icon', ['icon' => 'document'])</span><span><strong>Continue a draft</strong><small>Pick up where you left off</small></span><span class="dash-arrow" aria-hidden="true">&rarr;</span></a>
            <a class="dash-shortcut" href="{{ route('memos.all') }}"><span class="dash-icon">@include('dashboard.icon', ['icon' => 'review'])</span><span><strong>All memos</strong><small>Explore your organization's memos</small></span><span class="dash-arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </section>
</div>
@endsection
