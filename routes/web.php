<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DutyTimeController;


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
    Route::put('/profil', [PasswordController::class, 'update'])->name('password.update');
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
});

require __DIR__.'/auth.php';
