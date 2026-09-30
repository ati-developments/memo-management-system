<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_table_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memo_id')->constrained('memos')->cascadeOnDelete();
            $table->foreignId('template_table_id')->constrained('template_tables')->cascadeOnDelete();
            $table->json('row_data');
            $table->unsignedInteger('row_order')->default(0);
            $table->timestamps();

            $table->index(['memo_id', 'template_table_id', 'row_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_table_rows');
    }
};
