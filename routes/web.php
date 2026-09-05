<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DutyTimeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InactivityController;
use App\Http\Controllers\SettingController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    if (User::count() === 0) {
        return redirect()->route('register');
    }
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/fooldal', [DashboardController::class, 'index'])->name('dashboard');

    // Ajax
    Route::get('/dashboard', [DashboardController::class, 'getDashboardTable'])->name('dashboardTable');

    // Profile
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil', [PasswordController::class, 'update'])->name('profile.updatePassword');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/jelentesek', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/uj-jelentes', [ReportController::class, 'createReportView'])->name('reports.createReportView');
    Route::post('/uj-jelentes', [ReportController::class, 'storeNewReport'])->name('reports.storeNewReport');
    Route::get('/jelentes-frissites/{id}', [ReportController::class, 'editReportView'])->name('reports.editReportView');
    Route::put('/jelentes-frissites/{id}', [ReportController::class, 'updateReport'])->name('reports.updateReport');
    Route::delete('/jelentes-torles/{id}', [ReportController::class, 'deleteReport'])->name('reports.deleteReport');

    // Duty
    Route::get('/szolgalatok', [DutyTimeController::class, 'index'])->name('duty_time.index');
    Route::get('/uj-szolgalat', [DutyTimeController::class, 'createDutyView'])->name('duty_time.createDutyView');
    Route::post('/uj-szolgalat', [DutyTimeController::class, 'storeNewDuty'])->name('duty_time.storeNewDuty');
    Route::get('/szolgalat-frissites/{id}', [DutyTimeController::class, 'editDutyView'])->name('duty_time.editDutyView');
    Route::put('/szolgalat-frissites/{id}', [DutyTimeController::class, 'updateDuty'])->name('duty_time.updateDuty');
    Route::delete('/szolgalat-torles/{id}', [DutyTimeController::class, 'deleteDuty'])->name('duty_time.deleteDuty');

    // Inactivity
    Route::get('/inaktivitas', [InactivityController::class, 'index'])->name('inactivity.index');
    Route::get('/uj-inaktivitas', [InactivityController::class, 'createInactivityView'])->name('inactivity.createInactivityView');
    Route::post('/uj-inaktivitas', [InactivityController::class, 'storeNewInactivity'])->name('inactivity.storeNewInactivity');
    Route::delete('/inaktivitas-torles/{id}', [InactivityController::class, 'deleteInactivity'])->name('inactivity.deleteInactivity');

    // Admin
    Route::middleware('isAdmin')->group(function () {
        Route::prefix('admin')->group(function () {
            // Ajax routes
            // Route::get('/weekly-stats', [AdminController::class, 'getWeeklyStatsTable'])->name('admin.weeklyStats');
            // Route::get('/closed-week-stats', [AdminController::class, 'getClosedWeekStatsTable'])->name('admin.closedWeekStats');
            // Route::get('/inactivities', [AdminController::class, 'getInactivitiesTable'])->name('admin.inactivities');
            // Route::get('/registrated-users', [AdminController::class, 'getRegistratedUsersTable'])->name('admin.registratedUsers');
            // Route::get('/admin-logs', [AdminController::class, 'getAdminLogsTable'])->name('admin.adminLogs');

            Route::get('/', [AdminController::class, 'index'])->name('admin.index');

            Route::get('/felhasznalo-frissites/{id}', [AdminController::class, 'editUser'])->name('admin.editUser');
            Route::put('/felhasznalo-frissites/{id}', [AdminController::class, 'updateUser'])->name('admin.updateUser');
            Route::put('/felhasznalo-jelszo-frissites/{id}', [PasswordController::class, 'updateUserPassword'])->name('admin.updateUserPassword');

            Route::get('/jelentesek/{id}', [AdminController::class, 'viewUserReports'])->name('admin.viewUserReports');
            Route::delete('/jelentes-torles/{id}', [AdminController::class, 'deleteReport'])->name('admin.deleteReport');
            Route::get('/szolgalatok/{id}', [AdminController::class, 'viewUserDuty'])->name('admin.viewUserDuty');
            Route::delete('/szolgalat-torles/{id}', [AdminController::class, 'deleteDutyTime'])->name('admin.deleteDutyTime');

            Route::get('/lezart-jelentesek/{id}', [AdminController::class, 'viewClosedUserReports'])->name('admin.viewClosedUserReports');
            Route::get('/lezart-szolgalatok/{id}', [AdminController::class, 'viewClosedUserDuty'])->name('admin.viewClosedUserDuty');

            Route::get('/regisztracio', [AdminController::class, 'userRegistrationPage'])->name('admin.userRegistrationPage');
            Route::post('/regisztracio', [AdminController::class, 'registerUser'])->name('admin.registerUser');
            Route::delete('/felhasznalo-torles/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

            Route::post('/het-lezaras', [AdminController::class, 'closeWeek'])->name('admin.closeWeek');

            Route::post('/inaktivitas-elfogadas/{id}', [InactivityController::class, 'acceptInactivity'])->name('admin.acceptInactivity');
            Route::post('/inaktivitas-elutasitas/{id}', [InactivityController::class, 'declineInactivity'])->name('admin.declineInactivity');
            Route::delete('/admin-inaktivitas-torles/{id}', [InactivityController::class, 'deleteInactivityAsAdmin'])->name('admin.deleteInactivityAsAdmin');

            Route::post('/beallitasok-frissites', [SettingController::class, 'update'])->name('admin.updateSettings');
            Route::post('/eloleptetesek-mentes', [AdminController::class, 'updateUserRanks'])->name('admin.updateUserRanks');
            Route::post('/felhasznalo-eloleptetes/{id}', [AdminController::class, 'promoteUser'])->name('admin.promoteUser');
        });
    });
});

require __DIR__ . '/auth.php';
