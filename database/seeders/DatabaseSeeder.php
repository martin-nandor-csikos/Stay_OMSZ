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

        $rank1 = \App\Models\Rank::factory()->create([
            'name' => 'Gyakornok',
            'salary' => 5000,
            'rank_order' => 1,
            'requires_exam' => false,
            'minimum_successful_weeks' => 1,
            'is_leader' => false,
        ]);

        $rank2 = \App\Models\Rank::factory()->create([
            'name' => 'Mentőápoló',
            'salary' => 10000,
            'rank_order' => 2,
            'requires_exam' => true,
            'minimum_successful_weeks' => 2,
            'is_leader' => false,
        ]);

        $rank3 = \App\Models\Rank::factory()->create([
            'name' => 'Mentőtiszt',
            'salary' => 20000,
            'rank_order' => 3,
            'requires_exam' => true,
            'minimum_successful_weeks' => 3,
            'is_leader' => false,
        ]);

        $rank4 = \App\Models\Rank::factory()->create([
            'name' => 'Főorvos',
            'salary' => 35000,
            'rank_order' => 4,
            'requires_exam' => false,
            'minimum_successful_weeks' => 0,
            'is_leader' => true,
        ]);

        $highestRank = $rank4;

        \App\Models\User::factory()->create([
            'charactername' => 'Dr. Mac Burns',
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'adminLevel' => 2,
            'rank_id' => $highestRank->id,
        ]);
        \App\Models\User::factory()->create([
            'charactername' => 'Dr. Pietro Burns',
            'username' => 'nonadmin',
            'password' => bcrypt('nonadmin'),
            'adminLevel' => 0,
            'rank_id' => $highestRank->id,
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
