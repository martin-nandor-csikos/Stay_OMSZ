<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
