<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardTable(Request $request)
    {
        $topReports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('users.charactername', DB::raw('count(reports.user_id) as reportCount'))
            ->groupBy('users.charactername')
            ->orderBy('reportCount', 'desc')
            ->limit(5)
            ->get();

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

        $topDutyTime = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as topDutyTime'),
                'duty_times.user_id'
            )
            ->groupBy('user_id')
            ->orderBy('topDutyTime', 'desc')
            ->limit(1)
            ->get();

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

        if ($topDutyTime->isEmpty()) {
            $topDutyTime = collect([
                (object) [
                    'topDutyTime' => '0',
                    'user_id' => '-'
                ]
            ]);
        }

        if ($reportCount[0]->reportCount == '0' || $allReportCount[0]->allReportCount == '0') {
            $percentage = 0;
        } else {
            $percentage = round(($reportCount[0]->reportCount / $allReportCount[0]->allReportCount) * 100);
        }

        if ($topDutyTime[0]->topDutyTime == '0' || $dutyMinuteSum[0]->dutyMinuteSum == '0') {
            $minutesUntilTopDutyTime = 0;
        } else {
            $minutesUntilTopDutyTime = $topDutyTime[0]->topDutyTime - $dutyMinuteSum[0]->dutyMinuteSum;
        }

        $minimumDutyTime = 500;
        $minimumReportCount = 10;

        return view('view_dashboard', [
            'topReports' => $topReports,
            'reportCount' => $reportCount[0]->reportCount,
            'lastReportDate' => $lastReportDate[0]->created_at,
            'dutyMinuteSum' => $dutyMinuteSum[0]->dutyMinuteSum,
            'allReportCount' => $allReportCount[0]->allReportCount,
            'userReportPercentage' => $percentage,
            'minutesUntilTopDutyTime' => $minutesUntilTopDutyTime,
            'minimumDutyTime' => $minimumDutyTime,
            'minimumReportCount' => $minimumReportCount,
            'sumDutyTime'=> $sumDutyTime[0]->sumDutyTime,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $topReports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('users.charactername', DB::raw('count(reports.user_id) as reportCount'))
            ->groupBy('users.charactername')
            ->orderBy('reportCount', 'desc')
            ->limit(5)
            ->get();

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

        $topDutyTime = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as topDutyTime'),
                'duty_times.user_id'
            )
            ->groupBy('user_id')
            ->orderBy('topDutyTime', 'desc')
            ->limit(1)
            ->get();

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

        if ($topDutyTime->isEmpty()) {
            $topDutyTime = collect([
                (object) [
                    'topDutyTime' => '0',
                    'user_id' => '-'
                ]
            ]);
        }

        if ($reportCount[0]->reportCount == '0' || $allReportCount[0]->allReportCount == '0') {
            $percentage = 0;
        } else {
            $percentage = round(($reportCount[0]->reportCount / $allReportCount[0]->allReportCount) * 100);
        }

        if ($topDutyTime[0]->topDutyTime == '0' || $dutyMinuteSum[0]->dutyMinuteSum == '0') {
            $minutesUntilTopDutyTime = 0;
        } else {
            $minutesUntilTopDutyTime = $topDutyTime[0]->topDutyTime - $dutyMinuteSum[0]->dutyMinuteSum;
        }

        $minimumDutyTime = 500;
        $minimumReportCount = 10;

        return view('dashboard', [
            'topReports' => $topReports,
            'reportCount' => $reportCount[0]->reportCount,
            'lastReportDate' => $lastReportDate[0]->created_at,
            'dutyMinuteSum' => $dutyMinuteSum[0]->dutyMinuteSum,
            'allReportCount' => $allReportCount[0]->allReportCount,
            'userReportPercentage' => $percentage,
            'minutesUntilTopDutyTime' => $minutesUntilTopDutyTime,
            'minimumDutyTime' => $minimumDutyTime,
            'minimumReportCount' => $minimumReportCount,
            'sumDutyTime'=> $sumDutyTime[0]->sumDutyTime,
        ]);
    }
}
