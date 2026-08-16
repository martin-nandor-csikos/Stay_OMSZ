<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\InactivityStatus;
use App\Models\User;
use App\Models\Report;
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
        return DB::table('users')->leftJoin('reports', 'users.id', '=', 'reports.user_id')->select('users.id', 'users.charactername', DB::raw('COALESCE(count(reports.user_id), 0) as reportCount'), DB::raw('COALESCE((SELECT MAX(reports.created_at) FROM reports WHERE reports.user_id = users.id), "-") as lastReportDate'), DB::raw('COALESCE((SELECT SUM(duty_times.minutes) FROM duty_times WHERE duty_times.user_id = users.id), 0) as dutyMinuteSum'), DB::raw('COALESCE((SELECT MAX(duty_times.end) FROM duty_times WHERE duty_times.user_id = users.id), "-") as lastDutyDate'))->groupBy('users.id', 'users.charactername')->orderBy('reportCount', 'DESC')->get();
    }

    /**
     * Get the closed week statistics for each user.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getClosedWeekStatsQuery()
    {
        return DB::table('users_closed')->leftJoin('reports_closed', 'users_closed.id', '=', 'reports_closed.user_id')->select('users_closed.id', 'users_closed.charactername', DB::raw('COALESCE(count(reports_closed.user_id), 0) as reportCount'), DB::raw('COALESCE((SELECT MAX(reports_closed.created_at) FROM reports_closed WHERE reports_closed.user_id = users_closed.id), "-") as lastReportDate'), DB::raw('COALESCE((SELECT SUM(duty_times_closed.minutes) FROM duty_times_closed WHERE duty_times_closed.user_id = users_closed.id), 0) as dutyMinuteSum'), DB::raw('COALESCE((SELECT MAX(duty_times_closed.end) FROM duty_times_closed WHERE duty_times_closed.user_id = users_closed.id), "-") as lastDutyDate'))->groupBy('users_closed.id', 'users_closed.charactername')->orderBy('reportCount', 'DESC')->get();
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

            $user = User::create([
                'charactername' => $request->charactername,
                'username' => $randomUsername,
                'password' => Hash::make($randomPassword),
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

    /**
     * Move the weekly statistics to closed tables and reset the current tables.
     *
     * @return bool
     */
    private function moveWeeklyStatsToClosed()
    {
        try {
            DB::delete('DELETE FROM reports_closed');
            DB::delete('DELETE FROM duty_times_closed');
            DB::delete('DELETE FROM users_closed');

            DB::insert('INSERT INTO users_closed SELECT id, charactername FROM users');
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
            DB::rollBack();

            DB::table('locks')
                ->where('name', 'close_week')
                ->update(['isLocked' => 0]);

            return false;
        }
    }
}
