<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SidebarMenuItem extends Model
{
    protected $fillable = [
        'item_key', 'label', 'route_name', 'url', 'parent_id', 'icon',
        'access_key', 'admin_only', 'is_group', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'admin_only' => 'boolean',
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }
}
