<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\CompetitionTypeController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\StudentController;
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
    // [ Event ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('events')
        ->controller(EventController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.events.index');

            Route::post('/', 'store')
                ->name('api.v1.events.store');

            Route::get('/{event}', 'show')
                ->name('api.v1.events.show');

            Route::patch('/{event}', 'update')
                ->name('api.v1.events.patch');

            Route::delete('/{event}', 'destroy')
                ->name('api.v1.events.destroy');
        });

    // ========================================
    // [ Competition Type ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('competition-types')
        ->controller(CompetitionTypeController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.competition-types.index');

            Route::post('/', 'store')
                ->name('api.v1.competition-types.store');

            Route::get('/{competitionType}', 'show')
                ->name('api.v1.competition-types.show');

            Route::patch('/{competitionType}', 'update')
                ->name('api.v1.competition-types.patch');

            Route::delete('/{competitionType}', 'destroy')
                ->name('api.v1.competition-types.destroy');
        });

    // ========================================
    // [ School ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('schools')
        ->controller(SchoolController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.schools.index');

            Route::post('/', 'store')
                ->name('api.v1.schools.store');

            Route::get('/{school}', 'show')
                ->name('api.v1.schools.show');

            Route::patch('/{school}', 'update')
                ->name('api.v1.schools.patch');

            Route::delete('/{school}', 'destroy')
                ->name('api.v1.schools.destroy');

            Route::prefix('{school}/students')
                ->controller(StudentController::class)
                ->group(function () {

                    Route::get('/', 'index')
                        ->name('api.v1.schools.students.index');

                    Route::post('/', 'store')
                        ->name('api.v1.schools.students.store');

                    Route::get('/{student}', 'show')
                        ->name('api.v1.schools.students.show');

                    Route::patch('/{student}', 'update')
                        ->name('api.v1.schools.students.patch');

                    Route::delete('/{student}', 'destroy')
                        ->name('api.v1.schools.students.destroy');
                });
        });
});
