<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is working.',
    ], 200);
});

Route::prefix('v1')->group(function () {

    // ========================================
    // [ Auth ]
    // ========================================
    Route::prefix('auth')->group(function () {

        // Guest-only auth routes
        Route::middleware('guest:sanctum')->group(function () {
            Route::post('/register', RegisterController::class)
                ->name('api.v1.auth.register');

            Route::post('/login', LoginController::class)
                ->name('api.v1.auth.login');

            Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])
                ->name('api.v1.auth.password.forgot');

            Route::post('/reset-password', [PasswordController::class, 'resetPassword'])
                ->name('api.v1.auth.password.reset');
        });

        // Authenticated routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', LogoutController::class)
                ->name('api.v1.auth.logout');

            Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
                ->middleware('throttle:6,1')
                ->name('api.v1.auth.verification.send');

            Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
                ->middleware('signed')
                ->name('verification.verify');
        });
    });

    // ========================================
    // [ Role Permission ]
    // ========================================

    // T O D O

    // ========================================
    // [ School ]
    // ========================================

});
