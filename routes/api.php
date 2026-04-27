<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->as('api.v1.')
    ->group(function () {
        Route::get('/health', fn () => response()->json(['ok' => true]))->name('health');

        Route::prefix('auth')->as('auth.')->group(function () {
            Route::post('register', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'register'])->name('register');
            Route::post('login', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'login'])->name('login');
            Route::post('forgot-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('reset-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'resetPassword'])->name('reset-password');

            Route::middleware('auth:customer')->group(function () {
                Route::post('logout', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'logout'])->name('logout');
                Route::get('me', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'me'])->name('me');
                Route::put('profile', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'updateProfile'])->name('profile');
                Route::put('change-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'changePassword'])->name('change-password');
            });
        });
    });

