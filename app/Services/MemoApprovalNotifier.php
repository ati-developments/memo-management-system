<?php

namespace App\Services;

use App\Models\Memo;
use App\Notifications\MemoApprovalRequested;

class MemoApprovalNotifier
{
    public function notifyReadyApprovers(Memo $memo): void
    {
        if ($memo->status !== 'pending') {
            return;
        }

        $pending = $memo->approvals()->where('approval_role', 'approval')
            ->where('action', 'pending')->with(['approver', 'approvalStep', 'workflow'])
            ->get()->sortBy('chain_order');

        if ($pending->first()?->workflow?->approval_type === 'sequential') {
            $pending = $pending->take(1);
        }

        foreach ($pending as $approval) {
            $approval->approver?->notify(new MemoApprovalRequested($approval));
        }
    }
}
