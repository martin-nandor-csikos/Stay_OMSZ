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
        Schema::create('user_name_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('charactername');
            $table->timestamps();
        });

        Schema::create('deleted_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('charactername');
            $table->text('previous_characternames')->nullable();
            $table->string('highest_rank')->nullable();
            $table->timestamp('registered_at');
            $table->timestamp('deleted_at');
            $table->text('reason');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('highest_rank')->nullable()->after('rank_id');
        });

        DB::table('users')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->orderBy('users.id')
            ->select('users.id', 'ranks.name as rank_name')
            ->each(function ($user) {
                DB::table('users')->where('id', $user->id)->update([
                    'highest_rank' => $user->rank_name,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('highest_rank');
        });

        Schema::dropIfExists('deleted_users');
        Schema::dropIfExists('user_name_histories');
    }
};
