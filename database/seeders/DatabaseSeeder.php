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
            'isAdmin' => 0,
            'canGiveAdmin' => 0,
        ]);

        \App\Models\Lock::factory()->create([
            'name' => 'close_week',
            'isLocked' => 0,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'vizs',
            'price' => 20000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'kot',
            'price' => 30000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'gip',
            'price' => 35000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'gyogy',
            'price' => 30000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'kav',
            'price' => 20000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'as',
            'price' => 30000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'ss',
            'price' => 35000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'emb',
            'price' => 20000,
        ]);

        \App\Models\Price::factory()->create([
            'diagnosis_name' => 'th',
            'price' => 150000,
        ]);

        \App\Models\User::factory(20)->create();
        \App\Models\Report::factory(20)->create();
        \App\Models\DutyTime::factory(50)->create();
        \App\Models\AdminLog::factory(20)->create();
        \App\Models\Inactivity::factory(10)->create();
    }
}
