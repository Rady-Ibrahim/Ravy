<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthController;
use Modules\Auth\Http\Controllers\Api\SocialAuthController;

Route::middleware('api')->prefix('api/v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:6,1');

        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:6,1');

        Route::post('/verify', [AuthController::class, 'verify'])
            ->middleware('throttle:10,1');

        Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode'])
            ->middleware('throttle:6,1');

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->middleware('throttle:6,1');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->middleware('throttle:6,1');

        Route::post('/social/{provider}', [SocialAuthController::class, 'login'])
            ->middleware('throttle:6,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::patch('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
});
