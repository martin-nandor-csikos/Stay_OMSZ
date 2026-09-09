<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->default('MGK')->after('rank_id');
            $table->timestamp('last_department_change_at')->nullable()->after('department');
        });

        Schema::table('users_closed', function (Blueprint $table) {
            $table->string('department')->default('MGK')->after('charactername');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'last_department_change_at']);
        });

        Schema::table('users_closed', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
