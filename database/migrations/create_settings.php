<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('minimum_report_count')->default(15);
            $table->unsignedInteger('minimum_duty_time')->default(800);
            $table->unsignedInteger('double_week_report_count')->default(40);
            $table->unsignedInteger('double_week_duty_time')->default(1800);
            $table->unsignedInteger('bonus_first_percentage')->default(50);
            $table->unsignedInteger('bonus_second_percentage')->default(40);
            $table->unsignedInteger('bonus_third_percentage')->default(30);
            $table->timestamps();
        });

        // Settings table always has exactly one row holding the current values.
        DB::table('settings')->insert([
            'minimum_report_count' => 15,
            'minimum_duty_time' => 800,
            'double_week_report_count' => 40,
            'double_week_duty_time' => 1800,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
