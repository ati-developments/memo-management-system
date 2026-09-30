<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateTable extends Model
{
    protected $fillable = [
        'template_id',
        'table_name',
        'table_label',
        'is_active',
        'table_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MemoTemplate::class, 'template_id');
    }

    public function columns(): HasMany
    {
        return $this->hasMany(TemplateTableColumn::class, 'template_table_id')
            ->orderBy('column_order');
    }
}