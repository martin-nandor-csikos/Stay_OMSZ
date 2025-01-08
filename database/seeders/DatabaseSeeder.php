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
            'charactername' => 'Teszt Elek',
            'username' => 'admin',
            'password' => bcrypt('12345678'),
            'isAdmin' => 1,
            'canGiveAdmin' => 1,
        ]);
        \App\Models\User::factory()->create([
            'charactername' => 'Elek Róbert',
            'username' => 'nonadmin',
            'password' => bcrypt('12345678'),
            'isAdmin' => 0,
            'canGiveAdmin' => 0,
        ]);

        \App\Models\Lock::factory()->create([
            'name' => 'close_week',
            'isLocked' => 0,
        ]);

        \App\Models\Variable::factory()->create([
            'var_name' => 'mb_mention_id',
            'var_value' => '<@&1240409405850390599>',
        ]);

        \App\Models\Variable::factory()->create([
            'var_name' => 'mb_mention_id',
            'var_value' => '<@&1240409405850390599>',
        ]);

        /*
        \App\Models\User::factory(20)->create();
        \App\Models\Report::factory(20)->create();
        \App\Models\DutyTime::factory(50)->create();
        \App\Models\AdminLog::factory(20)->create();
        \App\Models\Inactivity::factory(10)->create();
        */
    }
}
