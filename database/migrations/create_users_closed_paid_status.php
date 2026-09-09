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
        if (!Schema::hasTable('users_closed')) {
            return;
        }

        Schema::table('users_closed', function (Blueprint $table) {
            if (!Schema::hasColumn('users_closed', 'is_paid')) {
                $table->boolean('is_paid')->default(false);
            }

            if (!Schema::hasColumn('users_closed', 'payment_proof_url')) {
                $table->string('payment_proof_url', 2048)->nullable()->after('is_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users_closed')) {
            return;
        }

        Schema::table('users_closed', function (Blueprint $table) {
            if (Schema::hasColumn('users_closed', 'payment_proof_url')) {
                $table->dropColumn('payment_proof_url');
            }

            if (Schema::hasColumn('users_closed', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
        });
    }
};
