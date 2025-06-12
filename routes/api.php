<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('refresh-token', [\App\Http\Controllers\AuthController::class, 'refreshToken']);

Route::middleware('jwt')->group(function () {
    Route::get('user', [\App\Http\Controllers\AuthController::class, 'user']);
});

//Route::get('/redis-check', function () {
//    Cache::put('key', 'value', now()->addSeconds(10));
//    return Cache::get('key');
//});
