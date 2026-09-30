<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateTableColumn extends Model
{
    protected $fillable = [
        'template_table_id',
        'column_name',
        'column_label',
        'column_type',
        'placeholder',
        'is_required',
        'is_calculated',
        'calculation',
        'column_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_calculated' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(
            TemplateTable::class,
            'template_table_id'
        );
    }
}