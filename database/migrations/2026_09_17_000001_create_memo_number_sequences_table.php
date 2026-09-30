<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_number_sequences', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_number_sequences');
    }
};
