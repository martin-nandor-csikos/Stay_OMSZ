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
        Schema::create('user_point_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->string('point_type');
            $table->integer('old_value');
            $table->integer('new_value');
            $table->text('reason');
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['user_id', 'point_type']);
            $table->index(['deleted_user_id', 'point_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_point_histories');
    }
};
