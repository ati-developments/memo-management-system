<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_approval_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('memo_templates')
                ->cascadeOnDelete();

            $table->string('step_label');

            $table->string('step_type')->default('approval');

            $table->integer('step_order')->default(0);

            $table->boolean('is_required')->default(true);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_approval_steps');
    }
};