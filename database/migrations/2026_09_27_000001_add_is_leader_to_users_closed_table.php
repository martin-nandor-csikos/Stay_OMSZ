<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users_closed') || Schema::hasColumn('users_closed', 'is_leader')) {
            return;
        }

        Schema::table('users_closed', function (Blueprint $table) {
            $table->boolean('is_leader')->default(false);
        });

        $leaderRankNames = DB::table('ranks')->where('is_leader', true)->pluck('name');
        if ($leaderRankNames->isNotEmpty()) {
            DB::table('users_closed')->whereIn('rank_name', $leaderRankNames)->update(['is_leader' => true]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users_closed') || !Schema::hasColumn('users_closed', 'is_leader')) {
            return;
        }

        Schema::table('users_closed', function (Blueprint $table) {
            $table->dropColumn('is_leader');
        });
    }
};