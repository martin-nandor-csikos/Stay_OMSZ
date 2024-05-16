<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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

        $lastReportDate = DB::table('reports')
            ->select('reports.created_at')
            ->where('reports.user_id', '=', $request->user()->id)
            ->orderBy('reports.created_at', 'desc')
            ->limit(1)
            ->get();

        $dutyMinuteSum = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as dutyMinuteSum'),
            )
            ->where('duty_times.user_id', '=', $request->user()->id)
            ->limit(1)
            ->get();

        $allReportCount = DB::table('reports')
            ->select(
                DB::raw('count(reports.id) as allReportCount'),
            )
            ->limit(1)
            ->get();

        /*
                DB::raw('(SELECT reports.created_at FROM reports WHERE reports.user_id = users.id ORDER BY reports.created_at DESC LIMIT 1) as lastReportDate'),
                DB::raw('count(reports.user_id) as reportCount'),
                DB::raw('sum(duty_times.minutes) as dutyMinuteSum'),
                DB::raw('(SELECT SUM(reports.id) FROM reports) as allReportCount'),
                DB::raw('(SELECT SUM(duty_times.minutes) FROM duty_times) as allDutyMinuteSum'),
        */
        $percentage = round(($reportCount[0]->reportCount / $allReportCount[0]->allReportCount) * 100);
        return view('dashboard', [
            'topReports' => $topReports,
            'reportCount' => $reportCount[0]->reportCount,
            'lastReportDate' => $lastReportDate[0]->created_at,
            'dutyMinuteSum' => $dutyMinuteSum[0]->dutyMinuteSum,
            'allReportCount' => $allReportCount[0]->allReportCount,
            'userReportPercentage' => $percentage,
        ]);
    }
}
