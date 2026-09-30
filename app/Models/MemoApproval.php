<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoApproval extends Model
{
    protected $fillable = [
        'memo_id',
        'workflow_id',
        'approver_id',
        'approval_step_id',
        'approval_role',
        'action',
        'comment',
        'signature_path',
        'approved_at',
        'approval_ip',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ApprovalWorkflow::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function approvalStep(): BelongsTo
    {
        return $this->belongsTo(ApprovalStep::class);
    }

    /** The prepared-by acknowledgement always appears before workflow steps. */
    public function getChainOrderAttribute(): int
    {
        return $this->approval_role === 'prepared'
            ? 0
            : ($this->approvalStep?->step_order ?? PHP_INT_MAX);
    }
}
