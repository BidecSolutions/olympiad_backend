<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Guest-only auth routes
    Route::middleware('guest:sanctum')->group(function () {
        Route::post('/auth/register', RegisterController::class)->name('api.v1.auth.register');
        Route::post('/auth/login', LoginController::class)->name('api.v1.auth.login');

        // Password reset
        Route::post('/auth/forgot-password', [PasswordController::class, 'forgotPassword'])->name('api.v1.auth.password.forgot');
        Route::post('/auth/reset-password', [PasswordController::class, 'resetPassword'])->name('api.v1.auth.password.reset');
    });

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', LogoutController::class)->name('api.v1.auth.logout');

        // Email verification
        Route::post('/auth/email/verification-notification', [VerifyEmailController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('api.v1.auth.verification.send');

        Route::get('/auth/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');
    });

});
