<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateApprovalStep extends Model
{
    protected $fillable = [
        'template_id',
        'step_label',
        'step_type',
        'step_order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MemoTemplate::class, 'template_id');
    }
}