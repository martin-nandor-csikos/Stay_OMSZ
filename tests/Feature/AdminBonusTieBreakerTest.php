<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminBonusTieBreakerTest extends TestCase
{
    use RefreshDatabase;

    public function test_bonus_tie_break_uses_duty_time_when_report_count_matches(): void
    {
        DB::table('settings')->updateOrInsert([], [
            'minimum_report_count' => 5,
            'minimum_duty_time' => 100,
            'double_week_report_count' => 10,
            'double_week_duty_time' => 200,
            'bonus_first_percentage' => 50,
            'bonus_second_percentage' => 40,
            'bonus_third_percentage' => 30,
        ]);

        $rank = Rank::create([
            'name' => 'Test rank',
            'salary' => 1000,
            'rank_order' => 1,
        ]);

        $lowDutyUser = User::create([
            'charactername' => 'Low Duty',
            'username' => 'low-duty',
            'password' => 'secret123',
            'rank_id' => $rank->id,
        ]);

        $highDutyUser = User::create([
            'charactername' => 'High Duty',
            'username' => 'high-duty',
            'password' => 'secret123',
            'rank_id' => $rank->id,
        ]);

        foreach (range(1, 8) as $index) {
            DB::table('reports')->insert([
                'user_id' => $lowDutyUser->id,
                'price' => 100,
                'diagnosis' => 'test',
                'withWho' => null,
                'img' => 'low-duty-' . $index . '.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('reports')->insert([
                'user_id' => $highDutyUser->id,
                'price' => 100,
                'diagnosis' => 'test',
                'withWho' => null,
                'img' => 'high-duty-' . $index . '.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('duty_times')->insert([
            'user_id' => $lowDutyUser->id,
            'begin' => now()->subMinutes(120),
            'end' => now(),
            'minutes' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('duty_times')->insert([
            'user_id' => $highDutyUser->id,
            'begin' => now()->subMinutes(180),
            'end' => now(),
            'minutes' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $controller = new AdminController();
        $method = new \ReflectionMethod(AdminController::class, 'getWeeklyStatsQuery');
        $method->setAccessible(true);

        $stats = $method->invoke($controller);

        $this->assertSame($highDutyUser->id, $stats->first()->id);
        $this->assertSame(50, (int) $stats->first()->bonus_percentage);
        $this->assertSame($lowDutyUser->id, $stats->last()->id);
        $this->assertSame(40, (int) $stats->last()->bonus_percentage);
    }
}
