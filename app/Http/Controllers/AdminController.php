<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\InactivityStatus;
use App\Models\User;
use App\Models\Report;
use App\Models\Rank;
use App\Http\Controllers\TicketServiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DutyTimeController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * @var TicketServiceController
     */
    protected $ticketServiceController;

    /**
     * @var ReportController
     */
    protected $reportController;

    /**
     * @var DutyTimeController
     */
    protected $dutyTimeController;

    /**
     * @var SettingController
     */
    protected $settingController;

    /**
     * @var RankController
     */
    protected $rankController;

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        $this->ticketServiceController = new TicketServiceController();
        $this->reportController = new ReportController();
        $this->dutyTimeController = new DutyTimeController();
        $this->settingController = new SettingController();
        $this->rankController = new RankController();
    }

    /**
     * Display the admin page view with all necessary data
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userStats = $this->getWeeklyStatsQuery();
        $closedUserStats = $this->getClosedWeekStatsQuery();
        $inactivities = $this->getInactivitiesQuery();
        $pointUsers = $this->getPointUsersQuery();
        $departmentUsers = $this->getDepartmentUsersQuery();
        $vehicles = $this->getVehiclesQuery();
        $vehicleTypes = $this->getVehicleTypesQuery();
        $vehicleUsers = $this->getVehicleUsersQuery();
        $unassignedVehicleUserNames = $this->getUnassignedVehicleUserNames();
        $users = $this->getRegisteredUsersQuery();
        $deletedUsers = $this->getDeletedUsersQuery();
        $admin_logs = $this->getAdminLogsQuery();
        $ticketServices = $this->ticketServiceController->getServicesQuery();
        $ticketServicesWithUpdateTimes = $this->ticketServiceController->getServicesWithUpdateTimesQuery();
        $settings = $this->settingController->getSettingsQuery();
        $ranks = $this->rankController->getRanksQuery();
        $promotionUsers = $this->getPromotionUsersQuery();

        $firstDayOfWeek = Carbon::today()->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $lastDayOfWeek = Carbon::today()->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $firstDayOfPreviousWeek = Carbon::today()->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $lastDayOfPreviousWeek = Carbon::today()->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $currentDay = Carbon::today()->toDateString();

        $waitingForAnswerInInactivites = false;
        foreach ($inactivities as $inactivity) {
            if ($inactivity->status == InactivityStatus::WaitingForApproval->value) {
                $waitingForAnswerInInactivites = true;
            }

            if (Carbon::now()->between($inactivity->begin, $inactivity->end) && $inactivity->status == InactivityStatus::Accepted->value) {
                $inactivity->inProgress = true;
            } else {
                $inactivity->inProgress = false;
            }

            // Reformat the begin and end dates
            $inactivity->begin = Carbon::parse($inactivity->begin)->format('Y.m.d');
            $inactivity->end = Carbon::parse($inactivity->end)->format('Y.m.d');
        }

        return view('admin.admin_page', [
            'users' => $users,
            'deletedUsers' => $deletedUsers,
            'userStats' => $userStats,
            'closedUserStats' => $closedUserStats,
            'admin_logs' => $admin_logs,
            'inactivities' => $inactivities,
            'pointUsers' => $pointUsers,
            'departmentUsers' => $departmentUsers,
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'vehicleUsers' => $vehicleUsers,
            'unassignedVehicleUserNames' => $unassignedVehicleUserNames,
            'ticketServices' => $ticketServices,
            'ticketServicesWithUpdateTimes' => $ticketServicesWithUpdateTimes,
            'settings' => $settings,
            'ranks' => $ranks,
            'promotionUsers' => $promotionUsers,
            'waitingForAnswerInInactivites' => $waitingForAnswerInInactivites,
            'firstDayOfWeek' => $firstDayOfWeek,
            'lastDayOfWeek' => $lastDayOfWeek,
            'firstDayOfPreviousWeek' => $firstDayOfPreviousWeek,
            'lastDayOfPreviousWeek' => $lastDayOfPreviousWeek,
            'currentDay' => $currentDay,
        ]);
    }

    /**
     * Get the weekly statistics for each user.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getWeeklyStatsQuery()
    {
        $userStats = DB::table('users')
            ->leftJoin('reports', 'users.id', '=', 'reports.user_id')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->select(
                'users.id',
                'users.charactername',
                DB::raw('COALESCE(ranks.name, "-") as rank_name'),
                'ranks.salary as rank_salary',
                DB::raw('COALESCE(count(reports.user_id), 0) as reportCount'),
                DB::raw('COALESCE((SELECT MAX(reports.created_at) FROM reports WHERE reports.user_id = users.id), "-") as lastReportDate'),
                DB::raw('COALESCE((SELECT SUM(duty_times.minutes) FROM duty_times WHERE duty_times.user_id = users.id), 0) as dutyMinuteSum'),
                DB::raw('COALESCE((SELECT MAX(duty_times.end) FROM duty_times WHERE duty_times.user_id = users.id), "-") as lastDutyDate')
            )
            ->groupBy('users.id', 'users.charactername', 'ranks.name', 'ranks.salary')
            ->orderBy('reportCount', 'DESC')
            ->get();

        return $this->calculateWeeklySalaries($userStats);
    }

    /**
     * Calculate weekly salary for users in weekly stats
     *
     * @param \Illuminate\Support\Collection $userStats
     * @return \Illuminate\Support\Collection
     */
    private function calculateWeeklySalaries($userStats)
    {
        $settings = DB::table('settings')->first();
        $minReports = (int) ($settings->minimum_report_count ?? 15);
        $bonus1 = $settings->bonus_first_percentage ?? 0;
        $bonus2 = $settings->bonus_second_percentage ?? 0;
        $bonus3 = $settings->bonus_third_percentage ?? 0;

        foreach ($userStats as $index => $stat) {
            $reportCount = (int) ($stat->reportCount ?? 0);
            $rankSalary = (int) ($stat->rank_salary ?? 0);

            if ($reportCount < $minReports) {
                $stat->salary = 0;
                $stat->bonus_percentage = 0;
                $stat->salary_tooltip = 'Nincs meg a minimum jelentés szám';
                $stat->closed_week_message = "Sajnos nem kapsz fizetést, mivel nem teljesítetted a minimum jelentés számot ({$reportCount}/{$minReports} jelentést adtál le).";
            } else {
                $baseSalary = $reportCount * $rankSalary;

                $bonusPercent = 0;
                if ($reportCount > 0) {
                    if ($index === 0) {
                        $bonusPercent = $bonus1;
                    } elseif ($index === 1) {
                        $bonusPercent = $bonus2;
                    } elseif ($index === 2) {
                        $bonusPercent = $bonus3;
                    }
                }

                $bonusMultiplier = 1 + ($bonusPercent / 100);
                $stat->salary = (int) round($baseSalary * $bonusMultiplier);
                $stat->bonus_percentage = $bonusPercent;

                if ($bonusPercent > 0) {
                    $stat->salary_tooltip = "({$reportCount} * {$rankSalary}) * {$bonusMultiplier}";
                } else {
                    $stat->salary_tooltip = "{$reportCount} * {$rankSalary}";
                }
                $stat->closed_week_message = null;
            }

            DB::table('users')->where('id', $stat->id)->update([
                'salary' => $stat->salary,
                'salary_tooltip' => $stat->salary_tooltip,
            ]);
        }

        return $userStats;
    }

    /**
     * Get the closed week statistics for each user.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getClosedWeekStatsQuery()
    {
        return DB::table('users_closed')
            ->leftJoin('reports_closed', 'users_closed.id', '=', 'reports_closed.user_id')
            ->select(
                'users_closed.id',
                'users_closed.charactername',
                DB::raw('COALESCE(users_closed.rank_name, "-") as rank_name'),
                DB::raw('COALESCE(users_closed.salary, 0) as salary'),
                'users_closed.is_paid',
                'users_closed.payment_proof_url',
                DB::raw('COALESCE(users_closed.salary_tooltip, "") as salary_tooltip'),
                DB::raw('COALESCE(count(reports_closed.user_id), 0) as reportCount'),
                DB::raw('COALESCE((SELECT MAX(reports_closed.created_at) FROM reports_closed WHERE reports_closed.user_id = users_closed.id), "-") as lastReportDate'),
                DB::raw('COALESCE((SELECT SUM(duty_times_closed.minutes) FROM duty_times_closed WHERE duty_times_closed.user_id = users_closed.id), 0) as dutyMinuteSum'),
                DB::raw('COALESCE((SELECT MAX(duty_times_closed.end) FROM duty_times_closed WHERE duty_times_closed.user_id = users_closed.id), "-") as lastDutyDate')
            )
            ->groupBy('users_closed.id', 'users_closed.charactername', 'users_closed.rank_name', 'users_closed.salary', 'users_closed.is_paid', 'users_closed.payment_proof_url', 'users_closed.salary_tooltip')
            ->orderBy('reportCount', 'DESC')
            ->get();
    }

    /**
     * Return paid statuses for the currently displayed closed week.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClosedWeekPaidStatuses()
    {
        return response()->json(
            DB::table('users_closed')
                ->select('id', 'is_paid', 'payment_proof_url')
                ->get()
                ->mapWithKeys(function ($closedUser) {
                    return [
                        $closedUser->id => [
                            'is_paid' => (bool) $closedUser->is_paid,
                            'payment_proof_url' => $closedUser->payment_proof_url,
                        ],
                    ];
                })
        );
    }

    /**
     * Update one user's paid status for the currently displayed closed week.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClosedWeekPaidStatus(Request $request, string $id)
    {
        if (Auth::user()->adminLevel < 1) {
            abort(403);
        }

        $validated = $request->validate([
            'is_paid' => ['required', 'boolean'],
            'payment_proof_url' => ['required_if:is_paid,1', 'nullable', 'url', 'max:2048'],
        ]);

        $closedUser = DB::table('users_closed')->where('id', $id)->first();
        abort_unless($closedUser, 404);

        $paymentProofUrl = $validated['is_paid'] ? $validated['payment_proof_url'] : null;

        if ($paymentProofUrl && DB::table('users_closed')->where('payment_proof_url', $paymentProofUrl)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'message' => 'Ezt a képet már feltöltötted',
            ], 422);
        }

        DB::table('users_closed')->where('id', $id)->update([
            'is_paid' => $validated['is_paid'],
            'payment_proof_url' => $paymentProofUrl,
        ]);

        $paymentProofLogValue = $paymentProofUrl
            ? '<a href="' . e($paymentProofUrl) . '" target="_blank" rel="noopener noreferrer">' . e($paymentProofUrl) . '</a>'
            : 'nincs';

        $this->logAdminAction(($validated['is_paid'] ? 'Kifizette ' : 'Nem kifizetettként jelölte ') . e($closedUser->charactername) . ' felhasználót (kép: ' . $paymentProofLogValue . ').');

        return response()->json([
            'is_paid' => (bool) $validated['is_paid'],
            'payment_proof_url' => $paymentProofUrl,
        ]);
    }

    /**
     * Get the promotions overview for all users
     *
     * @return \Illuminate\Support\Collection
     */
    private function getPromotionUsersQuery()
    {
        $users = DB::table('users')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->select(
                'users.id',
                'users.charactername',
                'users.rank_id',
                'users.successful_weeks',
                'users.last_rank_change_at',
                'ranks.name as rank_name',
                'ranks.rank_order',
                'ranks.minimum_successful_weeks',
                'ranks.requires_exam',
                'ranks.is_leader'
            )
            ->orderBy('users.charactername', 'ASC')
            ->get();

        $allRanks = Rank::orderBy('rank_order', 'ASC')->get();
        $ranksByOrder = $allRanks->keyBy('rank_order');

        foreach ($users as $user) {
            $user->is_current_leader = (bool) ($user->is_leader ?? false);
            $currentOrder = $user->rank_order;
            if ($currentOrder !== null) {
                $user->next_rank = $ranksByOrder->get($currentOrder + 1);
            } else {
                $user->next_rank = $ranksByOrder->get(1);
            }

            $user->is_next_leader = $user->next_rank ? (bool) ($user->next_rank->is_leader ?? false) : false;
            $requiredWeeks = $user->minimum_successful_weeks ?? 2;
            $user->required_weeks = $requiredWeeks;
            $lastRankChange = $user->last_rank_change_at ? Carbon::parse($user->last_rank_change_at) : Carbon::now();
            $user->last_rank_change_display = $lastRankChange->format('Y.m.d H:i');
            $user->days_at_rank = $lastRankChange->startOfDay()->diffInDays(Carbon::today());

            if ($user->is_current_leader) {
                $user->is_eligible = false;
                $user->is_max_rank = false;
            } elseif ($user->next_rank === null) {
                $user->is_eligible = false;
                $user->is_max_rank = true;
            } else {
                $user->is_max_rank = false;
                $user->is_eligible = ($user->successful_weeks >= $requiredWeeks);
            }
        }

        return $users->sort(function ($a, $b) {
            $priorityA = $a->is_current_leader ? 0 : ($a->is_eligible ? 2 : 1);
            $priorityB = $b->is_current_leader ? 0 : ($b->is_eligible ? 2 : 1);

            if ($priorityA !== $priorityB) {
                return $priorityB <=> $priorityA;
            }

            return strcmp($a->charactername, $b->charactername);
        })->values();
    }

    /**
     * Get the inactivities
     *
     * @return \Illuminate\Support\Collection
     */
    private function getInactivitiesQuery()
    {
        return DB::table('inactivities')->join('users', 'users.id', '=', 'inactivities.user_id')->select('users.account_id', 'users.charactername', 'inactivities.begin', 'inactivities.end', 'inactivities.reason', 'inactivities.id', 'inactivities.status')->orderBy('inactivities.created_at', 'desc')->get();
    }

    private function getPointUsersQuery()
    {
        return DB::table('users')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->select(
                'users.id',
                'users.charactername',
                'users.plus_points',
                'users.penalty_points',
                'users.last_plus_point_at',
                'users.last_penalty_point_at',
                'ranks.name as rank_name'
            )
            ->orderBy('users.charactername')
            ->get();
    }

    private function getDepartmentUsersQuery()
    {
        return DB::table('users')
            ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
            ->select('users.id', 'users.charactername', 'ranks.name as rank_name', 'users.department', 'users.last_department_change_at')
            ->orderBy('users.charactername')
            ->get();
    }

    public function updateUserDepartments(Request $request)
    {
        if (Auth::user()->adminLevel < 1) {
            abort(403);
        }

        $validated = $request->validate([
            'users' => ['required', 'array'],
            'users.*.department' => ['required', 'in:MOK,MM,LMSZ,MGK'],
        ]);

        foreach ($validated['users'] as $userId => $values) {
            $user = User::find($userId);
            if (!$user || $user->department === $values['department']) {
                continue;
            }

            $oldDepartment = $user->department;
            $user->update([
                'department' => $values['department'],
                'last_department_change_at' => now(),
            ]);

            $this->logAdminAction('Frissítette ' . $user->charactername . ' alosztályát (' . $oldDepartment . ' -> ' . $values['department'] . ')');
        }

        return Redirect::route('admin.index')->with('departments-updated', 'Az alosztályok sikeresen frissítve.');
    }

    private function getVehiclesQuery()
    {
        return DB::table('vehicles')
            ->leftJoin('users as caregiver', 'vehicles.caregiver_user_id', '=', 'caregiver.id')
            ->leftJoin('users as secondary_caregiver', 'vehicles.secondary_caregiver_user_id', '=', 'secondary_caregiver.id')
            ->select(
                'vehicles.id',
                'vehicles.vehicle_identifier',
                'vehicles.plate_number',
                'vehicles.type',
                'vehicles.caregiver_user_id',
                'vehicles.secondary_caregiver_user_id',
                'caregiver.charactername as caregiver_name',
                'secondary_caregiver.charactername as secondary_caregiver_name'
            )
            ->orderBy('vehicles.vehicle_identifier')
            ->get();
    }

    private function getVehicleUsersQuery()
    {
        return DB::table('users')
            ->select('id', 'charactername')
            ->orderBy('charactername')
            ->get();
    }

    private function getVehicleTypesQuery()
    {
        return DB::table('vehicles')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->all();
    }

    private function getUnassignedVehicleUserNames()
    {
        return DB::table('users')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('vehicles')
                    ->whereColumn('vehicles.caregiver_user_id', 'users.id')
                    ->orWhereColumn('vehicles.secondary_caregiver_user_id', 'users.id');
            })
            ->orderBy('charactername')
            ->pluck('charactername')
            ->all();
    }

    public function updateVehicles(Request $request)
    {
        if (Auth::user()->adminLevel < 1) {
            abort(403);
        }

        $existingVehicles = DB::table('vehicles')->get()->keyBy('id');

        $rules = [
            'vehicles' => ['nullable', 'array'],
            'vehicles.*.caregiver_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'vehicles.*.secondary_caregiver_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];

        if (Auth::user()->adminLevel == 2) {
            $rules = array_merge($rules, [
                'vehicles.*.vehicle_identifier' => ['required', 'string', 'max:255'],
                'vehicles.*.plate_number' => ['required', 'string', 'max:255'],
                'vehicles.*.type' => ['required', 'string', 'max:255'],
                'new_vehicles' => ['nullable', 'array'],
                'new_vehicles.*.vehicle_identifier' => ['required', 'string', 'max:255'],
                'new_vehicles.*.plate_number' => ['required', 'string', 'max:255'],
                'new_vehicles.*.type' => ['required', 'string', 'max:255'],
                'new_vehicles.*.caregiver_user_id' => ['nullable', 'integer', 'exists:users,id'],
                'new_vehicles.*.secondary_caregiver_user_id' => ['nullable', 'integer', 'exists:users,id'],
            ]);
        }

        $validated = $request->validate($rules);
        $submittedVehicles = $validated['vehicles'] ?? [];

        foreach (array_merge($submittedVehicles, $validated['new_vehicles'] ?? []) as $vehicleData) {
            if (!empty($vehicleData['caregiver_user_id']) && !empty($vehicleData['secondary_caregiver_user_id']) && (int) $vehicleData['caregiver_user_id'] === (int) $vehicleData['secondary_caregiver_user_id']) {
                return Redirect::route('admin.index')->with('vehicles-not-updated', 'Egy járműnél az Ápoló és II. Ápoló nem lehet ugyanaz a felhasználó.');
            }
        }

        if (Auth::user()->adminLevel == 2) {
            $vehicleIdentifiers = [];

            foreach ($submittedVehicles as $vehicleId => $vehicleData) {
                if (isset($existingVehicles[$vehicleId])) {
                    $vehicleIdentifiers[] = $vehicleData['vehicle_identifier'];
                }
            }

            foreach ($validated['new_vehicles'] ?? [] as $vehicleData) {
                $vehicleIdentifiers[] = $vehicleData['vehicle_identifier'];
            }

            if (count($vehicleIdentifiers) !== count(array_unique($vehicleIdentifiers))) {
                return Redirect::route('admin.index')->with('vehicles-not-updated', 'A jármű ID-knek egyedinek kell lenniük.');
            }
        }

        try {
            if (Auth::user()->adminLevel == 2) {
                foreach ($existingVehicles as $vehicleId => $vehicle) {
                    if (!array_key_exists((string) $vehicleId, $submittedVehicles)) {
                        DB::table('vehicles')->where('id', $vehicleId)->delete();
                        $this->logAdminAction('Törölte a(z) ' . $vehicle->vehicle_identifier . ' járművet.');
                    }
                }
            }

            foreach ($submittedVehicles as $vehicleId => $vehicleData) {
                if (!isset($existingVehicles[$vehicleId])) {
                    continue;
                }

                $vehicle = $existingVehicles[$vehicleId];
                $updates = [
                    'caregiver_user_id' => $vehicleData['caregiver_user_id'] ?? null,
                    'secondary_caregiver_user_id' => $vehicleData['secondary_caregiver_user_id'] ?? null,
                ];

                if (Auth::user()->adminLevel == 2) {
                    $updates['vehicle_identifier'] = $vehicleData['vehicle_identifier'];
                    $updates['plate_number'] = $vehicleData['plate_number'];
                    $updates['type'] = $vehicleData['type'];
                }

                if ($this->vehicleUpdatesChanged($vehicle, $updates)) {
                    $updates['updated_at'] = now();
                    DB::table('vehicles')->where('id', $vehicleId)->update($updates);
                    $this->logAdminAction('Frissítette a(z) ' . $vehicle->vehicle_identifier . ' jármű adatait.');
                }
            }

            if (Auth::user()->adminLevel == 2) {
                foreach ($validated['new_vehicles'] ?? [] as $vehicleData) {
                    DB::table('vehicles')->insert([
                        'vehicle_identifier' => $vehicleData['vehicle_identifier'],
                        'plate_number' => $vehicleData['plate_number'],
                        'type' => $vehicleData['type'],
                        'caregiver_user_id' => $vehicleData['caregiver_user_id'] ?? null,
                        'secondary_caregiver_user_id' => $vehicleData['secondary_caregiver_user_id'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->logAdminAction('Létrehozta a(z) ' . $vehicleData['vehicle_identifier'] . ' járművet.');
                }
            }
        } catch (Exception $e) {
            return Redirect::route('admin.index')->with('vehicles-not-updated', 'A járművek mentése sikertelen.');
        }

        return Redirect::route('admin.index')->with('vehicles-updated', 'A járművek sikeresen frissítve.');
    }

    private function vehicleUpdatesChanged($vehicle, array $updates): bool
    {
        foreach ($updates as $field => $value) {
            if ((string) ($vehicle->$field ?? '') !== (string) ($value ?? '')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the registered users
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRegisteredUsersQuery()
    {
        return DB::table('users')->select('users.id', 'users.account_id', 'users.charactername', 'users.username', 'users.created_at', 'users.adminLevel')->orderBy('users.charactername', 'ASC')->get();
    }

    public function updateUserPoints(Request $request)
    {
        if (Auth::user()->adminLevel < 1) {
            abort(403);
        }

        $validated = $request->validate([
            'users' => ['required', 'array'],
            'users.*.plus_points' => ['required', 'integer', 'min:0'],
            'users.*.penalty_points' => ['required', 'integer', 'min:0'],
            'users.*.plus_points_reason' => ['nullable', 'string', 'max:1000'],
            'users.*.penalty_points_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($validated['users'] as $userId => $pointValues) {
            $user = User::find($userId);
            if (!$user) {
                continue;
            }

            $plusPoints = (int) $pointValues['plus_points'];
            $penaltyPoints = (int) $pointValues['penalty_points'];
            $oldPlusPoints = (int) $user->plus_points;
            $oldPenaltyPoints = (int) $user->penalty_points;
            $plusPointsReason = trim($pointValues['plus_points_reason'] ?? '');
            $penaltyPointsReason = trim($pointValues['penalty_points_reason'] ?? '');

            if ($plusPoints !== $oldPlusPoints && $plusPointsReason === '') {
                throw ValidationException::withMessages([
                    'users.' . $userId . '.plus_points_reason' => 'A pluszpont módosítását indokolni kell.',
                ]);
            }

            if ($penaltyPoints !== $oldPenaltyPoints && $penaltyPointsReason === '') {
                throw ValidationException::withMessages([
                    'users.' . $userId . '.penalty_points_reason' => 'A hibapont módosítását indokolni kell.',
                ]);
            }

            $updates = [
                'plus_points' => $plusPoints,
                'penalty_points' => $penaltyPoints,
            ];

            if ($plusPoints !== $oldPlusPoints) {
                $updates['last_plus_point_at'] = now();
                $this->recordUserPointHistory((int) $user->id, 'pluszpont', $oldPlusPoints, $plusPoints, $plusPointsReason);
                $this->logAdminAction('Frissítette ' . $user->charactername . ' pluszpontjait (' . $oldPlusPoints . ' -> ' . $plusPoints . '). Indok: ' . $plusPointsReason);
                $this->createUserAlertNotification((int) $user->id, 'point_change', [
                    'point_type' => 'pluszpont',
                    'old_value' => $oldPlusPoints,
                    'new_value' => $plusPoints,
                    'reason' => $plusPointsReason,
                ]);
            }

            if ($penaltyPoints !== $oldPenaltyPoints) {
                $updates['last_penalty_point_at'] = now();
                $this->recordUserPointHistory((int) $user->id, 'hibapont', $oldPenaltyPoints, $penaltyPoints, $penaltyPointsReason);
                $this->logAdminAction('Frissítette ' . $user->charactername . ' hibapontjait (' . $oldPenaltyPoints . ' -> ' . $penaltyPoints . '). Indok: ' . $penaltyPointsReason);
                $this->createUserAlertNotification((int) $user->id, 'point_change', [
                    'point_type' => 'hibapont',
                    'old_value' => $oldPenaltyPoints,
                    'new_value' => $penaltyPoints,
                    'reason' => $penaltyPointsReason,
                ]);
            }

            $user->update($updates);
        }

        return Redirect::route('admin.index')->with('points-updated', 'A plusz- és hibapontok sikeresen frissítve.');
    }

    private function recordUserPointHistory(int $userId, string $pointType, int $oldValue, int $newValue, string $reason): void
    {
        if (!Schema::hasTable('user_point_histories')) {
            return;
        }

        DB::table('user_point_histories')->insert([
            'user_id' => $userId,
            'point_type' => $pointType,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'reason' => $reason,
            'changed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get deleted users for the archive.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getDeletedUsersQuery()
    {
        return DB::table('deleted_users')->orderBy('deleted_at', 'DESC')->get();
    }

    /**
     * Get the admin logs
     *
     * @return \Illuminate\Support\Collection
     */
    private function getAdminLogsQuery()
    {
        return DB::table('admin_logs')
            ->join('users', 'users.id', '=', 'admin_logs.user_id')
            ->select('users.charactername', 'admin_logs.didWhat', 'admin_logs.created_at')
            ->orderByDesc('admin_logs.created_at')
            ->orderByDesc('admin_logs.id')
            ->get();
    }

    /**
     * Return admin logs for automatic table refresh.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdminLogs()
    {
        return response()->json(
            $this->getAdminLogsQuery()->values()->map(function ($adminLog, $index) {
                return [
                    'row_number' => $index + 1,
                    'charactername' => e($adminLog->charactername),
                    'didWhat' => str_contains($adminLog->didWhat, '<a href=') ? $adminLog->didWhat : e($adminLog->didWhat),
                    'created_at' => Carbon::parse($adminLog->created_at)->format('Y.m.d H:i'),
                ];
            })
        );
    }

    /**
     * Close the current week by moving reports and duty times to closed tables and resetting the current tables.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function closeWeek()
    {
        $currentWeekReportCount = $this->reportController->getReportCountForCurrentWeek();
        $currentWeekDutyCount = $this->dutyTimeController->getDutyCountForCurrentWeek();

        // If there are no reports and duties, return with failure
        if ($currentWeekReportCount == 0 && $currentWeekDutyCount == 0) {
            return Redirect::route('admin.index')->with('close-failed', 'A hét lezárása sikertelen. Üres a jelenlegi hét.');
        }

        $lockCloseWeek = DB::table('locks')
            ->where('name', 'close_week')
            ->where('isLocked', 0)
            ->update(['isLocked' => 1]);

        // If somebody is already closing the week, return with failure
        if ($lockCloseWeek == 0) {
            return Redirect::route('admin.index')->with('close-failed', 'A hét lezárása sikertelen. Művelet már folyamatban van.');
        }

        if ($this->moveWeeklyStatsToClosed()) {
            return Redirect::route('admin.index')->with('close-success', 'A hét sikeresen lezárva.');
        }
        return Redirect::route('admin.index')->with('close-failed', 'A hét lezárása sikertelen.');
    }

    public function userRegistrationPage()
    {
        return view('admin.users.register_user');
    }

    public function registerUser(Request $request)
    {
        $request->validate(
            [
                'account_id' => ['required', 'integer', 'min:1', 'unique:users,account_id'],
                'charactername' => ['required', 'string', 'max:255'],
            ],
            [
                'account_id.required' => 'Az Account ID nem lehet üres.',
                'account_id.integer' => 'Az Account ID csak egész szám lehet.',
                'account_id.min' => 'Az Account ID legalább 1 lehet.',
                'account_id.unique' => 'Ez az Account ID már foglalt.',
                'charactername.required' => 'Az IC név nem lehet üres.',
                'charactername.max' => 'Túl hosszú az IC név.',
            ],
        );

        try {
            $randomUsername = Str::random(8);
            $randomPassword = Str::random(8);
            $lowestRank = Rank::where('rank_order', 1)->first();

            $user = User::create([
                'account_id' => $request->account_id,
                'charactername' => $request->charactername,
                'username' => $randomUsername,
                'password' => Hash::make($randomPassword),
                'rank_id' => $lowestRank ? $lowestRank->id : null,
                'department' => 'MGK',
                'last_rank_change_at' => now(),
                'highest_rank' => $lowestRank ? $lowestRank->name : null,
            ]);

            $this->logAdminAction('Regisztrált egy új felhasználót ' . $user->charactername . ' IC néven.');

            return Redirect::route('admin.index')->with('user-created', 'A felhasználó regisztrációja sikeres. FELHASZNÁLÓNÉV: ' . $randomUsername . ', JELSZÓ: ' . $randomPassword);
        } catch (\Throwable $th) {
            return Redirect::route('admin.index')->with('user-not-created', 'A felhasználó regisztrációja sikertelen.');
        }
    }

    public function viewUserReports(string $id)
    {
        $reports = $this->reportController->getUserReports($id);
        $userCharactername = $this->getCharacterNameById($id);

        if ($reports->isEmpty()) {
            return Redirect::route('admin.index');
        }

        return view('admin.weekly_stats.view_user_reports', [
            'reports' => $reports,
            'charactername' => $userCharactername,
        ]);
    }

    public function viewUserDuty(string $id)
    {
        $dutyTimes = DB::table('duty_times')->join('users', 'users.id', '=', 'duty_times.user_id')->select('duty_times.id', 'duty_times.begin', 'duty_times.end', 'duty_times.minutes', 'users.charactername')->where('user_id', '=', $id)->get();

        if ($dutyTimes->isEmpty()) {
            return Redirect::route('admin.index');
        }

        return view('admin.weekly_stats.view_user_duty', [
            'dutyTimes' => $dutyTimes,
            'charactername' => $dutyTimes[0]->charactername,
        ]);
    }

    /**
     * View a previous week's reports for the given user.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function viewClosedUserReports(string $id)
    {
        $reportsFromClosedWeek = $this->reportController->getUserReportsFromClosedWeek($id);
        $userCharacterName = $this->getCharacterNameById($id);

        if ($reportsFromClosedWeek->isEmpty()) {
            return Redirect::route('admin.index');
        }

        return view('admin.weekly_stats.view_user_reports', [
            'reports' => $reportsFromClosedWeek,
            'charactername' => $userCharacterName,
        ]);
    }

    public function viewClosedUserDuty(string $id)
    {
        $dutyTimes = DB::table('duty_times_closed')->join('users_closed', 'users_closed.id', '=', 'duty_times_closed.user_id')->select('duty_times_closed.id', 'duty_times_closed.begin', 'duty_times_closed.end', 'duty_times_closed.minutes', 'users_closed.charactername')->where('user_id', '=', $id)->get();

        return view('admin.weekly_stats.view_user_duty', [
            'dutyTimes' => $dutyTimes,
            'charactername' => $dutyTimes[0]->charactername,
        ]);
    }

    public function editUser(string $id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.update_user', [
            'user' => $user,
        ]);
    }

    public function updateUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $usernameCheck = $request->input('username') !== $user->username;
        $accountIdCheck = (int) $request->input('account_id') !== (int) $user->account_id;

        // Only users with adminLevel 2 (can give admin) may change another user's admin level.
        if (Auth::user()->adminLevel == 2 && Auth::user()->username != $user->username && $request->has('adminLevel')) {
            $request->validate(
                [
                    'adminLevel' => ['required', 'integer', 'in:0,1,2'],
                ],
                [
                    'adminLevel.required' => 'Az admin szint nem lehet üres.',
                    'adminLevel.in' => 'Érvénytelen admin szint.',
                ],
            );

            $newAdminLevel = (int) $request->input('adminLevel');

            if ($newAdminLevel !== (int) $user->adminLevel) {
                $this->logAdminAction('Frissítette ' . $user->charactername . ' admin szintjét (' . $user->adminLevel . ' -> ' . $newAdminLevel . ')');
                $user->adminLevel = $newAdminLevel;
            }
        }

        $request->validate(
            [
                'account_id' => ['required', 'integer', 'min:1', 'unique:users,account_id,' . $user->id],
            ],
            [
                'account_id.required' => 'Az Account ID nem lehet üres.',
                'account_id.integer' => 'Az Account ID csak egész szám lehet.',
                'account_id.min' => 'Az Account ID legalább 1 lehet.',
                'account_id.unique' => 'Ez az Account ID már foglalt.',
            ],
        );

        // Check if username was changed, if not, then don't validate for unique
        if ($usernameCheck) {
            $request->validate(
                [
                    'username' => ['string', 'max:255', 'unique:users'],
                ],
                [
                    'username.string' => 'A felhasználónév nem lehet üres.',
                    'username.unique' => 'Ez a felhasználónév már foglalt.',
                    'username.max' => 'Túl hosszú a felhasználónév.',
                ],
            );
        } else {
            $request->validate(
                [
                    'username' => ['string', 'max:255'],
                ],
                [
                    'username.string' => 'A felhasználónév nem lehet üres.',
                    'username.max' => 'Túl hosszú a felhasználónév.',
                ],
            );
        }

        $request->validate(
            [
                'charactername' => ['required', 'string', 'max:255'],
            ],
            [
                'charactername.string' => 'Az IC név nem lehet üres.',
                'charactername.max' => 'Túl hosszú az IC név.',
            ],
        );

        if ($accountIdCheck) {
            $oldAccountId = $user->account_id;
            $user->account_id = $request->input('account_id');

            $this->logAdminAction('Frissítette ' . $user->charactername . ' Account ID-ját (' . $oldAccountId . ' -> ' . $request->input('account_id') . ')');
        }

        if ($request->input('username') !== $user->username) {
            $oldusername = $user->username;
            $user->username = $request->input('username');

            $this->logAdminAction('Frissítette ' . $user->charactername . ' felhasználónevét (' . $oldusername . ' -> ' . $request->input('username') . ')');
        }

        if ($request->input('charactername') !== $user->charactername) {
            $oldcharactername = $user->charactername;
            $user->charactername = $request->input('charactername');

            DB::table('user_name_histories')->insert([
                'user_id' => $user->id,
                'charactername' => $oldcharactername,
            ]);

            $this->logAdminAction('Frissítette ' . $oldcharactername . ' IC nevét (' . $oldcharactername . ' -> ' . $request->input('charactername') . ')');
        }

        try {
            $user->save();

            return Redirect::route('admin.index')->with('user-updated', 'A felhasználó frissítése sikeres.');
        } catch (\Throwable $th) {
            return Redirect::route('admin.index')->with('user-not-updated', 'A felhasználó frissítése sikertelen.');
        }
    }

    public function deleteUser(string $id)
    {
        if (Auth::user()->id != $id) {
            request()->validate([
                'reason' => ['required', 'string', 'max:1000'],
                'blacklist' => ['required', 'in:-,Aktív,Erősített'],
            ], [
                'reason.required' => 'A törlés indoklása kötelező.',
                'reason.max' => 'A törlés indoklása maximum 1000 karakter lehet.',
                'blacklist.required' => 'A feketelista állapotát kötelező kiválasztani.',
                'blacklist.in' => 'Érvénytelen feketelista állapot.',
            ]);

            try {
                $user = User::findOrFail($id);
                $previousNames = DB::table('user_name_histories')
                    ->where('user_id', $user->id)
                    ->orderBy('id')
                    ->pluck('charactername')
                    ->all();

                $deletedUserId = DB::table('deleted_users')->insertGetId([
                    'account_id' => $user->account_id,
                    'charactername' => $user->charactername,
                    'previous_characternames' => empty($previousNames) ? null : json_encode($previousNames),
                    'highest_rank' => $user->highest_rank ?? ($user->rank ? $user->rank->name : null),
                    'department' => $user->department,
                    'registered_at' => $user->created_at,
                    'deleted_at' => now(),
                    'reason' => request()->input('reason'),
                    'blacklist' => request()->input('blacklist'),
                    'penalty_points' => $user->penalty_points,
                ]);

                if (Schema::hasTable('user_point_histories')) {
                    DB::table('user_point_histories')
                        ->where('user_id', $user->id)
                        ->whereNull('deleted_user_id')
                        ->update(['deleted_user_id' => $deletedUserId]);
                }

                $user->delete();

                $this->logAdminAction('Kitörölte ' . $user->charactername . ' felhasználót. Indok: ' . request()->input('reason') . '. Feketelista: ' . request()->input('blacklist'));

                return to_route('admin.index')->with('successful-user-deletion', 'A felhasználó törlése sikeres.');
            } catch (\Throwable $th) {
                return to_route('admin.index')->with('unsuccessful-user-deletion', 'A felhasználó törlése sikertelen.');
            }
        }
    }

    public function deleteReport(string $id)
    {
        $report = Report::findOrFail($id);
        $userId = $report->user_id;
        try {
            $characterName = $this->getCharacterNameById($userId);

            $report->delete();

            (new DashboardController())->refreshPersonalStatistics();

            $this->logAdminAction('Kitörölte a(z) ' . $characterName . ' (Jelentés ID: ' . $id . ') felhasználó jelentését');

            return Redirect::route('admin.viewUserReports', $userId)->with('successful-user-report-deletion', 'A felhasználó jelentésének törlése sikeres.');
        } catch (\Throwable $th) {
            return Redirect::route('admin.viewUserReports', $userId)->with('unsuccessful-user-report-deletion', 'A felhasználó jelentésének törlése sikertelen.');
        }
    }

        public function viewUserPointHistories(string $id)
        {
            $user = User::findOrFail($id);

            return view('admin.points.view_user_points', [
                'charactername' => $user->charactername,
                'plusPointHistories' => $this->getUserPointHistories((int) $user->id, 'pluszpont'),
                'penaltyPointHistories' => $this->getUserPointHistories((int) $user->id, 'hibapont'),
            ]);
        }

        public function viewDeletedUserPointHistories(string $id)
        {
            $deletedUser = DB::table('deleted_users')->where('id', $id)->first();
            abort_unless($deletedUser, 404);

            return view('admin.points.view_user_points', [
                'charactername' => $deletedUser->charactername,
                'plusPointHistories' => $this->getDeletedUserPointHistories((int) $deletedUser->id, 'pluszpont'),
                'penaltyPointHistories' => $this->getDeletedUserPointHistories((int) $deletedUser->id, 'hibapont'),
            ]);
        }

        private function getUserPointHistories(int $userId, string $pointType)
        {
            return DB::table('user_point_histories')
                ->where('user_id', $userId)
                ->where('point_type', $pointType)
                ->orderByDesc('changed_at')
                ->orderByDesc('id')
                ->get();
        }

        private function getDeletedUserPointHistories(int $deletedUserId, string $pointType)
        {
            return DB::table('user_point_histories')
                ->where('deleted_user_id', $deletedUserId)
                ->where('point_type', $pointType)
                ->orderByDesc('changed_at')
                ->orderByDesc('id')
                ->get();
        }

    /**
     * Log the given admin action for the current user
     *
     * @param string $action
     */
    public function logAdminAction($action)
    {
        DB::table('admin_logs')->insert([
            'user_id' => Auth::user()->id,
            'didWhat' => $action,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get the character name by user ID.
     *
     * @param int $id
     * @return string|null
     */
    public function getCharacterNameById($id)
    {
        return DB::table('users')->select('charactername')->where('id', '=', $id)->value('charactername');
    }

    public function updateUserRanks(Request $request)
    {
        if (Auth::user()->adminLevel < 1) {
            abort(403);
        }

        $userRanks = $request->input('user_ranks', []);
        $ranks = Rank::all()->keyBy('id');

        foreach ($userRanks as $userId => $newRankId) {
            $user = User::find($userId);
            if (!$user) {
                continue;
            }

            $currentRankId = $user->rank_id ? (string)$user->rank_id : '';
            $targetRankId = !empty($newRankId) ? (string)$newRankId : '';

            if ($currentRankId !== $targetRankId) {
                $oldRankName = $user->rank ? $user->rank->name : 'Nincs';
                $newRank = !empty($newRankId) ? $ranks->get($newRankId) : null;

                if (Auth::user()->adminLevel == 1 && !$this->canLevelOnePromoteToRank($user, $newRank)) {
                    abort(403);
                }

                $newRankName = $newRank ? $newRank->name : 'Nincs';

                $oldOrder = $user->rank ? $user->rank->rank_order : 0;
                $newOrder = $newRank ? $newRank->rank_order : 0;
                $rankChangeType = $newOrder < $oldOrder ? 'demotion' : 'promotion';

                $requiredWeeks = $user->rank ? (int) $user->rank->minimum_successful_weeks : 2;
                $carryOverWeeks = ($rankChangeType === 'promotion') ? max(0, (int) $user->successful_weeks - $requiredWeeks) : 0;
                $highestRankName = $user->highest_rank;

                if ($newRank && ($newOrder > $oldOrder || empty($highestRankName))) {
                    $highestRankName = $newRank->name;
                }

                $user->update([
                    'rank_id' => !empty($newRankId) ? (int)$newRankId : null,
                    'last_rank_change_at' => now(),
                    'highest_rank' => $highestRankName,
                    'successful_weeks' => $carryOverWeeks,
                ]);

                $this->createUserAlertNotification((int) $user->id, 'rank_change', [
                    'from_rank' => $oldRankName,
                    'to_rank' => $newRankName,
                    'change_type' => $rankChangeType,
                ]);

                $this->logAdminAction('Módosította a(z) ' . $user->charactername . ' felhasználó rangját (' . $oldRankName . ' -> ' . $newRankName . ')');
            }
        }

        return Redirect::route('admin.index')->with('promotions-updated', 'A rangok sikeresen frissültek.');
    }

    private function canLevelOnePromoteToRank(User $user, ?Rank $newRank): bool
    {
        if (!$newRank || $newRank->is_leader) {
            return false;
        }

        $currentRank = $user->rank;
        $expectedOrder = $currentRank ? $currentRank->rank_order + 1 : 1;

        if ((int) $newRank->rank_order !== (int) $expectedOrder) {
            return false;
        }

        $requiredWeeks = $currentRank ? (int) $currentRank->minimum_successful_weeks : 2;

        return (int) $user->successful_weeks >= $requiredWeeks;
    }

    public function promoteUser(Request $request, string $id)
    {
        if (Auth::user()->adminLevel != 2) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $currentRank = $user->rank;

        if ($currentRank) {
            $nextRank = Rank::where('rank_order', $currentRank->rank_order + 1)->first();
        } else {
            $nextRank = Rank::where('rank_order', 1)->first();
        }

        if (!$nextRank) {
            return Redirect::route('admin.index')->with('promotion-failed', 'Nincs magasabb rang.');
        }

        $requiredWeeks = $currentRank ? (int) $currentRank->minimum_successful_weeks : 2;
        $carryOverWeeks = max(0, (int) $user->successful_weeks - $requiredWeeks);

        $oldRankName = $currentRank ? $currentRank->name : 'Nincs';
        $newRankName = $nextRank->name;

        $user->update([
            'rank_id' => $nextRank->id,
            'last_rank_change_at' => now(),
            'highest_rank' => $nextRank->name,
            'successful_weeks' => $carryOverWeeks,
        ]);

        $this->createUserAlertNotification((int) $user->id, 'rank_change', [
            'from_rank' => $oldRankName,
            'to_rank' => $newRankName,
            'change_type' => 'promotion',
        ]);

        $this->logAdminAction('Előléptette a(z) ' . $user->charactername . ' felhasználót (' . $oldRankName . ' -> ' . $newRankName . ')');

        return Redirect::route('admin.index')->with('promotions-updated', $user->charactername . ' sikeresen előléptetve: ' . $newRankName);
    }

    /**
     * Move the weekly statistics to closed tables and reset the current tables.
     *
     * @return bool
     */
    private function moveWeeklyStatsToClosed()
    {
        try {
            $weeklyStats = $this->getWeeklyStatsQuery();
            $settings = DB::table('settings')->first();
            $minReports = (int) ($settings->minimum_report_count ?? 15);
            $minDuty = (int) ($settings->minimum_duty_time ?? 800);
            $doubleReports = (int) ($settings->double_week_report_count ?? 40);
            $doubleDuty = (int) ($settings->double_week_duty_time ?? 1800);
            $this->addWeeklyStatsToPersonalStatistics($weeklyStats);

            DB::delete('DELETE FROM reports_closed');
            DB::delete('DELETE FROM duty_times_closed');
            DB::delete('DELETE FROM users_closed');

            foreach ($weeklyStats as $stat) {
                DB::table('users_closed')->insert([
                    'id' => $stat->id,
                    'charactername' => $stat->charactername,
                    'salary' => $stat->salary ?? 0,
                    'salary_tooltip' => $stat->salary_tooltip ?? '',
                    'rank_name' => $stat->rank_name ?? '-',
                ]);

                DB::table('users')->where('id', $stat->id)->update([
                    'salary' => $stat->salary ?? 0,
                    'salary_tooltip' => $stat->salary_tooltip ?? '',
                ]);

                $this->createUserAlertNotification((int) $stat->id, 'closed_week_salary', [
                    'salary' => $stat->salary ?? 0,
                    'bonus' => $stat->bonus_percentage ?? 0,
                    'calculation' => $stat->salary_tooltip ?? '',
                    'message' => $stat->closed_week_message ?? null,
                ]);

                // Calculate successful weeks for each user
                $reportCount = (int) ($stat->reportCount ?? 0);
                $dutyMinutes = (int) ($stat->dutyMinuteSum ?? 0);
                $weeksToAdd = 0;

                if ($reportCount >= $doubleReports && $dutyMinutes >= $doubleDuty) {
                    $weeksToAdd = 2;
                } elseif (
                    ($reportCount >= $minReports && $dutyMinutes >= $minDuty) ||
                    $reportCount >= $doubleReports ||
                    $dutyMinutes >= $doubleDuty
                ) {
                    $weeksToAdd = 1;
                }

                if ($weeksToAdd > 0) {
                    DB::table('users')->where('id', $stat->id)->increment('successful_weeks', $weeksToAdd);
                }
            }

            DB::insert('INSERT INTO reports_closed SELECT * FROM reports');
            DB::insert('INSERT INTO duty_times_closed SELECT * FROM duty_times');

            DB::delete('DELETE FROM reports');
            DB::delete('DELETE FROM duty_times');

            $this->logAdminAction('Lezárta a hetet');

            DB::table('locks')
                ->where('name', 'close_week')
                ->update(['isLocked' => 0]);

            return true;
        } catch (\Exception $e) {
            DB::table('locks')
                ->where('name', 'close_week')
                ->update(['isLocked' => 0]);

            return false;
        }
    }

    private function addWeeklyStatsToPersonalStatistics($weeklyStats): void
    {
        if (!Schema::hasTable('personal_statistics')) {
            return;
        }

        $topThreeReportUserIds = $weeklyStats
            ->filter(fn ($stat) => (int) ($stat->reportCount ?? 0) > 0)
            ->sortByDesc(fn ($stat) => (int) ($stat->reportCount ?? 0))
            ->take(3)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($weeklyStats as $stat) {
            $userId = (int) $stat->id;
            $existingStatistic = DB::table('personal_statistics')->where('user_id', $userId)->first();
            $values = [
                'total_report_count' => (int) ($existingStatistic->total_report_count ?? 0) + (int) ($stat->reportCount ?? 0),
                'total_duty_minutes' => (int) ($existingStatistic->total_duty_minutes ?? 0) + (int) ($stat->dutyMinuteSum ?? 0),
                'top_three_report_count' => (int) ($existingStatistic->top_three_report_count ?? 0) + (in_array($userId, $topThreeReportUserIds, true) ? 1 : 0),
                'total_salary' => (int) ($existingStatistic->total_salary ?? 0) + (int) ($stat->salary ?? 0),
                'updated_at' => now(),
            ];

            if ($existingStatistic) {
                DB::table('personal_statistics')->where('user_id', $userId)->update($values);
                continue;
            }

            DB::table('personal_statistics')->insert(array_merge($values, [
                'user_id' => $userId,
                'created_at' => now(),
            ]));
        }
    }

    private function createUserAlertNotification(int $userId, string $type, array $payload): void
    {
        DB::table('user_alert_notifications')->insert([
            'user_id' => $userId,
            'type' => $type,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
