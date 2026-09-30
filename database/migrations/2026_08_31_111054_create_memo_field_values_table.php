<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_field_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('memo_id')
                ->constrained('memos')
                ->cascadeOnDelete();

            $table->foreignId('template_field_id')
                ->nullable()
                ->constrained('template_fields')
                ->nullOnDelete();

            $table->string('field_name');

            $table->longText('field_value')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_field_values');
    }
};