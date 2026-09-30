<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalWorkflow extends Model
{
    protected $fillable = [
        'template_id',
        'workflow_name',
        'approval_type',
        'approval_rule',
        'minimum_approvals',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MemoTemplate::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(
            ApprovalStep::class,
            'workflow_id'
        )->orderBy('step_order');
    }

    public function memoApprovals(): HasMany
    {
        return $this->hasMany(MemoApproval::class);
    }
}