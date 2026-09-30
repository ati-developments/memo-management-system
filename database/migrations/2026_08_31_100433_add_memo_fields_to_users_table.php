<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->foreignId('department_id')
                ->nullable()
                ->after('username')
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('role_id')
                ->nullable()
                ->after('department_id')
                ->constrained('roles')
                ->nullOnDelete();

            $table->string('employee_id')->nullable()->unique()->after('role_id');
            $table->string('designation')->nullable()->after('employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['role_id']);

            $table->dropColumn([
                'username',
                'department_id',
                'role_id',
                'employee_id',
                'designation',
            ]);
        });
    }
};