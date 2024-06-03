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
        Schema::create('inactivities', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->date('begin');
            $table->date('end');
            $table->string('reason');
            $table->boolean('accepted')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inactivities');
    }
};
