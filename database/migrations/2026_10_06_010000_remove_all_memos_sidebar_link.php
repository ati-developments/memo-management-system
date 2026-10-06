<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sidebar_menu_items')
            ->where('item_key', 'all_memos')
            ->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('sidebar_menu_items')
            ->where('item_key', 'all_memos')
            ->update(['is_active' => true]);
    }
};
