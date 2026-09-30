<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_tables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('memo_templates')
                ->cascadeOnDelete();

            $table->string('table_name');

            $table->string('table_label')->nullable();

            $table->boolean('is_active')->default(true);

            $table->integer('table_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_tables');
    }
};