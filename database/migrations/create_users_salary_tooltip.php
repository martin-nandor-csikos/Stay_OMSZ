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
        Schema::table('users_closed', function (Blueprint $table) {
            $table->string('salary_tooltip')->nullable()->after('salary');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('salary_tooltip')->nullable()->after('salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_closed', function (Blueprint $table) {
            $table->dropColumn('salary_tooltip');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salary_tooltip');
        });
    }
};
