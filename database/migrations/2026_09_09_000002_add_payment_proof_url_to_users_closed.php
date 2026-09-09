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
        if (!Schema::hasTable('users_closed') || Schema::hasColumn('users_closed', 'payment_proof_url')) {
            return;
        }

        Schema::table('users_closed', function (Blueprint $table) {
            $table->string('payment_proof_url', 2048)->nullable()->after('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users_closed') || !Schema::hasColumn('users_closed', 'payment_proof_url')) {
            return;
        }

        Schema::table('users_closed', function (Blueprint $table) {
            $table->dropColumn('payment_proof_url');
        });
    }
};
