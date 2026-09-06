<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('plus_points')->default(0)->after('account_id');
            $table->integer('penalty_points')->default(0)->after('plus_points');
            $table->timestamp('last_plus_point_at')->nullable()->after('penalty_points');
            $table->timestamp('last_penalty_point_at')->nullable()->after('last_plus_point_at');
        });

        Schema::table('deleted_users', function (Blueprint $table) {
            $table->integer('penalty_points')->default(0)->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plus_points', 'penalty_points', 'last_plus_point_at', 'last_penalty_point_at']);
        });

        Schema::table('deleted_users', function (Blueprint $table) {
            $table->dropColumn('penalty_points');
        });
    }
};
