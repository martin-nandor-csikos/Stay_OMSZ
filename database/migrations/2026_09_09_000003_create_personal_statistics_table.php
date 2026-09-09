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
        Schema::create('personal_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('total_report_count')->default(0);
            $table->unsignedInteger('total_duty_minutes')->default(0);
            $table->unsignedInteger('top_three_report_count')->default(0);
            $table->unsignedBigInteger('total_salary')->default(0);
            $table->unsignedInteger('report_rank')->nullable();
            $table->unsignedInteger('duty_time_rank')->nullable();
            $table->string('report_ranking_tooltip')->nullable();
            $table->string('duty_time_ranking_tooltip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_statistics');
    }
};
