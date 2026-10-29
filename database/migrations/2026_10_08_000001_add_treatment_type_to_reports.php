<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['reports', 'reports_closed'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'free_treatment')) {
                    $table->boolean('free_treatment')->default(false);
                }

                if (!Schema::hasColumn($tableName, 'npc_call')) {
                    $table->boolean('npc_call')->default(false);
                }
            });

            DB::table($tableName)
                ->where('price', 0)
                ->where('free_treatment', false)
                ->update(['free_treatment' => true]);
        }
    }

    public function down(): void
    {
        foreach (['reports', 'reports_closed'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = [];

                if (Schema::hasColumn($tableName, 'free_treatment')) {
                    $columns[] = 'free_treatment';
                }

                if (Schema::hasColumn($tableName, 'npc_call')) {
                    $columns[] = 'npc_call';
                }

                if ($columns) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
