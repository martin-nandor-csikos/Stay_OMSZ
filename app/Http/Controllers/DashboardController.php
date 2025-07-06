<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DiscordController;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get top reports
        $topReports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('users.charactername', DB::raw('count(reports.user_id) as reportCount'))
            ->groupBy('users.charactername')
            ->orderBy('reportCount', 'desc')
            ->limit(5)
            ->get();

        // Get report count
        $reportCount = DB::table('reports')
            ->select(
                DB::raw('count(reports.user_id) as reportCount'),
            )
            ->where('reports.user_id', '=', $request->user()->id)
            ->groupBy('reports.user_id')
            ->orderBy('reportCount', 'desc')
            ->limit(1)
            ->get();
        if ($reportCount->isEmpty()) {
            $reportCount = collect([
                (object) [
                    'reportCount' => '0'
                ]
            ]);
        }

        // Get last report date
        $lastReportDate = DB::table('reports')
            ->select('reports.created_at')
            ->where('reports.user_id', '=', $request->user()->id)
            ->orderBy('reports.created_at', 'desc')
            ->limit(1)
            ->get();
        if ($lastReportDate->isEmpty()) {
            $lastReportDate = collect([
                (object) [
                    'created_at' => '-'
                ]
            ]);
        }

        // Get duty minute sum
        $dutyMinuteSum = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as dutyMinuteSum'),
            )
            ->where('duty_times.user_id', '=', $request->user()->id)
            ->limit(1)
            ->get();

        if ($dutyMinuteSum->isEmpty()) {
            $dutyMinuteSum = collect([
                (object) [
                    'dutyMinuteSum' => '0'
                ]
            ]);
        }

        // Get all report count
        $allReportCount = DB::table('reports')
            ->select(
                DB::raw('count(reports.id) as allReportCount'),
            )
            ->limit(1)
            ->get();

        if ($allReportCount->isEmpty()) {
            $allReportCount = collect([
                (object) [
                    'allReportCount' => '0'
                ]
            ]);
        }

        // Get top duty time
        $topDutyTime = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as topDutyTime'),
                'duty_times.user_id'
            )
            ->groupBy('user_id')
            ->orderBy('topDutyTime', 'desc')
            ->limit(1)
            ->get();
        if ($topDutyTime->isEmpty()) {
            $topDutyTime = collect([
                (object) [
                    'topDutyTime' => '0',
                    'user_id' => '-'
                ]
            ]);
        }

        // Get sum duty time
        $sumDutyTime = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as sumDutyTime'),
            )
            ->get();
        if ($sumDutyTime->isEmpty()) {
            $sumDutyTime = collect([
                (object) [
                    'sumDutyTime' => '0',
                ]
            ]);
        }

        // Percentage of reports created by the user
        if ($reportCount[0]->reportCount == '0' || $allReportCount[0]->allReportCount == '0') {
            $reportPercentageYouCreated = 0;
        } else {
            $reportPercentageYouCreated = round(($reportCount[0]->reportCount / $allReportCount[0]->allReportCount) * 100);
        }

        // Minutes until having the top duty time
        if ($topDutyTime[0]->topDutyTime == '0' || $dutyMinuteSum[0]->dutyMinuteSum == '0') {
            $minutesUntilTopDutyTime = 0;
        } else {
            $minutesUntilTopDutyTime = $topDutyTime[0]->topDutyTime - $dutyMinuteSum[0]->dutyMinuteSum;
        }

        $minimumDutyTime = 800;
        $minimumReportCount = 15;
        $minimumDoubleRankupDutyTime = 1800;
        $minimumDoubleRankupReportCount = 40;

        $discordAnnouncements = (new DiscordController())->getDiscordAnnouncements();

        return view('dashboard', [
            'topReports' => $topReports,
            'reportCount' => $reportCount[0]->reportCount,
            'lastReportDate' => $lastReportDate[0]->created_at,
            'dutyMinuteSum' => $dutyMinuteSum[0]->dutyMinuteSum,
            'allReportCount' => $allReportCount[0]->allReportCount,
            'userReportPercentage' => $reportPercentageYouCreated,
            'minutesUntilTopDutyTime' => $minutesUntilTopDutyTime,
            'minimumDutyTime' => $minimumDutyTime,
            'minimumReportCount' => $minimumReportCount,
            'minimumDoubleRankupDutyTime' => $minimumDoubleRankupDutyTime,
            'minimumDoubleRankupReportCount' => $minimumDoubleRankupReportCount,
            'sumDutyTime'=> $sumDutyTime[0]->sumDutyTime,
            'discordAnnouncements' => $discordAnnouncements,
        ]);
    }
}
