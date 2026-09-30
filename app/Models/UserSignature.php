<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSignature extends Model
{
    protected $fillable = [
        'user_id',
        'signature_path',
        'is_active',
        'uploaded_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}