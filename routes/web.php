<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstimasiController;
use App\Http\Controllers\RiwayatController;
use App\Http\Middleware\EnsureSessionToken;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Configure rate limiter for estimation endpoint
RateLimiter::for('estimasi', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});

Route::middleware([EnsureSessionToken::class])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Estimasi
    Route::get('/estimasi', [EstimasiController::class, 'create'])->name('estimasi.form');
    Route::post('/estimasi', [EstimasiController::class, 'store'])
        ->name('estimasi.store')
        ->middleware('throttle:estimasi');
    Route::get('/estimasi/{id}/hasil', [EstimasiController::class, 'hasil'])->name('estimasi.hasil');

    // Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');
});
