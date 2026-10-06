@extends('layouts.app')

@section('title', 'Review Memo')

@section('styles')
<style>
    .memo-review { max-width: 1200px; margin: 0 auto; padding: 5px 0 48px; }
    .review-breadcrumb { display: flex; align-items: center; gap: 8px; margin: 0 0 25px; color: #687086; font-size: 12px; }
    .review-breadcrumb a { color: #687086; text-decoration: none; }
    .review-breadcrumb a:hover { color: #2856e8; }
    .review-breadcrumb strong { color: #27324a; font-weight: 700; }
    .memo-review-grid { display: grid; grid-template-columns: minmax(0, 1fr) 274px; gap: 20px; align-items: start; }
    .memo-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 11px; margin: 0 10px 25px; }
    .status-badge { display: inline-flex; padding: 4px 9px; border-radius: 12px; background: #fff1d5; color: #bd7700; font-size: 10px; font-weight: 700; letter-spacing: .03em; }
    .memo-number { color: #505b72; font-family: 'Courier New', monospace; font-size: 11px; }
    .print-button { margin-left: auto; padding: 7px 10px; border: 0; border-radius: 6px; background: #eef0f4; color: #344158; cursor: pointer; font: inherit; font-size: 11px; font-weight: 700; text-decoration: none; }
    .print-button:hover { background: #e2e6ee; }
    @include('memos.document-styles')
    .memo-document { box-sizing: border-box; width: 90%; min-height: 1000px; padding: 36px; border: 1px solid #d0d5dd; background: #fff; }
    .memo-document table { table-layout: fixed; }
    .memo-document td, .memo-document th { overflow-wrap: anywhere; }
    .memo-document .brand img { max-width: 100%; height: auto; }
    .memo-document.attachment-document { padding: 0; min-height: 0; }
    .document-navigation { display: flex; align-items: center; justify-content: center; gap: 16px; width: 90%; margin-bottom: 12px; }
    .document-navigation button { padding: 7px 14px; border: 1px solid #d0d5dd; border-radius: 6px; background: #fff; color: #344158; cursor: pointer; }
    .document-navigation button:hover:not(:disabled) { background: #eef0f4; }
    .document-navigation button:focus-visible { outline: 2px solid #2856e8; outline-offset: 3px; }
    .document-navigation button:disabled { background: #f3f4f6; color: #737d8c; cursor: default; }
    html[data-theme=dark] .document-navigation button { background: #22314a; color: #e0e8f5; border-color: #40516c; }
    html[data-theme=dark] .document-navigation button:hover:not(:disabled) { background: #304563; }
    html[data-theme=dark] .document-navigation button:disabled { background: #172238; color: #8f9fb9; border-color: #30405a; }
    [data-document-page][hidden], .document-navigation[hidden] { display: none; }
    @media (max-width: 600px) { .memo-document { min-height: 0; padding: 20px 12px; } }
    .memo-empty { padding: 20px 0; color: #7a8392; font-size: 13px; text-align: center; }
    .review-sidebar { display: grid; gap: 15px; }
    .side-card { overflow: hidden; border: 1px solid #e0ded9; border-radius: 10px; background: #fff; }
    .action-card { border-color: #df930f; }
    .side-card-title { margin: 0; padding: 13px 15px; border-bottom: 1px solid #ebe9e5; color: #596477; font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .action-card .side-card-title { border: 0; color: #d17e00; }
    .action-content { padding: 3px 15px 14px; }
    .action-content p { margin: 0 0 3px; color: #14243c; font-size: 13px; line-height: 1.45; }
    .action-content small { display: block; margin-bottom: 14px; color: #737d8c; font-size: 11px; line-height: 1.4; }
    .approval-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .approve-button, .reject-button { min-height: 37px; border: 0; border-radius: 7px; cursor: pointer; font: inherit; font-size: 12px; font-weight: 700; }
    .approve-button { background: #238454; color: #fff; }
    .reject-button { background: #fcedeD; color: #c33535; }
    .approve-button:hover, .reject-button:hover { filter: brightness(.96); }
    .decision-form { margin: 0; }
    .reject-form { grid-column: 1 / -1; }
    .reject-note { width: 100%; min-height: 74px; margin-top: 2px; padding: 9px; border: 1px solid #e4c1c1; border-radius: 7px; color: #27334a; font: inherit; font-size: 12px; line-height: 1.4; resize: vertical; }
    .decision-error, .review-alert { margin: 0 0 15px; padding: 10px 12px; border-radius: 7px; font-size: 12px; line-height: 1.4; }
    .decision-error { border: 1px solid #efc5c5; background: #fff2f2; color: #a92c2c; }
    .review-alert.success { border: 1px solid #b9dfc8; background: #eef9f1; color: #1b7044; }
    .review-alert.error { border: 1px solid #efc5c5; background: #fff2f2; color: #a92c2c; }
    .approval-dialog { width: min(420px, calc(100% - 32px)); padding: 0; border: 0; border-radius: 12px; box-shadow: 0 18px 55px rgba(18, 31, 57, .28); }
    .approval-dialog::backdrop { background: rgba(20, 32, 55, .48); }
    .approval-dialog-content { padding: 25px; }
    .approval-dialog h2 { margin: 0 0 9px; color: #1d2b44; font-family: Georgia, serif; font-size: 22px; }
    .approval-dialog p { margin: 0; color: #647086; font-size: 13px; line-height: 1.55; }
    .approval-dialog-actions { display: flex; justify-content: end; gap: 9px; margin-top: 22px; }
    .dialog-cancel, .dialog-confirm { min-height: 36px; padding: 0 14px; border: 0; border-radius: 7px; cursor: pointer; font: inherit; font-size: 12px; font-weight: 700; }
    .dialog-cancel { background: #edf0f4; color: #465268; }
    .dialog-confirm { background: #238454; color: #fff; }
    .chain { padding: 15px; }
    .chain-item { position: relative; display: flex; gap: 11px; min-height: 48px; }
    .chain-item:not(:last-child)::after { position: absolute; top: 30px; left: 14px; width: 1px; height: 17px; background: #d8dce2; content: ''; }
    .chain-step { display: grid; flex: none; width: 29px; height: 29px; place-items: center; border-radius: 50%; background: #ebeae6; color: #687083; font-size: 12px; font-weight: 700; }
    .chain-item.current .chain-step { background: #d28600; color: #fff; }
    .chain-person { padding-top: 2px; color: #27334a; font-size: 12px; font-weight: 700; line-height: 1.25; }
    .chain-role { margin-top: 2px; color: #788292; font-size: 11px; }
    .detail-list { padding: 12px 15px 15px; }
    .detail-row { display: grid; grid-template-columns: 1fr auto; gap: 10px; padding: 4px 0; color: #778190; font-size: 11px; }
    .detail-row strong { overflow: hidden; color: #27334a; font-weight: 600; text-align: right; text-overflow: ellipsis; white-space: nowrap; }
    @media (max-width: 930px) { .memo-review-grid { grid-template-columns: minmax(0, 1fr); } .review-sidebar { grid-template-columns: repeat(2, minmax(0, 1fr)); } .action-card { grid-column: 1 / -1; } }
    @media (max-width: 700px) { .memo-review { padding-top: 0; } }
    @media (max-width: 600px) { .review-sidebar { grid-template-columns: 1fr; } .action-card { grid-column: auto; } }
</style>
@endsection

@section('content')
@php
    $memo = $approval->memo;
    $values = $memo->fieldValues->pluck('field_value', 'field_name');
    $approvals = $memo->approvals->sortBy('chain_order');
    $signatureImages = $approvals->where('action', 'approved')->filter(fn ($item) => $item->signature_path)
        ->mapWithKeys(fn ($item) => [$item->id => asset('storage/' . $item->signature_path)])->all();
    $brandImage = asset('images/Amana-Takaful-logo.jpeg');
@endphp

<div class="memo-review">
    @if(session('success'))
        <div class="review-alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="review-alert error">{{ session('error') }}</div>
    @endif
    @section('header-title')
Review {{ $memo->memo_number }}
@endsection
@section('header-description')
Review the memo details and record your approval decision.
@endsection
@section('header-eyebrow')
Memo approvals
@endsection
@section('header-back')
<a href="{{ route('approvals.index') }}">&larr; Back to approvals</a>
@endsection


    <div class="memo-review-grid">
        <main>
            <div class="memo-meta">
                <span class="status-badge">{{ ucfirst($approval->action) }}</span>
                <span class="memo-number">{{ $memo->memo_number }}</span>
                <a href="{{ route('memos.pdf', $memo) }}" class="print-button" target="_blank" rel="noopener">Print / Save PDF</a>
            </div>

            @if($memo->attachments->isNotEmpty())
                <nav class="document-navigation" aria-label="Document pages" hidden data-document-navigation>
                    <button type="button" data-previous-page aria-label="Previous page" disabled>&larr;</button>
                    <span data-page-counter aria-live="polite">Page 1 of {{ $memo->attachments->count() + 1 }}</span>
                    <button type="button" data-next-page aria-label="Next page">&rarr;</button>
                </nav>
            @endif
            <article class="memo-document" aria-label="Memo document" data-document-page>
                @include('memos.document')
            </article>
            @foreach($memo->attachments as $attachment)
                <article class="memo-document attachment-document" aria-label="Attachment page {{ $loop->iteration }}" data-document-page>
                    @include('memos.attachment-page')
                </article>
            @endforeach
        </main>

        @php($approvalChain = $memo->approvals->sortBy(fn ($item) => $item->chain_order)->values())
        <aside class="review-sidebar">
            @php($currentSequentialApproval = $approvalChain->firstWhere('action', 'pending'))
            @php($canAct = $approval->action === 'pending'
                && $memo->status === 'pending'
                && $approval->workflow
                && ($approval->workflow->approval_type !== 'sequential' || $currentSequentialApproval?->id === $approval->id))
            @if($canAct)
                <section class="side-card action-card">
                    <h2 class="side-card-title">Your approval required</h2>
                    <div class="action-content">
                        
                        <small>Review the memo content before signing.</small>
                        <div class="approval-actions">
                            <form method="POST" action="{{ route('approvals.decision', $approval) }}" class="decision-form" data-approve-form>
                                @csrf
                                <input type="hidden" name="decision" value="approved">
                                <button type="submit" class="approve-button">&check; Approve</button>
                            </form>
                            <form method="POST" action="{{ route('approvals.decision', $approval) }}" class="decision-form reject-form">
                                @csrf
                                <input type="hidden" name="decision" value="rejected">
                                <textarea name="comment" class="reject-note" placeholder="Reason for rejection (required)" required>{{ old('decision') === 'rejected' ? old('comment') : '' }}</textarea>
                                @error('comment')<div class="decision-error">{{ $message }}</div>@enderror
                                <button type="submit" class="reject-button">&times; Reject</button>
                            </form>
                        </div>
                    </div>
                </section>
            @endif

            <section class="side-card">
                <h2 class="side-card-title">Approval chain</h2>
                <div class="chain">
                    @forelse($approvalChain as $chainApproval)
                        <div class="chain-item {{ $chainApproval->id === $approval->id ? 'current' : '' }}">
                            <span class="chain-step">{{ $chainApproval->chain_order + 1 }}</span>
                            <div>
                                <div class="chain-person">{{ $chainApproval->approver->name ?? 'Unknown approver' }}</div>
                                <div class="chain-role">{{ $chainApproval->approval_role === 'prepared' ? 'Prepared by' : ($chainApproval->approver->designation ?? 'Approver') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="memo-empty">No approval chain available.</div>
                    @endforelse
                </div>
            </section>

            <section class="side-card">
                <h2 class="side-card-title">Details</h2>
                <div class="detail-list">
                    <div class="detail-row"><span>Created by</span><strong>{{ $memo->creator->name ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Created</span><strong>{{ $memo->created_at?->format('Y-m-d H:i') ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Last updated</span><strong>{{ $memo->updated_at?->format('Y-m-d H:i') ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Template</span><strong>{{ $memo->template->template_name ?? '—' }}</strong></div>
                </div>
            </section>
        </aside>
    </div>
</div>

<dialog id="approvalConfirmationDialog" class="approval-dialog" aria-labelledby="approvalConfirmationTitle">
    <div class="approval-dialog-content">
        <h2 id="approvalConfirmationTitle">Approve this memo?</h2>
        <p>Your approval will be recorded with the current date and time. This action cannot be changed from this screen.</p>
        <div class="approval-dialog-actions">
            <button type="button" class="dialog-cancel" onclick="document.getElementById('approvalConfirmationDialog').close()">Cancel</button>
            <button type="button" id="confirmApprovalButton" class="dialog-confirm">Yes, approve memo</button>
        </div>
    </div>
</dialog>
@endsection

@section('scripts')
<script>
    (() => {
        const navigation = document.querySelector('[data-document-navigation]');
        if (!navigation) return;
        const pages = [...document.querySelectorAll('[data-document-page]')];
        const previous = navigation.querySelector('[data-previous-page]');
        const next = navigation.querySelector('[data-next-page]');
        const counter = navigation.querySelector('[data-page-counter]');
        let current = 0;
        const render = () => {
            pages.forEach((page, index) => { page.hidden = index !== current; });
            previous.disabled = current === 0;
            next.disabled = current === pages.length - 1;
            counter.textContent = `Page ${current + 1} of ${pages.length}`;
        };
        previous.addEventListener('click', () => { if (current > 0) { current--; render(); } });
        next.addEventListener('click', () => { if (current < pages.length - 1) { current++; render(); } });
        navigation.hidden = false;
        render();
    })();
    (() => {
        const form = document.querySelector('[data-approve-form]');
        const dialog = document.getElementById('approvalConfirmationDialog');
        const confirmButton = document.getElementById('confirmApprovalButton');

        if (!form || !dialog || !confirmButton) return;

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            dialog.showModal();
        });

        confirmButton.addEventListener('click', () => form.submit());
    })();
</script>
@endsection
