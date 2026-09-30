<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->nullable()
                ->constrained('memo_templates')
                ->nullOnDelete();

            $table->string('workflow_name');

            $table->enum('approval_type', [
                'sequential',
                'open'
            ]);

            $table->enum('approval_rule', [
                'all',
                'any',
                'minimum'
            ])->default('all');

            $table->unsignedInteger('minimum_approvals')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_workflows');
    }
};