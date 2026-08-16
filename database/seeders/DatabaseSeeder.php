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
            'password' => bcrypt('admin'),
            'adminLevel' => 2,
        ]);
        \App\Models\User::factory()->create([
            'charactername' => 'Dr. Pietro Burns',
            'username' => 'nonadmin',
            'password' => bcrypt('12345678'),
            'adminLevel' => 0,
        ]);

        \App\Models\Lock::factory()->create([
            'name' => 'close_week',
            'isLocked' => 0,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'VIZS',
            'cost' => 20000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'KÖT',
            'cost' => 30000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'GIP',
            'cost' => 35000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'GYÓGY',
            'cost' => 30000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'KAV',
            'cost' => 20000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'ÁS',
            'cost' => 30000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'SS',
            'cost' => 35000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'EMB',
            'cost' => 20000,
        ]);

        \App\Models\TicketService::factory()->create([
            'service_name' => 'TH',
            'cost' => 150000,
        ]);

        \App\Models\User::factory(20)->create();
        \App\Models\Report::factory(20)->create();
        \App\Models\DutyTime::factory(50)->create();
        \App\Models\AdminLog::factory(20)->create();
        \App\Models\Inactivity::factory(10)->create();
    }
}
