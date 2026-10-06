<?php

use App\Models\SidebarMenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sidebar_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_key')->unique();
            $table->string('label');
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('sidebar_menu_items')->cascadeOnDelete();
            $table->string('icon', 30)->default('document');
            $table->string('access_key', 50)->nullable();
            $table->boolean('admin_only')->default(false);
            $table->boolean('is_group')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $memos = SidebarMenuItem::create([
            'item_key' => 'memos_group', 'label' => 'Memos', 'icon' => 'document',
            'is_group' => true, 'sort_order' => 30,
        ]);
        $settings = SidebarMenuItem::create([
            'item_key' => 'settings_group', 'label' => 'Settings', 'icon' => 'grid',
            'is_group' => true, 'admin_only' => true, 'sort_order' => 50,
        ]);

        $items = [
            ['item_key' => 'dashboard', 'label' => 'Dashboard', 'route_name' => 'dashboard', 'icon' => 'grid', 'sort_order' => 10],
            ['item_key' => 'new_memo', 'label' => 'New Memo', 'route_name' => 'memos.new', 'icon' => 'plus', 'access_key' => 'new_memo', 'sort_order' => 20],
            ['item_key' => 'templates', 'label' => 'Templates', 'route_name' => 'templates.index', 'parent_id' => $memos->id, 'icon' => 'document', 'access_key' => 'templates', 'sort_order' => 10],
            ['item_key' => 'my_memos', 'label' => 'Memos', 'route_name' => 'memos.my', 'parent_id' => $memos->id, 'icon' => 'document', 'access_key' => 'memos', 'sort_order' => 20],
            ['item_key' => 'approvals', 'label' => 'Approvals', 'route_name' => 'approvals.index', 'icon' => 'check', 'access_key' => 'approvals', 'sort_order' => 40],
            ['item_key' => 'user_registration', 'label' => 'User Registration', 'route_name' => 'admin.users.create', 'parent_id' => $settings->id, 'icon' => 'plus', 'admin_only' => true, 'sort_order' => 10],
            ['item_key' => 'access_menu', 'label' => 'Access Menu', 'route_name' => 'admin.access-menu', 'parent_id' => $settings->id, 'icon' => 'grid', 'admin_only' => true, 'sort_order' => 20],
            ['item_key' => 'edit', 'label' => 'Update Labels', 'route_name' => 'admin.roles.index', 'parent_id' => $settings->id, 'icon' => 'document', 'admin_only' => true, 'sort_order' => 30],
        ];

        foreach ($items as $item) {
            SidebarMenuItem::create($item);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sidebar_menu_items');
    }
};
