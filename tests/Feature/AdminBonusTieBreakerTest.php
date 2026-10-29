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

    public function test_leaders_do_not_consume_bonus_positions_and_zero_report_users_get_no_bonus(): void
    {
        $this->configureBonusSettings();
        $leaderRank = Rank::create([
            'name' => 'Leader rank',
            'salary' => 1000,
            'rank_order' => 2,
            'is_leader' => true,
        ]);
        $memberRank = Rank::create([
            'name' => 'Member rank',
            'salary' => 1000,
            'rank_order' => 1,
            'is_leader' => false,
        ]);

        $firstLeader = User::create([
            'charactername' => 'First Leader',
            'username' => 'first-leader',
            'password' => 'secret123',
            'rank_id' => $leaderRank->id,
        ]);
        $secondLeader = User::create([
            'charactername' => 'Second Leader',
            'username' => 'second-leader',
            'password' => 'secret123',
            'rank_id' => $leaderRank->id,
        ]);
        $thirdPlaceMember = User::create([
            'charactername' => 'Third Place Member',
            'username' => 'third-place-member',
            'password' => 'secret123',
            'rank_id' => $memberRank->id,
        ]);
        $inactiveMember = User::create([
            'charactername' => 'Inactive Member',
            'username' => 'inactive-member',
            'password' => 'secret123',
            'rank_id' => $memberRank->id,
        ]);

        $this->addReports($firstLeader, 8);
        $this->addReports($secondLeader, 7);
        $this->addReports($thirdPlaceMember, 6);

        $stats = $this->getWeeklyStats()->keyBy('id');

        $this->assertSame(0, (int) $stats[$firstLeader->id]->bonus_percentage);
        $this->assertSame(0, (int) $stats[$secondLeader->id]->bonus_percentage);
        $this->assertSame(50, (int) $stats[$thirdPlaceMember->id]->bonus_percentage);
        $this->assertSame(0, (int) $stats[$inactiveMember->id]->bonus_percentage);
    }

    public function test_no_bonus_is_given_when_every_reporting_player_is_a_leader(): void
    {
        $this->configureBonusSettings();
        $leaderRank = Rank::create([
            'name' => 'Leader rank',
            'salary' => 1000,
            'rank_order' => 2,
            'is_leader' => true,
        ]);

        $leaders = collect(['First Leader', 'Second Leader'])->map(function ($name, $index) use ($leaderRank) {
            $user = User::create([
                'charactername' => $name,
                'username' => 'leader-' . $index,
                'password' => 'secret123',
                'rank_id' => $leaderRank->id,
            ]);
            $this->addReports($user, 5 - $index);

            return $user;
        });

        $stats = $this->getWeeklyStats()->keyBy('id');

        foreach ($leaders as $leader) {
            $this->assertSame(0, (int) $stats[$leader->id]->bonus_percentage);
        }
    }

    private function configureBonusSettings(): void
    {
        DB::table('settings')->updateOrInsert([], [
            'minimum_report_count' => 1,
            'minimum_duty_time' => 100,
            'double_week_report_count' => 10,
            'double_week_duty_time' => 200,
            'bonus_first_percentage' => 50,
            'bonus_second_percentage' => 40,
            'bonus_third_percentage' => 30,
        ]);
    }

    private function addReports(User $user, int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            DB::table('reports')->insert([
                'user_id' => $user->id,
                'price' => 100,
                'diagnosis' => 'test',
                'withWho' => null,
                'img' => $user->id . '-' . $index . '.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getWeeklyStats()
    {
        $controller = new AdminController();
        $method = new \ReflectionMethod(AdminController::class, 'getWeeklyStatsQuery');
        $method->setAccessible(true);

        return $method->invoke($controller);
    }
}
