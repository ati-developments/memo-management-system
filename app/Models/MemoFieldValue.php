<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoFieldValue extends Model
{
    protected $fillable = [
        'memo_id',
        'template_field_id',
        'field_name',
        'field_value',
    ];

    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    public function templateField(): BelongsTo
    {
        return $this->belongsTo(TemplateField::class);
    }
}