<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'role_name',
        'description',
        'status',
        'menu_access',
    ];

    protected $casts = ['menu_access' => 'array'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
