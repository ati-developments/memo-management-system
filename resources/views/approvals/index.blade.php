@extends('layouts.app')

@section('title', 'Approvals')

@section('styles')
<style>
    .approvals-page { max-width: none; margin: 0; padding: 28px 0 54px; color: #29374d; }
    .approvals-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
    .approvals-kicker { margin: 0 0 7px; color: #5472a7; font-size: 11px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .approvals-header h1 { margin: 0; color: #1d2b44; font-family: Georgia, serif; font-size: 31px; }
    .approvals-header p { margin: 8px 0 0; color: #687386; font-size: 14px; }
    .pending-total { display: flex; align-items: center; gap: 11px; min-width: 175px; padding: 12px 14px; border: 1px solid #f0d7a4; border-radius: 9px; background: #fffbf2; }
    .pending-total-icon { display: grid; width: 28px; height: 28px; place-items: center; border-radius: 50%; color: #a56c00; background: #fff0cd; font-size: 15px; font-weight: 800; }
    .pending-total strong, .pending-total span { display: block; } .pending-total strong { color: #694400; font-size: 13px; } .pending-total span { margin-top: 2px; color: #8b6a32; font-size: 11px; }
    .approval-tools { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 16px; }
    .approval-tabs { display: flex; flex-wrap: wrap; gap: 7px; }
    .approval-tab { display: inline-flex; align-items: center; gap: 7px; padding: 8px 10px; border: 1px solid #e1e6ed; border-radius: 7px; color: #667085; background: #fff; font-size: 12px; font-weight: 700; text-decoration: none; }
    .approval-tab:hover { border-color: #b7c9e6; color: #2856a8; } .approval-tab.active { border-color: #2856a8; background: #2856a8; color: #fff; }
    .tab-count { display: grid; min-width: 18px; height: 18px; place-items: center; border-radius: 9px; background: rgba(40, 86, 168, .1); font-size: 10px; } .approval-tab.active .tab-count { background: rgba(255,255,255,.2); }
    .approval-search { width: min(100%, 300px); } .approval-search input { width: 100%; height: 38px; padding: 0 12px; border: 1px solid #dce2ea; border-radius: 7px; color: #29374d; background: #fff; font: inherit; font-size: 13px; outline: none; } .approval-search input:focus { border-color: #2856a8; box-shadow: 0 0 0 3px rgba(40,86,168,.12); }
    .approval-register { overflow: hidden; border: 1px solid #e0e4ea; border-radius: 10px; background: #fff; box-shadow: 0 3px 12px rgba(31,49,79,.04); }
    .register-heading { display: flex; align-items: center; justify-content: space-between; padding: 17px 20px; border-bottom: 1px solid #e8ebef; } .register-heading h2 { margin: 0; color: #28364d; font-size: 15px; } .register-heading span { color: #768297; font-size: 12px; }
    .table-scroll { overflow-x: auto; } .approval-table { width: 100%; min-width: 1050px; border-collapse: collapse; } .approval-table th { padding: 11px 16px; color: #748093; background: #fafbfc; border-bottom: 1px solid #e8ebef; font-size: 10px; font-weight: 800; letter-spacing: .06em; text-align: left; text-transform: uppercase; } .approval-table td { padding: 14px 16px; border-bottom: 1px solid #edf0f3; color: #435066; font-size: 13px; vertical-align: middle; } .approval-table tbody tr:last-child td { border-bottom: 0; } .approval-table tbody tr:hover { background: #fbfcff; }
    .memo-number { color: #506886; font-family: "Courier New", monospace; font-size: 11px; font-weight: 700; white-space: nowrap; } .memo-subject { display: block; max-width: 230px; overflow: hidden; color: #26364e; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; } .memo-template { display: block; margin-top: 4px; color: #7a8799; font-size: 11px; } .muted { color: #69778b; font-size: 12px; white-space: nowrap; }
    .status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 8px; border-radius: 5px; font-size: 10px; font-weight: 800; text-transform: uppercase; } .status::before { width: 6px; height: 6px; border-radius: 50%; background: currentColor; content: ""; } .status.pending { color: #9a6500; background: #fff4de; } .status.approved { color: #1e7747; background: #eaf7ef; } .status.rejected { color: #b83442; background: #fff0f1; }
    .progress { display: flex; align-items: center; gap: 7px; min-width: 115px; } .progress-track { width: 58px; height: 6px; overflow: hidden; border-radius: 10px; background: #e8edf3; } .progress-track span { display: block; height: 100%; border-radius: inherit; background: #3e7bdb; } .progress-label { color: #64748b; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .step-label { color: #4e5e75; font-size: 12px; white-space: nowrap; } .step-label strong { color: #29374d; } .review-button { display: inline-flex; align-items: center; justify-content: center; padding: 8px 11px; border-radius: 6px; color: #fff; background: #2856a8; font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap; } .review-button:hover { background: #1f478e; } .waiting-label { color: #8b6a32; font-size: 12px; white-space: nowrap; } .view-link { color: #2856a8; font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .alert { margin-bottom: 16px; padding: 12px 14px; border-radius: 8px; font-size: 13px; } .alert.success { color: #17633a; border: 1px solid #bce1ca; background: #effaf3; } .alert.error { color: #a92d3a; border: 1px solid #f0c3ca; background: #fff3f4; }
    .empty-state { padding: 64px 25px; text-align: center; } .empty-icon { display: grid; width: 48px; height: 48px; place-items: center; margin: 0 auto 14px; border-radius: 50%; color: #5472a7; background: #eef3fb; font-size: 22px; } .empty-state h2 { margin: 0 0 7px; color: #2c3950; font-family: Georgia, serif; font-size: 20px; } .empty-state p { max-width: 410px; margin: 0 auto; color: #748093; font-size: 13px; line-height: 1.55; }
    .pagination-wrap { padding: 17px 20px; border-top: 1px solid #e8ebef; } .pagination-wrap nav > div:first-child { display: none; } .pagination-wrap nav > div:last-child { display: flex; align-items: center; justify-content: space-between; gap: 16px; } .pagination-wrap nav > div:last-child > div:first-child p { margin: 0; color: #748093; font-size: 12px; } .pagination-wrap nav > div:last-child > div:first-child p span { color: #435066; font-weight: 750; } .pagination-wrap nav > div:last-child > div:last-child > span { display: inline-flex; overflow: hidden; border: 1px solid #dce2ea; border-radius: 7px; } .pagination-wrap nav a, .pagination-wrap nav [aria-current="page"] > span, .pagination-wrap nav [aria-disabled="true"] > span { display: inline-flex; min-width: 34px; height: 33px; align-items: center; justify-content: center; padding: 0 9px; margin: 0; border: 0; border-left: 1px solid #dce2ea; color: #52627a; background: #fff; font-size: 12px; font-weight: 700; line-height: 1; text-decoration: none; } .pagination-wrap nav > div:last-child > div:last-child > span > :first-child { border-left: 0; } .pagination-wrap nav a:hover, .pagination-wrap nav [aria-current="page"] > span { color: #fff; background: #2856a8; } .pagination-wrap nav svg { width: 16px; height: 16px; }
    @media (max-width: 760px) { .approvals-page { padding: 12px 0 38px; } .approvals-header, .approval-tools { align-items: stretch; flex-direction: column; } .pending-total { align-self: flex-start; } .approval-search { width: 100%; } .pagination-wrap nav > div:last-child { display: none; } .pagination-wrap nav > div:first-child { display: flex; justify-content: space-between; gap: 10px; } }
</style>
@endsection

@section('content')
<div class="approvals-page">
    @section('header-title')
Approvals
@endsection
@section('header-description')
Review and track memo requests assigned to you.
@endsection
@section('header-eyebrow')
Memo approvals
@endsection
@section('header-actions')
<span class="app-header-total">{{ $counts['pending'] }} awaiting {{ Str::plural('decision', $counts['pending']) }}</span>
@endsection

    @if(session('success'))<div class="alert success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert error" role="alert">{{ session('error') }}</div>@endif
    <div class="approval-tools">
            <form method="GET" action="{{ route('approvals.index') }}" class="approval-search">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search memo number or subject" aria-label="Search approvals">
            </form>
        </div>
    <section class="approval-register"><header class="register-heading"><h2>Approval register</h2><span>{{ $memos->total() }} {{ Str::plural('request', $memos->total()) }}</span></header>
        @if($memos->isNotEmpty())
            <div class="table-scroll"><table class="approval-table"><thead><tr><th>Memo</th><th>Submitted by</th><th>Submitted</th><th>Your status</th><th>Workflow progress</th><th>Your step</th><th>Action</th></tr></thead><tbody>
                @foreach($memos as $memo)
                    @php
                        $approvalChain = $memo->approvals->sortBy(fn ($approval) => $approval->chain_order)->values();
                        $myApproval = $approvalChain->first(fn ($approval) => $approval->approver_id === auth()->id());
                        $myStatus = $myApproval?->action ?? 'pending';
                        $stepIndex = $myApproval ? $approvalChain->search(fn ($approval) => $approval->id === $myApproval->id) : 0;
                        $currentSequentialApproval = $approvalChain->firstWhere('action', 'pending');
                        $isSequential = $myApproval?->workflow?->approval_type === 'sequential';
                        $canReview = $myApproval && $myStatus === 'pending' && (!$isSequential || $currentSequentialApproval?->id === $myApproval->id);
                        $completed = $approvalChain->where('action', 'approved')->count();
                        $progress = $approvalChain->count() ? (int) round(($completed / $approvalChain->count()) * 100) : 0;
                    @endphp
                    <tr><td><span class="memo-number">{{ $memo->memo_number }}</span><span class="memo-subject" title="{{ $memo->subject }}">{{ $memo->subject ?: 'Untitled memo' }}</span><span class="memo-template">{{ $memo->template?->template_name ?? 'Memo' }}</span></td><td class="muted">{{ $memo->creator?->name ?? 'Unknown user' }}</td><td class="muted">{{ $memo->submitted_at?->format('d M Y') ?? $memo->created_at?->format('d M Y') }}</td><td><span class="status {{ $myStatus }}">{{ ucfirst($myStatus) }}</span></td><td><div class="progress"><div class="progress-track"><span style="width: {{ $progress }}%"></span></div><span class="progress-label">{{ $completed }}/{{ $approvalChain->count() }}</span></div></td><td><span class="step-label"><strong>{{ $stepIndex + 1 }}</strong> of {{ $approvalChain->count() }}</span></td><td>@if($canReview)<a href="{{ route('approvals.review', $myApproval) }}" class="review-button">Review &amp; sign</a>@elseif($myStatus === 'pending')<span class="waiting-label">Waiting for prior step</span>@else<a href="{{ route('approvals.review', $myApproval) }}" class="view-link">View memo</a>@endif</td></tr>
                @endforeach
            </tbody></table></div>
            @if($memos->hasPages())<div class="pagination-wrap">{{ $memos->links() }}</div>@endif
        @else
            <div class="empty-state"><div class="empty-icon">✓</div><h2>No approvals found</h2><p>There are no approval requests matching your current filter. New requests will appear here when they are assigned to you.</p></div>
        @endif
    </section>
</div>
@endsection
