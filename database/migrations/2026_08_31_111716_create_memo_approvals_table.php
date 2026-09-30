<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('memo_id')
                ->constrained('memos')
                ->cascadeOnDelete();

            $table->foreignId('workflow_id')
                ->nullable()
                ->constrained('approval_workflows')
                ->nullOnDelete();

            $table->foreignId('approver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('approval_step_id')
                ->nullable()
                ->constrained('approval_steps')
                ->nullOnDelete();

            $table->enum('action', [
                'pending',
                'approved',
                'returned',
                'rejected'
            ])->default('pending');

            $table->text('comment')->nullable();

            $table->string('signature_path')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->string('approval_ip')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_approvals');
    }
};