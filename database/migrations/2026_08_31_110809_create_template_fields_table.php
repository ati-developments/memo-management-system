<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('memo_templates')
                ->cascadeOnDelete();

            $table->string('field_name');
            $table->string('field_label');

            $table->enum('field_type', [
                'text',
                'textarea',
                'number',
                'date',
                'datetime',
                'email',
                'select',
                'radio',
                'checkbox',
                'file',
                'richtext'
            ]);

            $table->text('placeholder')->nullable();

            $table->json('options')->nullable();

            $table->boolean('is_required')->default(false);

            $table->boolean('is_active')->default(true);

            $table->integer('field_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_fields');
    }
};