@extends('layouts.app')

@section('title', 'All Memos')

@section('styles')
<style>
    .all-memos-page { max-width: none; margin: 0; padding: 24px 0 48px; color: #24324a; }
    .all-memos-header { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
    .all-memos-kicker { margin: 0 0 7px; color: #5472a7; font-size: 11px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
    .all-memos-header h1 { margin: 0; color: #1d2b44; font-family: Georgia, serif; font-size: 31px; }
    .all-memos-header p { margin: 8px 0 0; color: #687386; font-size: 14px; }
    .memo-total { padding: 10px 13px; border: 1px solid #dce5f1; border-radius: 8px; background: #f7faff; color: #45628f; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .memo-filters { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
    .status-tabs { display: flex; flex-wrap: wrap; gap: 7px; }
    .status-tab { display: inline-flex; align-items: center; gap: 7px; padding: 8px 10px; border: 1px solid #e1e6ed; border-radius: 7px; color: #667085; background: #fff; font-size: 12px; font-weight: 700; text-decoration: none; }
    .status-tab:hover { border-color: #b7c9e6; color: #2856a8; }
    .status-tab.active { border-color: #2856a8; background: #2856a8; color: #fff; }
    .tab-count { display: grid; min-width: 18px; height: 18px; place-items: center; border-radius: 9px; background: rgba(39, 86, 168, .1); font-size: 10px; }
    .status-tab.active .tab-count { background: rgba(255,255,255,.19); }
    .memo-search { position: relative; width: min(100%, 310px); }
    .memo-search input { width: 100%; height: 38px; padding: 0 35px 0 12px; border: 1px solid #dce2ea; border-radius: 7px; color: #29374d; background: #fff; font: inherit; font-size: 13px; outline: none; }
    .memo-search input:focus { border-color: #2856a8; box-shadow: 0 0 0 3px rgba(40,86,168,.12); }
    .memo-search button { position: absolute; top: 5px; right: 5px; width: 28px; height: 28px; border: 0; border-radius: 5px; color: #5472a7; background: #edf3ff; cursor: pointer; font-size: 15px; }
    .memo-register { overflow: hidden; border: 1px solid #e0e4ea; border-radius: 10px; background: #fff; box-shadow: 0 3px 12px rgba(31,49,79,.04); }
    .register-heading { display: flex; align-items: center; justify-content: space-between; padding: 17px 20px; border-bottom: 1px solid #e8ebef; }
    .register-heading h2 { margin: 0; color: #28364d; font-size: 15px; } .register-heading span { color: #768297; font-size: 12px; }
    .table-scroll { overflow-x: auto; }
    .memo-table { width: 100%; border-collapse: collapse; min-width: 920px; }
    .memo-table th { padding: 11px 16px; color: #748093; background: #fafbfc; border-bottom: 1px solid #e8ebef; font-size: 10px; font-weight: 800; letter-spacing: .06em; text-align: left; text-transform: uppercase; }
    .memo-table td { padding: 15px 16px; border-bottom: 1px solid #edf0f3; color: #435066; font-size: 13px; vertical-align: middle; } .memo-table tr:last-child td { border-bottom: 0; }
    .memo-number { color: #506886; font-family: "Courier New", monospace; font-size: 11px; font-weight: 700; } .memo-subject { display: block; max-width: 230px; overflow: hidden; color: #26364e; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .memo-author { color: #4e5e75; font-weight: 650; } .memo-department { color: #69778b; font-size: 12px; } .memo-date { color: #6f7b8d; font-size: 12px; white-space: nowrap; }
    .status { display: inline-flex; padding: 5px 8px; border-radius: 5px; font-size: 10px; font-weight: 800; text-transform: uppercase; } .status.pending { background: #fff4de; color: #9a6500; } .status.approved { background: #eaf7ef; color: #1e7747; } .status.rejected { background: #fff0f1; color: #b83442; } .status.draft { background: #eef1f5; color: #617087; }
    .approval-summary { color: #627086; font-size: 12px; white-space: nowrap; } .approval-summary strong { color: #34445d; }
    .all-memos-empty { padding: 65px 25px; text-align: center; } .empty-mark { display: grid; width: 48px; height: 48px; place-items: center; margin: 0 auto 14px; border-radius: 50%; background: #eef3fb; color: #5472a7; font-size: 21px; } .all-memos-empty h2 { margin: 0 0 7px; color: #2c3950; font-family: Georgia, serif; font-size: 20px; } .all-memos-empty p { margin: 0; color: #748093; font-size: 13px; }
    .pagination-wrap { padding: 17px 20px; border-top: 1px solid #e8ebef; }
    .pagination-wrap nav > div:first-child { display: none; }
    .pagination-wrap nav > div:last-child { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .pagination-wrap nav > div:last-child > div:first-child p { margin: 0; color: #748093; font-size: 12px; }
    .pagination-wrap nav > div:last-child > div:first-child p span { color: #435066; font-weight: 750; }
    .pagination-wrap nav > div:last-child > div:last-child > span { display: inline-flex; overflow: hidden; border: 1px solid #dce2ea; border-radius: 7px; box-shadow: none; }
    .pagination-wrap nav a, .pagination-wrap nav [aria-current="page"] > span, .pagination-wrap nav [aria-disabled="true"] > span { display: inline-flex; min-width: 34px; height: 33px; align-items: center; justify-content: center; padding: 0 9px; margin: 0; border: 0; border-left: 1px solid #dce2ea; color: #52627a; background: #fff; font-size: 12px; font-weight: 700; line-height: 1; text-decoration: none; }
    .pagination-wrap nav > div:last-child > div:last-child > span > :first-child { border-left: 0; }
    .pagination-wrap nav a:hover { color: #fff; background: #2856a8; }
    .pagination-wrap nav [aria-current="page"] > span { color: #fff; background: #2856a8; }
    .pagination-wrap nav [aria-disabled="true"] > span { color: #b1b9c5; background: #f7f8fa; cursor: not-allowed; }
    .pagination-wrap nav svg { width: 16px; height: 16px; }
    @media (max-width: 760px) { .all-memos-page { padding: 10px 0 35px; } .all-memos-header, .memo-filters { align-items: stretch; flex-direction: column; } .memo-total { align-self: flex-start; } .memo-search { width: 100%; } .pagination-wrap nav > div:last-child { display: none; } .pagination-wrap nav > div:first-child { display: flex; align-items: center; justify-content: space-between; gap: 10px; } .pagination-wrap nav > div:first-child a, .pagination-wrap nav > div:first-child span { min-width: auto; padding: 0 13px; border: 1px solid #dce2ea; border-radius: 6px; } }
</style>
@endsection

@section('content')
<div class="all-memos-page">
    @section('header-title')
All Memos
@endsection
@section('header-description')
Browse every memo created across the organization.
@endsection
@section('header-eyebrow')
Document register
@endsection
@section('header-actions')
<span class="app-header-total">{{ $counts['all'] }} {{ Str::plural('memo', $counts['all']) }} in total</span>
@endsection


    <div class="memo-filters">
        <nav class="status-tabs" aria-label="Filter by status">
            @foreach(['all' => 'All', 'pending' => ($memoStatusLabels['pending'] ?? 'Pending'), 'approved' => ($memoStatusLabels['approved'] ?? 'Approved'), 'draft' => ($memoStatusLabels['draft'] ?? 'Draft'), 'rejected' => 'Rejected'] as $value => $label)
                <a href="{{ route('memos.all', array_filter(['status' => $value === 'all' ? null : $value, 'search' => request('search')])) }}" class="status-tab {{ request('status', 'all') === $value ? 'active' : '' }}">
                    {{ $label }} <span class="tab-count">{{ $counts[$value] }}</span>
                </a>
            @endforeach
        </nav>
        <form method="GET" action="{{ route('memos.all') }}" class="memo-search">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search number, subject, author..." aria-label="Search all memos">
            <button type="submit" aria-label="Search">⌕</button>
        </form>
    </div>

    <section class="memo-register">
        <header class="register-heading"><h2>Memo register</h2><span>{{ $memos->total() }} {{ Str::plural('result', $memos->total()) }}</span></header>
        @if($memos->isNotEmpty())
            <div class="table-scroll">
                <table class="memo-table">
                    <thead><tr><th>Memo no.</th><th>Subject</th><th>Prepared by</th><th>Department</th><th>Date</th><th>Status</th><th>Approvals</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($memos as $memo)
                            <tr>
                                <td><span class="memo-number">{{ $memo->memo_number }}</span></td>
                                <td><span class="memo-subject" title="{{ $memo->subject }}">{{ $memo->subject ?: 'No subject' }}</span></td>
                                <td><span class="memo-author">{{ $memo->creator?->name ?? 'Unknown' }}</span></td>
                                <td><span class="memo-department">{{ $memo->department?->department_name ?? '—' }}</span></td>
                                <td><span class="memo-date">{{ $memo->created_at?->format('d M Y') ?? '—' }}</span></td>
                                <td><span class="status {{ strtolower($memo->status) }}">{{ $memoStatusLabels[$memo->status] ?? ucfirst($memo->status) }}</span></td>
                                <td><span class="approval-summary"><strong>{{ $memo->approved_approvals_count }}</strong> / {{ $memo->total_approvals_count }} signed</span></td>
                                <td><a class="status-tab" href="{{ route('memos.pdf', $memo) }}" target="_blank" rel="noopener">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($memos->hasPages())<div class="pagination-wrap">{{ $memos->links() }}</div>@endif
        @else
            <div class="all-memos-empty"><div class="empty-mark">⌕</div><h2>No memos found</h2><p>Try a different search term or status filter.</p></div>
        @endif
    </section>
</div>
@endsection
