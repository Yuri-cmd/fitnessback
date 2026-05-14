<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GoalController;
use App\Http\Controllers\Web\RoutineController;
use App\Http\Controllers\Web\StatsController;
use App\Http\Controllers\Web\WeightController;
use App\Http\Controllers\Web\WikiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('/weight', [WeightController::class, 'index'])->name('weight.index');
    Route::post('/weight', [WeightController::class, 'store'])->name('weight.store');

    Route::get('/routines', [RoutineController::class, 'index'])->name('routines.index');
    Route::get('/routines/create', [RoutineController::class, 'create'])->name('routines.create');
    Route::get('/routines/pdf', [RoutineController::class, 'pdf'])->name('routines.pdf');
    Route::get('/routines/import', [RoutineController::class, 'importForm'])->name('routines.import');
    Route::post('/routines/import', [RoutineController::class, 'importStore'])->name('routines.import.store');
    Route::post('/routines', [RoutineController::class, 'store'])->name('routines.store');
    Route::get('/routines/{routine}/edit', [RoutineController::class, 'edit'])->name('routines.edit');
    Route::put('/routines/{routine}', [RoutineController::class, 'update'])->name('routines.update');
    Route::delete('/routines/{routine}', [RoutineController::class, 'destroy'])->name('routines.destroy');
    Route::post('/routines/{routine}/complete', [RoutineController::class, 'complete'])->name('routines.complete');
    Route::get('/routines/{routine}/train', [RoutineController::class, 'train'])->name('routines.train');

    Route::get('/strength', [RoutineController::class, 'strength'])->name('strength.index');

    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');

    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
    Route::get('/wiki', [WikiController::class, 'index'])->name('wiki.index');
    Route::get('/wiki/{exercise}', [WikiController::class, 'show'])->name('wiki.show');

    Route::post('/water', [DashboardController::class, 'logWater'])->name('water.store');

    Route::get('/exercises-json', [RoutineController::class, 'exercises'])->name('exercises.json');
});
