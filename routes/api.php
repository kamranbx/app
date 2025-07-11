<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleApiAuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh-token', [AuthController::class, 'refreshToken']);

Route::middleware('jwt')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::put('/reports/{report}', [ReportController::class, 'update']);
    Route::patch('/reports/{report}', [ReportController::class, 'update']);
    Route::delete('/reports/{report}', [ReportController::class, 'delete']);
});

Route::middleware(['jwt', 'role:admin'])->group(function() {
    Route::post('/report-special', [ReportController::class, 'special']);
});

Route::middleware(['jwt', 'role:manager,user'])->group(function() {
    Route::post('/report-normal', [ReportController::class, 'normal']);
});

Route::middleware(['jwt', 'role-perm:admin'])->group(function() {
    Route::post('/test', [ReportController::class, 'test']);
});

Route::get('google/callback', [GoogleApiAuthController::class, 'handle'])->name('auth.google.callback');

//Route::get('/redis-check', function () {
//    Cache::put('key', 'value', now()->addSeconds(10));
//    return Cache::get('key');
//});
