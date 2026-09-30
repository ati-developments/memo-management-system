<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalStep extends Model
{
    protected $fillable = [
        'workflow_id',
        'approver_id',
        'step_order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

     public function workflow(): BelongsTo
    {
        return $this->belongsTo(
            ApprovalWorkflow::class,
            'workflow_id'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}