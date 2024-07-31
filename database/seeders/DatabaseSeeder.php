<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\User::factory()->create([
            'charactername' => 'Dr. Mac Burns',
            'username' => 'admin',
            'password' => bcrypt('12345678'),
            'isAdmin' => 1,
            'canGiveAdmin' => 1,
        ]);
        \App\Models\User::factory()->create([
            'charactername' => 'Dr. Pietro Burns',
            'username' => 'nonadmin',
            'password' => bcrypt('12345678'),
            'isAdmin' => 1,
            'canGiveAdmin' => 0,
        ]);

        \App\Models\Lock::factory()->create([
            'name' => 'close_week',
            'isLocked' => false,
        ]);

        \App\Models\User::factory(20)->create();
        \App\Models\Report::factory(20)->create();
        \App\Models\DutyTime::factory(50)->create();
        \App\Models\AdminLog::factory(20)->create();
        \App\Models\Inactivity::factory(10)->create();
    }
}
