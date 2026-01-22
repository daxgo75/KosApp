<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Health check endpoints (public)
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
Route::get('/health/live', [HealthController::class, 'liveness'])->name('health.live');
Route::get('/health/ready', [HealthController::class, 'readiness'])->name('health.ready');
