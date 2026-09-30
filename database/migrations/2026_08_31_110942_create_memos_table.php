<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->id();

            $table->string('memo_number')->unique();

            $table->foreignId('template_id')
                ->nullable()
                ->constrained('memo_templates')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('subject');

            $table->string('status')->default('draft');

            $table->string('creation_type')->default('template');

            $table->text('content')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};