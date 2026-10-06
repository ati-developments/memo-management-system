<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_status_labels', function (Blueprint $table) {
            $table->id();
            $table->string('status_key', 30)->unique();
            $table->string('label', 50);
            $table->timestamps();
        });

        $now = now();
        DB::table('memo_status_labels')->insert([
            ['status_key' => 'draft', 'label' => 'Draft', 'created_at' => $now, 'updated_at' => $now],
            ['status_key' => 'pending', 'label' => 'Pending', 'created_at' => $now, 'updated_at' => $now],
            ['status_key' => 'approved', 'label' => 'Approved', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_status_labels');
    }
};
