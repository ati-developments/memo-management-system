<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_templates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            $table->string('template_name');
            $table->string('template_code');
            $table->text('description')->nullable();

            $table->string('document_path')->nullable();

            $table->boolean('status')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'department_id',
                'template_code'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_templates');
    }
};