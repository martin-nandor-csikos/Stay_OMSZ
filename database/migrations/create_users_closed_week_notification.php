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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('closed_week_salary')->nullable()->after('rank_change_type');
            $table->unsignedInteger('closed_week_bonus')->nullable()->after('closed_week_salary');
            $table->string('closed_week_calculation')->nullable()->after('closed_week_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['closed_week_salary', 'closed_week_bonus', 'closed_week_calculation']);
        });
    }
};
