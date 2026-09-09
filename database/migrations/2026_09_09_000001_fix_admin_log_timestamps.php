<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE admin_logs MODIFY created_at timestamp NULL DEFAULT NULL');
        DB::statement('ALTER TABLE admin_logs MODIFY updated_at timestamp NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE admin_logs MODIFY created_at timestamp NULL DEFAULT NULL');
        DB::statement('ALTER TABLE admin_logs MODIFY updated_at timestamp NULL DEFAULT NULL');
    }
};
