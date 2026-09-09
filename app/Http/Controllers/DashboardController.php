<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\DiscordController;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * @var DiscordController
     */
    protected $discordController;

    /**
     * DashboardController constructor.
     */
    public function __construct()
    {
        $this->discordController = new DiscordController();
    }

    /**
     * Getting the statistics for the dashboard and rendering the view
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $top5UsersWithMostReports = $this->getTop5UsersWithMostReports();
        $userReportCount = $this->getUserReportCount($request);
        $latestUserReportDate = $this->getLatestUserReportDate($request);
        $userSumOfDutyTime = $this->getUserSumOfDutyTime($request);
        $allReportCount = $this->getAllReportCount();
        $topDutyTime = $this->getTopDutyTime();
        $sumOfDutyTime = $this->getSumOfDutyTime();
        $personalTotalStats = $this->getPersonalTotalStats($request);

        $userReportPercentage = $this->calculatingUserReportPercentage($userReportCount, $allReportCount);
        $minutesLeftUntilHavingTopDutyTime = $this->calculatingMinutesUntilTopDutyTime($topDutyTime, $userSumOfDutyTime);

        $settings = DB::table('settings')->first();
        $minimumDutyTime = $settings->minimum_duty_time;
        $minimumReportCount = $settings->minimum_report_count;
        $minimumDoubleRankupDutyTime = $settings->double_week_duty_time;
        $minimumDoubleRankupReportCount = $settings->double_week_report_count;

        $discordAnnouncements = $this->discordController->getDiscordAnnouncements();

        return view('dashboard', [
            'top5UsersWithMostReports' => $top5UsersWithMostReports,
            'userReportCount' => $userReportCount,
            'latestUserReportDate' => $latestUserReportDate,
            'userSumOfDutyTime' => $userSumOfDutyTime,
            'allReportCount' => $allReportCount,
            'userReportPercentage' => $userReportPercentage,
            'minutesLeftUntilHavingTopDutyTime' => $minutesLeftUntilHavingTopDutyTime,
            'minimumDutyTime' => $minimumDutyTime,
            'minimumReportCount' => $minimumReportCount,
            'minimumDoubleRankupDutyTime' => $minimumDoubleRankupDutyTime,
            'minimumDoubleRankupReportCount' => $minimumDoubleRankupReportCount,
            'sumOfDutyTime' => $sumOfDutyTime,
            'personalTotalStats' => $personalTotalStats,
            'discordAnnouncements' => $discordAnnouncements,
        ]);
    }

    /**
     * Get the top 5 users with the most reports.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getTop5UsersWithMostReports()
    {
        return DB::table('reports')->join('users', 'users.id', '=', 'reports.user_id')->select('users.charactername', DB::raw('count(reports.user_id) as reportCount'))->groupBy('users.charactername')->orderBy('reportCount', 'desc')->limit(5)->get();
    }

    /**
     * Get the count of reports created by the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function getUserReportCount(Request $request)
    {
        $reportCount = DB::table('reports')
            ->select(DB::raw('count(reports.user_id) as reportCount'))
            ->where('reports.user_id', '=', $request->user()->id)
            ->groupBy('reports.user_id')
            ->orderBy('reportCount', 'desc')
            ->value('reportCount');

        if ($reportCount === null) {
            return '0';
        }
        return $reportCount;
    }

    /**
     * Get the latest report date for the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function getLatestUserReportDate(Request $request)
    {
        $latestReportDate = DB::table('reports')
            ->select('reports.created_at')
            ->where('reports.user_id', '=', $request->user()->id)
            ->orderBy('reports.created_at', 'desc')
            ->value('reports.created_at');

        if ($latestReportDate === null) {
            return '-';
        }

        return Carbon::parse($latestReportDate)->format('Y.m.d H:i');
    }

    /**
     * Get the sum of duty time for the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function getUserSumOfDutyTime(Request $request)
    {
        $userSumOfDutyTime = DB::table('duty_times')
            ->select(DB::raw('sum(duty_times.minutes) as dutyMinuteSum'))
            ->where('duty_times.user_id', '=', $request->user()->id)
            ->value('dutyMinuteSum');

        if ($userSumOfDutyTime === null) {
            return '0';
        }

        return $userSumOfDutyTime;
    }

    /**
     * Get the count of all reports.
     *
     * @return string
     */
    private function getAllReportCount()
    {
        return DB::table('reports')->select(DB::raw('count(reports.id) as allReportCount'))->value('allReportCount');
    }

    /**
     * Get the top duty time for all users.
     *
     * @return string
     */
    private function getTopDutyTime()
    {
        return DB::table('duty_times')->select(DB::raw('sum(duty_times.minutes) as topDutyTime'), 'duty_times.user_id')->groupBy('user_id')->orderBy('topDutyTime', 'desc')->value('topDutyTime');
    }

    /**
     * Get the sum of duty time for all users.
     *
     * @return string
     */
    private function getSumOfDutyTime()
    {
        return DB::table('duty_times')->select(DB::raw('sum(duty_times.minutes) as sumDutyTime'))->value('sumDutyTime');
    }

    /**
     * Get the logged-in user's total statistics and rankings from current and closed data.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    private function getPersonalTotalStats(Request $request): array
    {
        $userId = (int) $request->user()->id;
        $statistics = $this->refreshPersonalStatistics();

        return $statistics[$userId] ?? $this->emptyPersonalStatistics();
    }

    /**
     * Refresh personal statistics in the database and return them indexed by user id.
     * Public so report/duty deletion can force an immediate recalculation.
     *
     * @return array<int, array<string, mixed>>
     */
    public function refreshPersonalStatistics(): array
    {
        $users = DB::table('users')->select('id', 'charactername', 'salary')->get()->keyBy('id');
        $storedStatistics = $this->getStoredPersonalStatisticsByUser();
        $reportTotals = $this->getReportTotalsByUser($storedStatistics);
        $dutyTotals = $this->getDutyTotalsByUser($storedStatistics);
        $statistics = [];

        foreach ($users as $user) {
            $currentUserId = (int) $user->id;
            $reportRanking = $this->getUserRanking($users, $reportTotals, $currentUserId, 'jelentés');
            $dutyRanking = $this->getUserRanking($users, $dutyTotals, $currentUserId, 'perc');
            $storedStatistic = $storedStatistics[$currentUserId] ?? null;
            $topThreeReportCount = (int) ($storedStatistic->top_three_report_count ?? 0);
            $totalSalary = (int) ($storedStatistic->total_salary ?? 0) + (int) ($user->salary ?? 0);

            $statistics[$currentUserId] = [
                'reports' => [
                    'value' => (int) ($reportTotals[$currentUserId] ?? 0),
                    'ranking' => $reportRanking,
                ],
                'duty_time' => [
                    'value' => (int) ($dutyTotals[$currentUserId] ?? 0),
                    'ranking' => $dutyRanking,
                ],
                'top_three_report_count' => $topThreeReportCount,
                'salary' => $totalSalary,
            ];

            if (Schema::hasTable('personal_statistics')) {
                DB::table('personal_statistics')->updateOrInsert(
                    ['user_id' => $currentUserId],
                    [
                        'total_report_count' => (int) ($storedStatistic->total_report_count ?? 0),
                        'total_duty_minutes' => (int) ($storedStatistic->total_duty_minutes ?? 0),
                        'top_three_report_count' => $topThreeReportCount,
                        'total_salary' => (int) ($storedStatistic->total_salary ?? 0),
                        'report_rank' => $reportRanking['rank'],
                        'duty_time_rank' => $dutyRanking['rank'],
                        'report_ranking_tooltip' => $reportRanking['tooltip'],
                        'duty_time_ranking_tooltip' => $dutyRanking['tooltip'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        return $statistics;
    }

    /**
     * @return array<int, object>
     */
    private function getStoredPersonalStatisticsByUser(): array
    {
        if (!Schema::hasTable('personal_statistics')) {
            return [];
        }

        return DB::table('personal_statistics')->get()->keyBy('user_id')->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyPersonalStatistics(): array
    {
        return [
            'reports' => [
                'value' => 0,
                'ranking' => ['rank' => null, 'display' => '-', 'tooltip' => '-'],
            ],
            'duty_time' => [
                'value' => 0,
                'ranking' => ['rank' => null, 'display' => '-', 'tooltip' => '-'],
            ],
            'top_three_report_count' => 0,
            'salary' => 0,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function getReportTotalsByUser(array $storedStatistics): array
    {
        $totals = [];

        foreach ($storedStatistics as $userId => $storedStatistic) {
            $totals[(int) $userId] = (int) $storedStatistic->total_report_count;
        }

        DB::table('reports')
            ->select('user_id', DB::raw('COUNT(*) as report_count'))
            ->groupBy('user_id')
            ->get()
            ->each(function ($reportTotal) use (&$totals) {
                $userId = (int) $reportTotal->user_id;
                $totals[$userId] = ($totals[$userId] ?? 0) + (int) $reportTotal->report_count;
            });

        return $totals;
    }

    /**
     * @return array<int, int>
     */
    private function getDutyTotalsByUser(array $storedStatistics): array
    {
        $totals = [];

        foreach ($storedStatistics as $userId => $storedStatistic) {
            $totals[(int) $userId] = (int) $storedStatistic->total_duty_minutes;
        }

        DB::table('duty_times')
            ->select('user_id', DB::raw('COALESCE(SUM(minutes), 0) as duty_minutes'))
            ->groupBy('user_id')
            ->get()
            ->each(function ($dutyTotal) use (&$totals) {
                $userId = (int) $dutyTotal->user_id;
                $totals[$userId] = ($totals[$userId] ?? 0) + (int) $dutyTotal->duty_minutes;
            });

        return $totals;
    }

    /**
     * @param \Illuminate\Support\Collection<int, object> $users
     * @param array<int, int> $totals
     * @return array<string, mixed>
     */
    private function getUserRanking($users, array $totals, int $userId, string $unit): array
    {
        $rankedUsers = $users->map(function ($user) use ($totals) {
            $user->total_value = (int) ($totals[(int) $user->id] ?? 0);

            return $user;
        })->sort(function ($firstUser, $secondUser) {
            if ($firstUser->total_value !== $secondUser->total_value) {
                return $secondUser->total_value <=> $firstUser->total_value;
            }

            return strcmp($firstUser->charactername, $secondUser->charactername);
        })->values();

        $userIndex = $rankedUsers->search(fn ($user) => (int) $user->id === $userId);
        $rank = $userIndex === false ? null : $userIndex + 1;
        $aheadUsers = collect();

        if ($rank && $rank > 1) {
            $userTotal = $rankedUsers[$userIndex]->total_value;
            $aheadTotal = $rankedUsers
                ->take($userIndex)
                ->pluck('total_value')
                ->filter(fn ($totalValue) => (int) $totalValue > (int) $userTotal)
                ->min();

            $aheadUsers = $rankedUsers
                ->take($userIndex)
                ->filter(fn ($user) => (int) $user->total_value === (int) $aheadTotal)
                ->values();
        }

        $aheadNames = $aheadUsers->take(4)->pluck('charactername')->implode(' | ');

        if ($aheadUsers->count() > 4) {
            $aheadNames .= ' | ...';
        }

        $aheadValue = $aheadUsers->isNotEmpty() ? $aheadUsers->first()->total_value . ' ' . $unit : '-';

        return [
            'rank' => $rank,
            'display' => $rank ? $rank . '. hely' : '-',
            'tooltip' => $rank === 1 ? 'Te vagy az első ^^' : 'Előtted: ' . ($aheadNames ?: 'Nincs') . ' (' . $aheadValue . ')',
            'previous_user_name' => $aheadNames ?: 'Nincs',
            'previous_user_value' => $aheadValue,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function getTopThreeReportCountsByUser(): array
    {
        $reportRows = collect();

        foreach (['reports', 'reports_closed'] as $table) {
            $reportRows = $reportRows->merge(DB::table($table)->select('user_id', 'created_at')->get());
        }

        $topThreeCounts = [];

        $reportRows
            ->groupBy(fn ($report) => Carbon::parse($report->created_at)->startOfWeek(Carbon::MONDAY)->toDateString())
            ->each(function ($weeklyReports) use (&$topThreeCounts) {
                $weeklyReports
                    ->groupBy('user_id')
                    ->map(fn ($userReports) => $userReports->count())
                    ->sortDesc()
                    ->keys()
                    ->take(3)
                    ->each(function ($weeklyUserId) use (&$topThreeCounts) {
                        $userId = (int) $weeklyUserId;
                        $topThreeCounts[$userId] = ($topThreeCounts[$userId] ?? 0) + 1;
                    });
            });

        return $topThreeCounts;
    }

    /**
     * Calculate the percentage of reports created by the user.
     *
     * @param string $userReportCount
     * @param string $allReportCount
     * @return int
     */
    private function calculatingUserReportPercentage($userReportCount, $allReportCount)
    {
        if ($userReportCount != '0' && $allReportCount != '0') {
            return round(($userReportCount / $allReportCount) * 100);
        }
        return 0;
    }

    /**
     * Calculate the minutes until having the top duty time.
     *
     * @param string $topDutyTime
     * @param string $dutyMinuteSum
     * @return int
     */
    private function calculatingMinutesUntilTopDutyTime($topDutyTime, $dutyMinuteSum)
    {
        if ($topDutyTime != '0' && $dutyMinuteSum != '0') {
            return $topDutyTime - $dutyMinuteSum;
        }
        return 0;
    }
}
