<?php

use App\Http\Controllers\Api\TenantApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('tenants')->name('tenants.')->controller(TenantApiController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{tenant}', 'show')->name('show');
        Route::put('/{tenant}', 'update')->name('update');
        Route::delete('/{tenant}', 'destroy')->name('destroy');
    });
});
