<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HKController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RanapController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\DashboardController;

Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest', 'no-cache');
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth', 'no-cache')->group(function () {
        Route::get('/', [DashboardController::class, 'showDashboard'])->name('dashboard');

        Route::get('/keuangan', [KeuanganController::class, 'showDashboardKeuangan'])->name('keuangan');

        Route::get('/ranap', [RanapController::class, 'showDashboardRanap'])->name('ranap');

        Route::get('/hk', [HKController::class, 'showDashboard'])->name('hk');
    });
});

// Route::get('/test-sqlserver', function () {
//     try {
//         DB::connection('sqlsrv')->getPdo();
//         echo "Connected successfully to the database ms sql server!";
//     } catch (\Exception $e) {
//         die("Could not connect to the database. Error: " . $e->getMessage());
//     }
// });
