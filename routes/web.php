<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DutyTimeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InactivityController;


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
    if(Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/fooldal', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil', [PasswordController::class, 'update'])->name('profile.updatePassword');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::get('/jelentesek', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/uj-jelentes', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/uj-jelentes', [ReportController::class, 'store'])->name('reports.store');
    Route::delete('/jelentes-torles/{id}', [ReportController::class, 'destroy'])->name('reports.delete');

    // Duty
    Route::get('/szolgalatok', [DutyTimeController::class, 'index'])->name('duty_time.index');
    Route::get('/uj-szolgalat', [DutyTimeController::class, 'create'])->name('duty_time.create');
    Route::post('/uj-szolgalat', [DutyTimeController::class, 'store'])->name('duty_time.store');
    Route::delete('/szolgalat-torles/{id}', [DutyTimeController::class, 'destroy'])->name('duty_time.delete');

    // Inactivity
    Route::get('/inaktivitas', [InactivityController::class, 'index'])->name('inactivity.index');
    Route::get('/uj-inaktivitas', [InactivityController::class, 'create'])->name('inactivity.create');
    Route::post('/uj-inaktivitas', [InactivityController::class, 'store'])->name('inactivity.store');
    Route::delete('/inaktivitas-torles/{id}', [InactivityController::class, 'destroy'])->name('inactivity.delete');

    // Admin
    Route::middleware('isAdmin')->group(function () {
        Route::prefix('admin')->group(function () {
            // Ajax routes
            Route::get('/weekly-stats', [AdminController::class, 'getWeeklyStats'])->name('admin.weeklyStats');

            
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

            Route::post('/inaktivitas-elfogadas/{id}', [AdminController::class, 'acceptInactivity'])->name('admin.acceptInactivity');
            Route::post('/inaktivitas-elutasitas/{id}', [AdminController::class, 'denyInactivity'])->name('admin.denyInactivity');
            Route::delete('/admin-inaktivitas-torles/{id}', [AdminController::class, 'destroyInactivity'])->name('admin.destroyInactivity');
        });
    });
});


require __DIR__.'/auth.php';
