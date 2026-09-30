<table class="letterhead"><tr><td><h1>MEMO</h1><div class="department">{{ $memo->department?->department_name }}</div></td><td class="brand">@if($brandImage ?? null)<img src="{{ $brandImage }}" alt="Amana Takaful Insurance">@else AMANA<small>TAKAFUL INSURANCE</small>@endif</td></tr></table>
<p class="reference">{{ $memo->memo_number }} &middot; {{ ucfirst($memo->status) }}</p>
@include('memos.text-blocks', ['position' => 'start'])
<table class="metadata">
    <tr><td><div class="box"><div class="label">To</div><div class="value">{{ $values->get('to') ?: '—' }}</div></div></td><td class="gap"></td><td><div class="box"><div class="label">From</div><div class="value">{{ $values->get('from') ?: $memo->creator?->name }}</div></div></td></tr>
    <tr><td><div class="box"><div class="label">Through</div><div class="value">{{ $values->get('through') ?: '—' }}</div></div></td><td class="gap"></td><td><div class="box"><div class="label">Date</div><div class="value">{{ $values->get('date') ?: $memo->created_at?->format('Y-m-d') }}</div></div></td></tr>
</table>
<table class="subject"><tr><td class="caption">Subject</td><td>{{ $memo->subject }}</td></tr></table>
@include('memos.document-body')
@php
    $signers = collect([['label' => 'Prepared by', 'user' => $memo->creator, 'approval' => $approvals->firstWhere('approval_role', 'prepared')]]);
    $workflowApprovals = $approvals->where('approval_role', '!=', 'prepared')->values();
    $approvalLabels = \App\Support\ApprovalSignatureLabels::forCount($workflowApprovals->count());
    foreach ($workflowApprovals as $index => $approval) {
        $signers->push(['label' => $approvalLabels[$index] ?? 'Approved by', 'user' => $approval->approver, 'approval' => $approval]);
    }
@endphp
<table class="signatures">
@foreach($signers->chunk(3) as $row)
    <tr>@foreach($row as $signer)<td>
        <div>{{ $signer['label'] }}</div>
        <div class="signature-space">
            @if($signer['approval']?->action === 'approved' && isset($signatureImages[$signer['approval']->id]))
                <img class="signature-image" src="{{ $signatureImages[$signer['approval']->id] }}" alt="Signature of {{ $signer['user']?->name }}">
            @endif
        </div>
        @if($signer['approval']?->action === 'approved')<div class="approval-confirmation">Approved @if($signer['approval']->approved_at)&ndash; {{ $signer['approval']->approved_at->format('d M Y, h:i A') }}@endif</div>@endif
        <strong>{{ $signer['user']?->name ?? 'Unknown' }}</strong>
        <div class="designation">{{ $signer['user']?->designation }}</div>
        @if($signer['approval'] && $signer['approval']->action !== 'approved')<div class="approval-state">{{ ucfirst($signer['approval']->action) }}{{ $signer['approval']->approved_at ? ' · '.$signer['approval']->approved_at->format('d M Y H:i') : '' }}</div>@endif
    </td>@endforeach @for($empty = $row->count(); $empty < 3; $empty++)<td></td>@endfor</tr>
@endforeach
</table>
@include('memos.text-blocks', ['position' => 'end'])
