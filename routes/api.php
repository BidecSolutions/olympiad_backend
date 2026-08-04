<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\CompetitionCategoryController;
use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OfficialController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SchoolDocumentController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is working.',
    ], 200);
});

// ========================================
// [ Auth ]
// ========================================
Route::prefix('auth')->group(function () {

    // Guest-only auth routes
    Route::middleware('guest:sanctum')->group(function () {
        Route::post('/register', RegisterController::class)
            ->name('api.auth.register');

        Route::post('/login', LoginController::class)
            ->name('api.auth.login');

        Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])
            ->name('api.auth.password.forgot');

        Route::post('/reset-password', [PasswordController::class, 'resetPassword'])
            ->name('api.auth.password.reset');
    });

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', LogoutController::class)
            ->name('api.auth.logout');

        Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('api.auth.verification.send');

        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');
    });
});

// ========================================
// [ Role Permission ]  //Seed
// ========================================

// ========================================
// [ Event ]
// ========================================
Route::middleware('auth:sanctum')
    ->prefix('events')
    ->controller(EventController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->name('api.events.index');

        Route::post('/', 'store')
            ->name('api.events.store');

        Route::get('/{event}', 'show')
            ->name('api.events.show');

        Route::patch('/{event}', 'update')
            ->name('api.events.patch');

        Route::delete('/{event}', 'destroy')
            ->name('api.events.destroy');
    });

// ========================================
// [ Official ]
// ========================================
Route::middleware('auth:sanctum')
    ->prefix('officials')
    ->controller(OfficialController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->name('api.officials.index');

        Route::post('/', 'store')
            ->name('api.officials.store');

        Route::get('/{official}', 'show')
            ->name('api.officials.show');

        Route::patch('/{official}', 'update')
            ->name('api.officials.patch');

        Route::delete('/{official}', 'destroy')
            ->name('api.officials.destroy');
    });

// ========================================
// [ Competition ]
// ========================================
Route::middleware('auth:sanctum')
    ->prefix('competitions')
    ->controller(CompetitionController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->name('api.competitions.index');

        Route::post('/', 'store')
            ->name('api.competitions.store');

        Route::get('/{competition}', 'show')
            ->name('api.competitions.show');

        Route::patch('/{competition}', 'update')
            ->name('api.competitions.patch');

        Route::delete('/{competition}', 'destroy')
            ->name('api.competitions.destroy');

        Route::prefix('{competition}/competition-categories')
            ->controller(CompetitionCategoryController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->name('api.competitions.competition-categories.index');

                Route::post('/', 'store')
                    ->name('api.competitions.competition-categories.store');

                Route::get('/{competitionCategory}', 'show')
                    ->name('api.competitions.competition-categories.show');

                Route::patch('/{competitionCategory}', 'update')
                    ->name('api.competitions.competition-categories.patch');

                Route::delete('/{competitionCategory}', 'destroy')
                    ->name('api.competitions.competition-categories.destroy');
            });
    });

// ========================================
// [ School ]
// ========================================
Route::post('/schools', [SchoolController::class, 'store'])
    ->middleware('guest:sanctum')
    ->name('api.schools.store');

Route::middleware('auth:sanctum')
    ->prefix('schools')
    ->controller(SchoolController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->name('api.schools.index');

        Route::get('/{school}', 'show')
            ->name('api.schools.show');

        Route::patch('/{school}', 'update')
            ->name('api.schools.patch');

        Route::delete('/{school}', 'destroy')
            ->name('api.schools.destroy');

        Route::prefix('{school}/documents')
            ->controller(SchoolDocumentController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->name('api.schools.documents.index');

                Route::post('/', 'store')
                    ->name('api.schools.documents.store');

                Route::get('/{document}', 'show')
                    ->name('api.schools.documents.show');

                Route::patch('/{document}', 'update')
                    ->name('api.schools.documents.patch');

                Route::delete('/{document}', 'destroy')
                    ->name('api.schools.documents.destroy');
            });

        Route::prefix('{school}/students')
            ->controller(StudentController::class)
            ->group(function () {

                Route::get('/', 'index')
                    ->name('api.schools.students.index');

                Route::post('/', 'store')
                    ->name('api.schools.students.store');

                Route::get('/{student}', 'show')
                    ->name('api.schools.students.show');

                Route::patch('/{student}', 'update')
                    ->name('api.schools.students.patch');

                Route::delete('/{student}', 'destroy')
                    ->name('api.schools.students.destroy');
            });

        Route::prefix('{school}/teams')
            ->group(function () {

                Route::controller(TeamController::class)
                    ->group(function () {

                        Route::get('/', 'index')
                            ->name('api.schools.teams.index');

                        Route::post('/', 'store')
                            ->name('api.schools.teams.store');

                        Route::get('/{team}', 'show')
                            ->name('api.schools.teams.show');

                        Route::patch('/{team}', 'update')
                            ->name('api.schools.teams.patch');

                        Route::delete('/{team}', 'destroy')
                            ->name('api.schools.teams.destroy');
                    });

                Route::prefix('{team}/members')
                    ->controller(TeamMemberController::class)
                    ->group(function () {

                        Route::get('/', 'index')
                            ->name('api.schools.teams.members.index');

                        Route::post('/', 'store')
                            ->name('api.schools.teams.members.store');

                        Route::get('/{teamMember}', 'show')
                            ->name('api.schools.teams.members.show');

                        Route::patch('/{teamMember}', 'update')
                            ->name('api.schools.teams.members.patch');

                        Route::delete('/{teamMember}', 'destroy')
                            ->name('api.schools.teams.members.destroy');
                    });
            });
    });
