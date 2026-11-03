<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memo_templates', function (Blueprint $table) {
            $table->boolean('is_one_time')->default(false)->after('status');
        });

        Schema::table('memo_templates', function (Blueprint $table) {
            $table->dropUnique('memo_templates_department_id_template_code_unique');
            $table->unique(
                ['department_id', 'template_code', 'is_one_time'],
                'memo_templates_department_code_one_time_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('memo_templates', function (Blueprint $table) {
            $table->dropUnique('memo_templates_department_code_one_time_unique');
            $table->dropColumn('is_one_time');
            $table->unique(['department_id', 'template_code'], 'memo_templates_department_id_template_code_unique');
        });
    }
};
