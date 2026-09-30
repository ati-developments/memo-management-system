<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workflow_id')
                ->constrained('approval_workflows')
                ->cascadeOnDelete();

            $table->foreignId('approver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedInteger('step_order')->default(1);

            $table->boolean('is_required')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};