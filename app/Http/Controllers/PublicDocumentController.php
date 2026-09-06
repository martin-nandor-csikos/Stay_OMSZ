<?php

namespace App\Http\Controllers;

use App\Enums\InactivityStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PublicDocumentController extends Controller
{
    /**
     * Display the public, read-only user document.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = DB::table('users')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->select(
                'users.account_id',
                'users.charactername',
                'users.department',
                'users.created_at',
                'users.last_rank_change_at',
                'users.plus_points',
                'users.penalty_points',
                DB::raw('COALESCE(ranks.name, "-") as rank_name'),
                'ranks.rank_order'
            )
            ->selectRaw(
                'EXISTS (
                    SELECT 1
                    FROM inactivities
                    WHERE inactivities.user_id = users.id
                        AND inactivities.status = ?
                        AND ? BETWEEN inactivities.begin AND inactivities.end
                ) as is_inactive',
                [InactivityStatus::Accepted->value, Carbon::today()->toDateString()]
            )
            ->orderByRaw('ranks.rank_order IS NULL ASC')
            ->orderBy('ranks.rank_order', 'DESC')
            ->orderBy('users.charactername')
            ->get()
            ->map(function ($user) {
                $createdAt = Carbon::parse($user->created_at);
                $lastRankChangeAt = $user->last_rank_change_at ? Carbon::parse($user->last_rank_change_at) : $createdAt;

                $user->created_at_display = $createdAt->format('Y.m.d H:i');
                $user->last_rank_change_at_display = $lastRankChangeAt->format('Y.m.d H:i');
                $user->days_at_rank = $lastRankChangeAt->copy()->startOfDay()->diffInDays(Carbon::today());
                $user->days_in_faction = $createdAt->copy()->startOfDay()->diffInDays(Carbon::today());
                $user->status = $user->is_inactive ? 'Inaktív' : 'Aktív';

                return $user;
            });

        return view('public_document.index', ['users' => $users]);
    }

    /**
     * Display the public, read-only vehicle document.
     *
     * @return \Illuminate\View\View
     */
    public function vehicles()
    {
        $vehicles = DB::table('vehicles')
            ->leftJoin('users as caregiver', 'vehicles.caregiver_user_id', '=', 'caregiver.id')
            ->leftJoin('users as secondary_caregiver', 'vehicles.secondary_caregiver_user_id', '=', 'secondary_caregiver.id')
            ->select(
                'vehicles.vehicle_identifier',
                'vehicles.plate_number',
                'vehicles.type',
                DB::raw('COALESCE(caregiver.charactername, "-") as caregiver_name'),
                DB::raw('COALESCE(secondary_caregiver.charactername, "-") as secondary_caregiver_name')
            )
            ->orderBy('vehicles.vehicle_identifier')
            ->get();

        return view('public_document.vehicles', ['vehicles' => $vehicles]);
    }
}
