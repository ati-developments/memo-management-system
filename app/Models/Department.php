<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'department_name',
        'department_code',
        'description',
        'status',
    ];
        protected $casts = [
        'status' => 'boolean',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(MemoTemplate::class);
    }

    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }
    public function memoTemplates(): HasMany
    {
        return $this->hasMany(MemoTemplate::class, 'department_id');
    }
}