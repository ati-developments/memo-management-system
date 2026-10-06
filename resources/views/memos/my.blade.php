@extends('layouts.app')

@section('title', 'Memos')

@section('content')

<div class="my-memos-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    @section('header-title')
                Memos
@endsection
@section('header-description')
{{ $isAdmin ? 'View and manage all memos across the organization.' : 'View memos you have prepared or are assigned to approve.' }}
@endsection
@section('header-eyebrow')
Document management
@endsection
@section('header-actions')
<a href="{{ route('memos.new') }}" class="app-header-button">+ New memo</a>
@endsection



    {{-- =========================================================
         STATUS FILTER + SEARCH
    ========================================================== --}}

    <div class="toolbar">

        <div class="status-filters">

            <a
                href="{{ route('memos.my') }}"
                class="status-filter {{ request('status', 'all') === 'all' ? 'active' : '' }}"
            >
                <span>All</span>
                <span class="filter-count">
                    {{ $counts['all'] }}
                </span>
            </a>


            <a
                href="{{ route('memos.my', ['status' => 'pending']) }}"
                class="status-filter {{ request('status') === 'pending' ? 'active' : '' }}"
            >
                <span>{{ $memoStatusLabels['pending'] ?? 'Pending' }}</span>
                <span class="filter-count">
                    {{ $counts['pending'] }}
                </span>
            </a>


            <a
                href="{{ route('memos.my', ['status' => 'approved']) }}"
                class="status-filter {{ request('status') === 'approved' ? 'active' : '' }}"
            >
                <span>{{ $memoStatusLabels['approved'] ?? 'Approved' }}</span>
                <span class="filter-count">
                    {{ $counts['approved'] }}
                </span>
            </a>


            <a
                href="{{ route('memos.my', ['status' => 'draft']) }}"
                class="status-filter {{ request('status') === 'draft' ? 'active' : '' }}"
            >
                <span>{{ $memoStatusLabels['draft'] ?? 'Draft' }}</span>
                <span class="filter-count">
                    {{ $counts['draft'] }}
                </span>
            </a>


            <a
                href="{{ route('memos.my', ['status' => 'rejected']) }}"
                class="status-filter {{ request('status') === 'rejected' ? 'active' : '' }}"
            >
                <span>Rejected</span>
                <span class="filter-count">
                    {{ $counts['rejected'] }}
                </span>
            </a>

        </div>


        {{-- Search --}}

        <form
            method="GET"
            action="{{ route('memos.my') }}"
            class="search-form"
        >

            @if(request('status'))
                <input
                    type="hidden"
                    name="status"
                    value="{{ request('status') }}"
                >
            @endif

            <div class="search-box">

                <span class="search-icon">
                    ⌕
                </span>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search memos..."
                >

                @if(request('search'))

                    <a
                        href="{{ route('memos.my', ['status' => request('status')]) }}"
                        class="clear-search"
                        title="Clear search"
                    >
                        ×
                    </a>

                @endif

            </div>

        </form>

    </div>


    {{-- =========================================================
         MEMO TABLE
    ========================================================== --}}

    <div class="memo-table-card">

        <div class="table-header">

            <div>

                <h2>
                    Memo Records
                </h2>

                    <p>
                    {{ $isAdmin ? 'All memos across the organization' : 'Memos you have prepared or are assigned to approve' }}
                </p>

            </div>

            <div class="record-count">

                {{ $memos->total() }}

                {{ $memos->total() === 1 ? 'memo' : 'memos' }}

            </div>

        </div>


        @if($memos->count() > 0)

            <div class="table-wrapper">

                <table class="memo-table">

                    <thead>

                        <tr>

                            <th>
                                MEMO NO.
                            </th>

                            <th>
                                SUBJECT
                            </th>

                            <th>
                                TEMPLATE
                            </th>

                            <th>
                                TO
                            </th>

                            <th>
                                DATE
                            </th>

                            <th>
                                STATUS
                            </th>

                            <th>
                                ACTION
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($memos as $memo)

                            <tr>

                                {{-- Memo Number --}}

                                <td>

                                    <div class="memo-number">

                                        {{ $memo->memo_number }}

                                    </div>

                                </td>


                                {{-- Subject --}}

                                <td>

                                    <div class="subject-cell">

                                        <div class="subject-title">

                                            {{ $memo->subject ?: 'No subject' }}

                                        </div>

                                    </div>

                                </td>


                                {{-- Template --}}

                                <td>

                                    @if($memo->template)

                                        <span class="template-badge">

                                            {{ $memo->template->template_name }}

                                        </span>

                                    @else

                                        <span class="muted-text">
                                            No Template
                                        </span>

                                    @endif

                                </td>


                                {{-- Department --}}

                                <td>

                                    <div class="department-cell">

                                        @if($memo->department)

                                            {{ $memo->department->department_name }}

                                        @else

                                            <span class="muted-text">
                                                —
                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- Date --}}

                                <td>

                                    <div class="date-cell">

                                        {{ $memo->created_at->format('Y-m-d') }}

                                    </div>

                                </td>


                                {{-- Status --}}

                                <td>

                                    @php

                                        $status = strtolower(
                                            $memo->status ?? 'draft'
                                        );

                                    @endphp


                                    @if($status === 'pending')

                                        <div class="status-wrapper">

                                            <span class="status-badge pending">
                                                {{ $memoStatusLabels['pending'] ?? 'Pending' }}
                                            </span>

                                        </div>

                                    @elseif($status === 'approved')

                                        <div class="status-wrapper">

                                            <span class="status-badge approved">
                                                {{ $memoStatusLabels['approved'] ?? 'Approved' }}
                                            </span>

                                        </div>

                                    @elseif($status === 'rejected')

                                        <div class="status-wrapper">

                                            <span class="status-badge rejected">
                                                Rejected
                                            </span>

                                        </div>

                                    @else

                                        <div class="status-wrapper">

                                            <span class="status-badge draft">
                                                {{ $memoStatusLabels['draft'] ?? 'Draft' }}
                                            </span>

                                        </div>

                                    @endif

                                </td>


                                {{-- Action --}}

                                <td>

                                    <div class="action-buttons">

                                        {{-- View the saved memo document --}}

                                        <a
                                            href="{{ route('memos.pdf', $memo) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="view-button"
                                        >
                                            View
                                            <span>→</span>
                                        </a>


                                        @if($memo->template_id && ($isAdmin || ($status === 'draft' && (int) $memo->created_by === (int) auth()->id())))

                                            <a
                                                href="{{ route('memos.create.template', ['template' => $memo->template_id, 'memo_id' => $memo->id]) }}"
                                                class="edit-button"
                                            >
                                                {{ $isAdmin ? 'Update' : 'Edit' }}
                                            </a>

                                        @endif
                                        @if($isAdmin)
                                            <form method="POST" action="{{ route('admin.memos.destroy', $memo) }}" onsubmit="return confirm('Delete memo {{ $memo->memo_number }}? This cannot be undone.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="edit-button">Delete</button>
                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                 PAGINATION
            ================================================== --}}

            @if($memos->hasPages())

                <div class="pagination-area">

                    <div class="pagination-info">

                        Showing

                        <strong>
                            {{ $memos->firstItem() }}
                        </strong>

                        to

                        <strong>
                            {{ $memos->lastItem() }}
                        </strong>

                        of

                        <strong>
                            {{ $memos->total() }}
                        </strong>

                    </div>


                    <div class="pagination-links">

                        {{ $memos->links() }}

                    </div>

                </div>

            @endif


        @else

            {{-- =================================================
                 EMPTY STATE
            ================================================== --}}

            <div class="empty-state">

                <div class="empty-icon">
                    📄
                </div>

                <h3>
                    No memos found
                </h3>

                <p>

                    @if(request('search'))

                        No memos match your search.

                    @elseif(request('status'))

                        You don't have any
                        {{ $memoStatusLabels[request('status')] ?? ucfirst(request('status')) }}
                        memos.

                    @else

                        No memos are available for you yet.

                    @endif

                </p>


                <a
                    href="{{ route('memos.create') }}"
                    class="empty-action"
                >
                    + Create New Memo
                </a>

            </div>

        @endif

    </div>

</div>


{{-- =============================================================
     CSS
============================================================= --}}

<style>

.my-memos-page {

    max-width: 1780px;

    margin: 50 auto;

    padding: 10px 10px 15px;

    color: #172033;

}


/* =============================================================
   HEADER
============================================================= */

.page-header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    margin-bottom: 28px;

}


.eyebrow {

    margin-bottom: 6px;

    color: #667085;

    font-size: 10px;

    font-weight: 700;

    letter-spacing: 1.4px;

}


.page-header h1 {

    margin: 0;

    font-size: 30px;

    line-height: 1.2;

    font-weight: 750;

    color: #182230;

}


.page-header p {

    margin: 7px 0 0;

    color: #7a8494;

    font-size: 13px;

}


.new-memo-button {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 11px 17px;

    border-radius: 8px;

    background: var(--primary);

    color: white;

    text-decoration: none;

    font-size: 13px;

    font-weight: 700;

    box-shadow: 0 4px 10px #81bd432e;

    transition: .2s ease;

}


.new-memo-button:hover {

    background: var(--primary-hover);

    transform: translateY(-1px);

}


.plus-icon {

    font-size: 17px;

    line-height: 1;

}


/* =============================================================
   TOOLBAR
============================================================= */

.toolbar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 18px;

    margin-bottom: 18px;

}


.status-filters {

    display: flex;

    align-items: center;

    gap: 4px;

    padding: 4px;

    background: #f5f6f8;

    border: 1px solid #e7e9ed;

    border-radius: 9px;

}


.status-filter {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 8px 11px;

    border-radius: 6px;

    color: #697386;

    text-decoration: none;

    font-size: 11px;

    font-weight: 600;

    white-space: nowrap;

    transition: .15s ease;

}


.status-filter:hover {

    color: #24324a;

}


.status-filter.active {

    background: #ffffff;

    color: #1d2939;

    box-shadow: 0 1px 4px rgba(16, 24, 40, .10);

}


.filter-count {

    color: #98a2b3;

    font-size: 10px;

}


.status-filter.active .filter-count {

    color: #344054;

}


/* =============================================================
   SEARCH
============================================================= */

.search-form {

    flex: 0 0 245px;

}


.search-box {

    position: relative;

    display: flex;

    align-items: center;

}


.search-box input {

    width: 100%;

    height: 38px;

    box-sizing: border-box;

    padding: 0 34px 0 35px;

    border: 1px solid #e1e4e9;

    border-radius: 8px;

    background: white;

    color: #344054;

    font-family: inherit;

    font-size: 12px;

    outline: none;

    transition: .2s ease;

}


.search-box input:focus {

    border-color: var(--primary);

    box-shadow: 0 0 0 3px #81bd4314;

}


.search-icon {

    position: absolute;

    left: 12px;

    z-index: 1;

    color: #98a2b3;

    font-size: 18px;

}


.clear-search {

    position: absolute;

    right: 11px;

    color: #98a2b3;

    text-decoration: none;

    font-size: 17px;

}


/* =============================================================
   TABLE CARD
============================================================= */

.memo-table-card {

    overflow: hidden;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    box-shadow: 0 3px 15px rgba(16, 24, 40, .045);

}


.table-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 18px 20px;

    border-bottom: 1px solid #edf0f3;

}


.table-header h2 {

    margin: 0;

    color: #1d2939;

    font-size: 14px;

    font-weight: 700;

}


.table-header p {

    margin: 4px 0 0;

    color: #98a2b3;

    font-size: 11px;

}


.record-count {

    color: #667085;

    font-size: 11px;

    font-weight: 600;

}


/* =============================================================
   TABLE
============================================================= */

.table-wrapper {

    overflow-x: auto;

}


.memo-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: fixed;

}


.memo-table th {

    padding: 11px 20px;

    background: #fafbfc;

    border-bottom: 1px solid #e7e9ed;

    color: #667085;

    text-align: left;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: .65px;

    white-space: nowrap;

}


.memo-table td {

    padding: 15px 10px;

    border-bottom: 1px solid #edf0f3;

    vertical-align: middle;

    color: #344054;

    font-size: 12px;

}


.memo-table tbody tr {

    transition: background .15s ease;

}


.memo-table tbody tr:hover {

    background: var(--primary-soft);

}


.memo-table tbody tr:last-child td {

    border-bottom: none;

}


/* Column widths */

.memo-table th:nth-child(1),
.memo-table td:nth-child(1) {

    width: 15%;

}


.memo-table th:nth-child(2),
.memo-table td:nth-child(2) {

    width: 20%;

}


.memo-table th:nth-child(3),
.memo-table td:nth-child(3) {

    width: 23%;

}


.memo-table th:nth-child(4),
.memo-table td:nth-child(4) {

    width: 13%;

}


.memo-table th:nth-child(5),
.memo-table td:nth-child(5) {

    width: 12%;

}


.memo-table th:nth-child(6),
.memo-table td:nth-child(6) {

    width: 12%;

}


.memo-table th:nth-child(7),
.memo-table td:nth-child(7) {

    width: 22%;

}


/* =============================================================
   CELLS
============================================================= */

.memo-number {

    color: #344054;

    font-family: monospace;

    font-size: 11px;

    font-weight: 600;

    line-height: 1.5;

}


.subject-title {

    overflow: hidden;

    color: #1d2939;

    font-size: 12px;

    font-weight: 600;

    text-overflow: ellipsis;

    white-space: nowrap;

}


.template-badge {

    display: inline-block;

    max-width: 135px;

    padding: 5px 8px;

    overflow: hidden;

    border-radius: 5px;

    background: #f2f3f5;

    color: #667085;

    font-size: 10px;

    line-height: 1.3;

    text-overflow: ellipsis;

    vertical-align: middle;

}


.department-cell {

    overflow: hidden;

    color: #667085;

    font-size: 11px;

    text-overflow: ellipsis;

    white-space: nowrap;

}


.date-cell {

    color: #667085;

    font-size: 10px;

}


.muted-text {

    color: #a0a7b2;

}


/* =============================================================
   STATUS
============================================================= */

.status-wrapper {

    display: flex;

    align-items: center;

}


.status-badge {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 58px;

    padding: 5px 9px;

    border-radius: 20px;

    font-size: 9px;

    font-weight: 800;

}


.status-badge.pending {

    background: #fff4d8;

    color: #a15c00;

}


.status-badge.approved {

    background: #e3f7ef;

    color: #087a56;

}


.status-badge.draft {

    background: #eef0f3;

    color: #667085;

}


.status-badge.rejected {

    background: #fdeaea;

    color: #c53030;

}


/* =============================================================
   ACTIONS
============================================================= */

.action-buttons {

    display: flex;

    align-items: center;

    gap: 8px;

}


.view-button {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    color: var(--primary-link);

    text-decoration: none;

    font-size: 11px;

    font-weight: 700;

}


.view-button:hover {

    color: var(--primary-link);

}


.view-button span {

    font-size: 14px;

}


.edit-button {

    padding: 5px 8px;

    border: 1px solid #e1e4e9;

    border-radius: 5px;

    color: #667085;

    text-decoration: none;

    font-size: 10px;

    font-weight: 600;

}


.edit-button:hover {

    background: #f8f9fb;

    color: #344054;

}


/* =============================================================
   EMPTY STATE
============================================================= */

.empty-state {

    padding: 65px 20px;

    text-align: center;

}


.empty-icon {

    width: 58px;

    height: 58px;

    display: flex;

    align-items: center;

    justify-content: center;

    margin: 0 auto 15px;

    border-radius: 14px;

    background: #f2f4f7;

    font-size: 25px;

}


.empty-state h3 {

    margin: 0 0 7px;

    color: #344054;

    font-size: 15px;

}


.empty-state p {

    margin: 0 0 18px;

    color: #98a2b3;

    font-size: 12px;

}


.empty-action {

    display: inline-flex;

    padding: 9px 14px;

    border-radius: 7px;

    background: var(--primary);

    color: white;

    text-decoration: none;

    font-size: 11px;

    font-weight: 700;

}


 .pagination-area {

        display: flex;

        justify-content: space-between;

        align-items: center;

        padding: 14px 20px;

        border-top: 1px solid #edf0f3;

    }


    .pagination-info {

        color: #98a2b3;

        font-size: 10px;

    }


    .pagination-info strong {

        color: #667085;

    }


    /* Laravel pagination container */

    .pagination-links {

        display: flex;

        align-items: center;

    }


    /* Navigation */

    .pagination-links nav {

        display: flex;

        align-items: center;

    }


    /* Laravel's pagination list */

    .pagination-links nav > div {

        display: flex;

        align-items: center;

    }


    /* Hide the default "Showing..." text if Laravel renders it */

    .pagination-links nav p {

        display: none;

    }


    /* Pagination list */

    .pagination-links ul {

        display: flex;

        align-items: center;

        gap: 4px;

        margin: 0;

        padding: 0;

        list-style: none;

    }


    /* Individual list items */

    .pagination-links li {

        list-style: none;

    }


    /* Pagination links */

    .pagination-links a,
    .pagination-links span {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        min-width: 30px;

        height: 30px;

        padding: 0 8px;

        box-sizing: border-box;

        border: 1px solid #e1e4e9;

        border-radius: 6px;

        background: #ffffff;

        color: #667085;

        text-decoration: none;

        font-size: 10px;

        font-weight: 600;

        line-height: 1;

    }


    /* Hover */

    .pagination-links a:hover {

        background: var(--primary-soft);

        border-color: var(--primary-line);

        color: var(--primary-link);

    }


    /* Current page */

    .pagination-links span[aria-current="page"] {

        background: var(--primary);

        border-color: var(--primary);

        color: #ffffff;

    }


    /* Disabled previous / next */

    .pagination-links span[aria-disabled="true"] {

        background: #f8f9fb;

        color: #c1c7d0;

        border-color: #edf0f3;

        cursor: not-allowed;

    }


    /* SVG arrows generated by Laravel */

    .pagination-links svg {

        width: 13px;

        height: 13px;

    }


    /* Previous / next */

    .pagination-links a[rel="prev"],
    .pagination-links a[rel="next"] {

        min-width: 30px;

    }




    /* =========================================================
    REVIEW MEMO
    ========================================================= */

    .review-memo-page {
        max-width: 1400px;
        margin: 0 auto;
    }

    .review-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .memo-status {
        display: flex;
        align-items: center;
    }

    .memo-info-grid,
    .approval-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .info-value {
        font-size: 15px;
        font-weight: 500;
        color: #1e293b;
    }

    .memo-fields {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .memo-field {
        display: grid;
        grid-template-columns: 240px 1fr;
        border-bottom: 1px solid #eef2f7;
        padding-bottom: 14px;
    }

    .memo-field:last-child {
        border-bottom: none;
    }

    .memo-field-label {
        font-weight: 600;
        color: #475569;
    }

    .memo-field-value {
        color: #1e293b;
        white-space: pre-wrap;
    }

    .approval-history {
        display: flex;
        flex-direction: column;
    }

    .approval-history-item {
        display: flex;
        gap: 16px;
        padding: 18px 0;
        border-bottom: 1px solid #eef2f7;
    }

    .approval-history-item:last-child {
        border-bottom: none;
    }

    .approval-step-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--primary-soft);
        color: var(--primary-link);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .approval-history-content {
        flex: 1;
    }

    .approval-history-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .approval-designation {
        margin-top: 4px;
        font-size: 13px;
        color: #64748b;
    }

    .approval-date {
        margin-top: 6px;
        font-size: 12px;
        color: #64748b;
    }

    .approval-comment {
        margin-top: 10px;
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 6px;
        font-size: 13px;
        color: #475569;
    }

    .approval-action-card {
        border: 1px solid var(--primary-line);
    }

    .action-description {
        color: #64748b;
        margin-bottom: 20px;
    }

    .approval-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .reject-button,
    .approve-button {
        border: none;
        border-radius: 8px;
        padding: 11px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .reject-button {
        background: #fee2e2;
        color: #b91c1c;
    }

    .approve-button {
        background: var(--primary);
        color: #ffffff;
    }

    .reject-button:hover,
    .approve-button:hover {
        opacity: 0.9;
    }

    @media (max-width: 900px) {

        .memo-info-grid,
        .approval-info-grid {
            grid-template-columns: 1fr;
        }

        .memo-field {
            grid-template-columns: 1fr;
            gap: 6px;
        }

    }

    /* Responsive */

    @media (max-width: 700px) {

        .pagination-area {

            flex-direction: column;

            align-items: flex-start;

            gap: 12px;

        }

    }


/* =============================================================
   RESPONSIVE
============================================================= */

@media (max-width: 950px) {

    .toolbar {

        flex-direction: column;

        align-items: stretch;

    }


    .status-filters {

        overflow-x: auto;

    }


    .search-form {

        flex: none;

        width: 100%;

    }

}


@media (max-width: 700px) {

    .my-memos-page {

        padding: 20px 15px 40px;

    }


    .page-header {

        flex-direction: column;

        gap: 18px;

    }


    .new-memo-button {

        width: 100%;

    }


    .status-filter {

        padding: 8px 9px;

    }


    .memo-table {

        min-width: 950px;

    }


   
}

</style>

@endsection
