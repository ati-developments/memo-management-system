<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memo_approvals', function (Blueprint $table) {
            $table->string('approval_role', 30)->default('approval')->after('approval_step_id');
        });
    }

    public function down(): void
    {
        Schema::table('memo_approvals', function (Blueprint $table) {
            $table->dropColumn('approval_role');
        });
    }
};
