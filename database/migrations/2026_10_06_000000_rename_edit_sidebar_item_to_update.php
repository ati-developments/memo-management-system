<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sidebar_menu_items')
            ->where('item_key', 'edit')
            ->where('route_name', 'admin.roles.index')
            ->update(['label' => 'Update Labels']);
    }

    public function down(): void
    {
        DB::table('sidebar_menu_items')
            ->where('item_key', 'edit')
            ->where('route_name', 'admin.roles.index')
            ->update(['label' => 'Edit']);
    }
};
