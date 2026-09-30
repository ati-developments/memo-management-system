<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('memos', fn (Blueprint $table) => $table->json('inserted_items')->nullable());
    }

    public function down(): void
    {
        Schema::table('memos', fn (Blueprint $table) => $table->dropColumn('inserted_items'));
    }
};
