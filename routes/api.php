<?php

use App\Http\Controllers\PromocodeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::middleware('auth:api')->group(function () {
    Route::prefix('user')->group(function() {
        Route::get('info', [UserController::class, 'info'])->name('user.info');
        Route::get('balance', [UserController::class, 'balance'])->name('user.balance');

    });

    Route::resource('promocodes', PromocodeController::class)->only(['index', 'store']);
    Route::post('promocodes/use', [PromocodeController::class, 'use'])->name('promocodes.use');
});

Route::prefix('user')->group(function() {
    Route::post('register', [AuthController::class, 'register'])->name('user.register');
    Route::post('login', [AuthController::class, 'login'])->name('user.login');
    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout')->middleware('auth:api');
});
