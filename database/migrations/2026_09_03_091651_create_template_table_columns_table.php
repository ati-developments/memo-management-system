<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_table_columns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_table_id')
                ->constrained('template_tables')
                ->cascadeOnDelete();

            $table->string('column_name');

            $table->string('column_label');

            $table->enum('column_type', [
                'text',
                'number',
                'decimal',
                'date',
                'textarea',
            ])->default('text');

            $table->string('placeholder')->nullable();

            $table->boolean('is_required')->default(false);

            $table->boolean('is_calculated')->default(false);

            $table->string('calculation')->nullable();

            $table->integer('column_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_table_columns');
    }
};