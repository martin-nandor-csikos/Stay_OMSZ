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
    public function index()
    {
        $topReports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('users.username', DB::raw('count(reports.user_id) as reportCount'))
            ->groupBy('users.username')
            ->orderBy('reportCount', 'desc')
            ->limit(5)
            ->get();

        $userStats = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('users.username', DB::raw('count(reports.user_id) as reportCount'))
            ->groupBy('users.username')
            ->orderBy('reportCount', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'topReports' => $topReports,
        ]);
    }
}
