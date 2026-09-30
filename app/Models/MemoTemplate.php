<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemoTemplate extends Model
{
    protected $fillable = [
        'department_id',
        'template_name',
        'template_code',
        'description',
        'document_path',
        'allow_optional_text',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'allow_optional_text' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(TemplateField::class, 'template_id')
            ->orderBy('field_order');
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class, 'template_id');
    }
    public function tables(): HasMany
    {
        return $this->hasMany(TemplateTable::class, 'template_id')
            ->orderBy('table_order');
    }

    public function approvalSteps(): HasMany
    {
        return $this->hasMany(TemplateApprovalStep::class, 'template_id')
            ->orderBy('step_order');
    }
}
