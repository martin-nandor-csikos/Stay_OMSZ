<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = DB::table('users')
            ->select('users.id', 'users.charactername', 'users.username', 'users.created_at', 'users.isAdmin')
            ->get();

        $userStats = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select(
                'users.id',
                'users.charactername',
                DB::raw('count(reports.user_id) as reportCount'),
                DB::raw('(SELECT MAX(reports.created_at) FROM reports WHERE reports.user_id = users.id) as lastReportDate'),
                DB::raw('(SELECT SUM(duty_times.minutes) FROM duty_times WHERE duty_times.user_id = users.id) as dutyMinuteSum'),
                DB::raw('(SELECT MAX(duty_times.end) FROM duty_times WHERE duty_times.user_id = users.id) as lastDutyDate'),
            )
            ->groupBy('users.id', 'users.charactername')
            ->get();

        return view('admin.view_admin', [
            'users' => $users,
            'userStats' => $userStats,
        ]);
    }

    public function userRegistrationPage()
    {
        return view('admin.register_user');
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'charactername' => ['required', 'string', 'max:255'],
        ], [
            'charactername.required' => 'Az IC név nem lehet üres.',
            'charactername.required' => 'Túl hosszú az IC név.',
        ]);

        try {
            $randomUsername = Str::random(8);
            $randomPassword = Str::random(8);

            //dd($request->charactername);
            //dd($randomUsername);
            //dd($randomPassword);

            $user = User::create([
                'charactername' => $request->charactername,
                'username' => $randomUsername,
                'password' => Hash::make($randomPassword),
            ]);
            return Redirect::route('admin.index')->with('user-created', 'A felhasználó regisztrációja sikeres. FELHASZNÁLÓNÉV: ' . $randomUsername . ', JELSZÓ: ' . $randomPassword);
        } catch (\Throwable $th) {
            return Redirect::route('admin.index')->with('user-not-created', 'A felhasználó regisztrációja sikertelen');
        }
    }
    
    public function viewUserReports (string $id)
    {
        $reports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('reports.id', 'reports.price', 'reports.diagnosis', 'reports.withWho', 'reports.img', 'reports.created_at', 'users.charactername')
            ->where('user_id', '=', $id)
            ->get();

        return view('admin.view_user_reports', [
            'reports' => $reports,
            'charactername' => $reports[0]->charactername,
        ]);
    }

    public function viewUserDuty (string $id)
    {
        $dutyTimes = DB::table('duty_times')
            ->join('users', 'users.id', '=', 'duty_times.user_id')
            ->select('duty_times.id', 'duty_times.begin', 'duty_times.end', 'duty_times.minutes', 'users.charactername')
            ->where('user_id', '=', $id)
            ->get();

        return view('admin.view_user_duty', [
            'dutyTimes' => $dutyTimes,
            'charactername' => $dutyTimes[0]->charactername,
        ]);
    }

    public function editUser(string $id)
    {
        $user = User::findOrFail($id);

        return view('admin.update_user', [
            'user' => $user,
        ]);
    }

    public function updateUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $usernameCheck = ($request->input('username') !== $user->username);

        if ($request->filled('password') && $request->filled('password_confirmation')) {
            try {
                $hashedPassword = $this->updatePassword($request);
                $user->password = $hashedPassword;
            } catch (\Exception $e) {
                return Redirect::back()->withErrors(['password' => $e->getMessage()]);
            }
        }

        if (Auth::user()->canGiveAdmin == 1) {
            if (Auth::user()->username != $user->username) {
                if ($request->has('admin')) {
                    $user->isAdmin = 1;
                } else {
                    $user->isAdmin = 0;
                }
            }
        }

        // Check if username was changed, if not, then don't validate for unique
        if ($usernameCheck) {
            $validatedData = $request->validate([
                'username' => ['string', 'max:255', 'unique:users'],
            ], [
                'username.string' => 'A felhasználónév nem lehet üres.',
                'username.unique' => 'Ez a felhasználónév már foglalt.',
                'username.max' => 'Túl hosszú a felhasználónév.',
            ]);
        } else {
            $validatedData = $request->validate([
                'username' => ['string', 'max:255'],
            ], [
                'username.string' => 'A felhasználónév nem lehet üres.',
                'username.max' => 'Túl hosszú a felhasználónév.',
            ]);
        }

        $validatedData = $request->validate([
            'charactername' => ['string', 'max:255'],
        ], [
            'charactername.string' => 'Az IC név nem lehet üres.',
            'charactername.max' => 'Túl hosszú az IC név.',
        ]);

        $user->username = $request->input('username');
        $user->charactername = $request->input('charactername');

        try {
            $user->save();

            return Redirect::route('admin.index')->with('user-updated', 'A felhasználó frissítése sikeres.');
        } catch (\Throwable $th) {
            return Redirect::route('admin.index')->with('user-not-updated', 'A felhasználó frissítése sikertelen.');
        }
    }

    private function updatePassword(Request $request) {
        $validated = $request->validateWithBag('updatePassword', [
            'password' => ['min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'A megadott 2 jelszó nem egyezik meg.',
            'password.required' => 'Meg kell adnod egy új jelszót.',
            'password.min' => 'Túl rövid a jelszó. Minimum 8 karakterből kell állnia.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Hash::make($validated['password']);
    }

    public function deleteUser(string $id)
    {
        if (Auth::user()->id != $id) {
            try {
                $user = User::findOrFail($id);
                $user->delete();

                return to_route('admin.index')->with('successful-user-deletion', 'A felhasználó törlése sikeres.');
            } catch (\Throwable $th) {
                return to_route('admin.index')->with('unsuccessful-user-deletion', 'A felhasználó törlése sikertelen.');
            }
        }
    }
}
