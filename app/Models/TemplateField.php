<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateField extends Model
{
    protected $fillable = [
        'template_id',
        'field_name',
        'field_label',
        'field_type',
        'placeholder',
        'options',
        'is_required',
        'is_active',
        'field_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MemoTemplate::class, 'template_id');
    }
}