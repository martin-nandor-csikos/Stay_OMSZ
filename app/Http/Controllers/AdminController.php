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
        $users = $this->getRegisteredUsersQuery();
        $admin_logs = $this->getAdminLogsQuery();
        $ticketServices = $this->ticketServiceController->getServicesQuery();
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
            'userStats' => $userStats,
            'closedUserStats' => $closedUserStats,
            'admin_logs' => $admin_logs,
            'inactivities' => $inactivities,
            'ticketServices' => $ticketServices,
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
                DB::raw('COALESCE(users_closed.salary_tooltip, "") as salary_tooltip'),
                DB::raw('COALESCE(count(reports_closed.user_id), 0) as reportCount'),
                DB::raw('COALESCE((SELECT MAX(reports_closed.created_at) FROM reports_closed WHERE reports_closed.user_id = users_closed.id), "-") as lastReportDate'),
                DB::raw('COALESCE((SELECT SUM(duty_times_closed.minutes) FROM duty_times_closed WHERE duty_times_closed.user_id = users_closed.id), 0) as dutyMinuteSum'),
                DB::raw('COALESCE((SELECT MAX(duty_times_closed.end) FROM duty_times_closed WHERE duty_times_closed.user_id = users_closed.id), "-") as lastDutyDate')
            )
            ->groupBy('users_closed.id', 'users_closed.charactername', 'users_closed.rank_name', 'users_closed.salary', 'users_closed.salary_tooltip')
            ->orderBy('reportCount', 'DESC')
            ->get();
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
        return DB::table('inactivities')->join('users', 'users.id', '=', 'inactivities.user_id')->select('users.charactername', 'inactivities.begin', 'inactivities.end', 'inactivities.reason', 'inactivities.id', 'inactivities.status')->orderBy('inactivities.created_at', 'desc')->get();
    }

    /**
     * Get the registered users
     *
     * @return \Illuminate\Support\Collection
     */
    private function getRegisteredUsersQuery()
    {
        return DB::table('users')->select('users.id', 'users.charactername', 'users.username', 'users.created_at', 'users.adminLevel')->orderBy('users.charactername', 'ASC')->get();
    }

    /**
     * Get the admin logs
     *
     * @return \Illuminate\Support\Collection
     */
    private function getAdminLogsQuery()
    {
        return DB::table('admin_logs')->join('users', 'users.id', '=', 'admin_logs.user_id')->select('users.charactername', 'admin_logs.didWhat', 'admin_logs.created_at')->orderBy('admin_logs.created_at', 'desc')->get();
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
                'charactername' => ['required', 'string', 'max:255'],
            ],
            [
                'charactername.required' => 'Az IC név nem lehet üres.',
                'charactername.required' => 'Túl hosszú az IC név.',
            ],
        );

        try {
            $randomUsername = Str::random(8);
            $randomPassword = Str::random(8);
            $lowestRank = Rank::where('rank_order', 1)->first();

            $user = User::create([
                'charactername' => $request->charactername,
                'username' => $randomUsername,
                'password' => Hash::make($randomPassword),
                'rank_id' => $lowestRank ? $lowestRank->id : null,
            ]);

            $this->logAdminAction('Regisztrált egy új felhasználót ' . $request->charactername . ' IC néven (ID: ' . $user->id . ')');

            return Redirect::route('admin.index')->with('user-created', 'A felhasználó regisztrációja sikeres. FELHASZNÁLÓNÉV: ' . $randomUsername . ', JELSZÓ: ' . $randomPassword);
        } catch (\Throwable $th) {
            return Redirect::route('admin.index')->with('user-not-created', 'A felhasználó regisztrációja sikertelen.');
        }
    }

    public function viewUserReports(string $id)
    {
        $reports = $this->getUserReports($id);
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
        $reportsFromClosedWeek = $this->getUserReportsFromClosedWeek($id);
        $userCharacterName = $this->getCharacterNameById($id);

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
                $this->logAdminAction('Frissítette a(z) ' . $user->id . ' ID-val rendelkező felhasználó admin szintjét (' . $user->adminLevel . ' -> ' . $newAdminLevel . ')');
                $user->adminLevel = $newAdminLevel;
            }
        }

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
                'charactername' => ['string', 'max:255'],
            ],
            [
                'charactername.string' => 'Az IC név nem lehet üres.',
                'charactername.max' => 'Túl hosszú az IC név.',
            ],
        );

        if ($request->input('username') !== $user->username) {
            $oldusername = $user->username;
            $user->username = $request->input('username');

            $this->logAdminAction('Frissítette a(z) ' . $user->id . ' ID-val rendelkező felhasználó felhasználónevét (' . $oldusername . ' -> ' . $request->input('username') . ')');
        }

        if ($request->input('charactername') !== $user->charactername) {
            $oldcharactername = $user->charactername;
            $user->charactername = $request->input('charactername');

            $this->logAdminAction('Frissítette a(z) ' . $user->id . ' ID-val rendelkező felhasználó IC nevét (' . $oldcharactername . ' -> ' . $request->input('charactername') . ')');
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
            try {
                $user = User::findOrFail($id);
                $user->delete();

                $this->logAdminAction('Kitörölte a(z) ' . $user->charactername . ' (ID: ' . $user->id . ') felhasználót');

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

            $this->logAdminAction('Kitörölte a(z) ' . $characterName . ' (Jelentés ID: ' . $id . ') felhasználó jelentését');

            return Redirect::route('admin.viewUserReports', $userId)->with('successful-user-report-deletion', 'A felhasználó jelentésének törlése sikeres.');
        } catch (\Throwable $th) {
            return Redirect::route('admin.viewUserReports', $userId)->with('unsuccessful-user-report-deletion', 'A felhasználó jelentésének törlése sikertelen.');
        }
    }

    /**
     * Log the given admin action for the current user
     *
     * @param string $action
     */
    public function logAdminAction($action)
    {
        DB::table('admin_logs')->insert(['user_id' => Auth::user()->id, 'didWhat' => $action]);
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
        if (Auth::user()->adminLevel != 2) {
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
                $newRankName = $newRank ? $newRank->name : 'Nincs';

                $oldOrder = $user->rank ? $user->rank->rank_order : 0;
                $newOrder = $newRank ? $newRank->rank_order : 0;
                $rankChangeType = $newOrder < $oldOrder ? 'demotion' : 'promotion';

                $requiredWeeks = $user->rank ? (int) $user->rank->minimum_successful_weeks : 2;
                $carryOverWeeks = ($rankChangeType === 'promotion') ? max(0, (int) $user->successful_weeks - $requiredWeeks) : 0;

                $user->update([
                    'rank_id' => !empty($newRankId) ? (int)$newRankId : null,
                    'successful_weeks' => $carryOverWeeks,
                    'promoted_from_rank' => $oldRankName,
                    'promoted_to_rank' => $newRankName,
                    'rank_change_type' => $rankChangeType,
                ]);

                $this->logAdminAction('Módosította a(z) ' . $user->charactername . ' felhasználó rangját (' . $oldRankName . ' -> ' . $newRankName . ')');
            }
        }

        return Redirect::route('admin.index')->with('promotions-updated', 'A rangok sikeresen frissítve.');
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
            'successful_weeks' => $carryOverWeeks,
            'promoted_from_rank' => $oldRankName,
            'promoted_to_rank' => $newRankName,
            'rank_change_type' => 'promotion',
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
                    'closed_week_salary' => $stat->salary ?? 0,
                    'closed_week_bonus' => $stat->bonus_percentage ?? 0,
                    'closed_week_calculation' => $stat->salary_tooltip ?? '',
                    'closed_week_message' => $stat->closed_week_message ?? null,
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
}
