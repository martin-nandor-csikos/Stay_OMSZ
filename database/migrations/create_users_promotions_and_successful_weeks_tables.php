<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->unsignedInteger('minimum_successful_weeks')->default(2)->after('requires_exam');
        });

        Schema::table('users_closed', function (Blueprint $table) {
            $table->string('rank_name')->nullable()->after('salary');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('successful_weeks')->default(0)->after('rank_id');
            $table->string('promoted_from_rank')->nullable()->after('successful_weeks');
            $table->string('promoted_to_rank')->nullable()->after('promoted_from_rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropColumn('minimum_successful_weeks');
        });

        Schema::table('users_closed', function (Blueprint $table) {
            $table->dropColumn('rank_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['successful_weeks', 'promoted_from_rank', 'promoted_to_rank']);
        });
    }
};
