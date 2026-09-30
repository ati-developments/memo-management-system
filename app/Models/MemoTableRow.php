<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoTableRow extends Model
{
    protected $fillable = [
        'memo_id',
        'template_table_id',
        'row_data',
        'row_order',
    ];

    protected $casts = [
        'row_data' => 'array',
    ];

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    public function templateTable(): BelongsTo
    {
        return $this->belongsTo(TemplateTable::class);
    }
}
