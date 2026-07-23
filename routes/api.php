<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\CompetitionCategoryController;
use App\Http\Controllers\Api\V1\CompetitionCategoryParticipationController;
use App\Http\Controllers\Api\V1\CompetitionController;
use App\Http\Controllers\Api\V1\CompetitionTypeController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\OfficialController;
use App\Http\Controllers\Api\V1\ParticipationTypeController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\TeamMemberController;
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
    // [ Official ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('officials')
        ->controller(OfficialController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.officials.index');

            Route::post('/', 'store')
                ->name('api.v1.officials.store');

            Route::get('/{official}', 'show')
                ->name('api.v1.officials.show');

            Route::patch('/{official}', 'update')
                ->name('api.v1.officials.patch');

            Route::delete('/{official}', 'destroy')
                ->name('api.v1.officials.destroy');
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
    // [ Competition ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('competitions')
        ->controller(CompetitionController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.competitions.index');

            Route::post('/', 'store')
                ->name('api.v1.competitions.store');

            Route::get('/{competition}', 'show')
                ->name('api.v1.competitions.show');

            Route::patch('/{competition}', 'update')
                ->name('api.v1.competitions.patch');

            Route::delete('/{competition}', 'destroy')
                ->name('api.v1.competitions.destroy');

            Route::prefix('{competition}/competition-categories')
                ->controller(CompetitionCategoryController::class)
                ->group(function () {

                    Route::get('/', 'index')
                        ->name('api.v1.competitions.competition-categories.index');

                    Route::post('/', 'store')
                        ->name('api.v1.competitions.competition-categories.store');

                    Route::get('/{competitionCategory}', 'show')
                        ->name('api.v1.competitions.competition-categories.show');

                    Route::patch('/{competitionCategory}', 'update')
                        ->name('api.v1.competitions.competition-categories.patch');

                    Route::delete('/{competitionCategory}', 'destroy')
                        ->name('api.v1.competitions.competition-categories.destroy');
                });
        });

    // ========================================
    // [ Participation Type ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('participation-types')
        ->controller(ParticipationTypeController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.participation-types.index');

            Route::post('/', 'store')
                ->name('api.v1.participation-types.store');

            Route::get('/{participationType}', 'show')
                ->name('api.v1.participation-types.show');

            Route::patch('/{participationType}', 'update')
                ->name('api.v1.participation-types.patch');

            Route::delete('/{participationType}', 'destroy')
                ->name('api.v1.participation-types.destroy');
        });

    // ========================================
    // [ Competition Category Participation ]
    // ========================================
    Route::middleware('auth:sanctum')
        ->prefix('competition-categories/{competitionCategory}/participations')
        ->controller(CompetitionCategoryParticipationController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('api.v1.competition-categories.participations.index');

            Route::post('/', 'store')
                ->name('api.v1.competition-categories.participations.store');

            Route::get('/{participation}', 'show')
                ->name('api.v1.competition-categories.participations.show');

            Route::patch('/{participation}', 'update')
                ->name('api.v1.competition-categories.participations.patch');

            Route::delete('/{participation}', 'destroy')
                ->name('api.v1.competition-categories.participations.destroy');
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

            Route::prefix('{school}/teams')
                ->group(function () {

                    Route::controller(TeamController::class)
                        ->group(function () {

                            Route::get('/', 'index')
                                ->name('api.v1.schools.teams.index');

                            Route::post('/', 'store')
                                ->name('api.v1.schools.teams.store');

                            Route::get('/{team}', 'show')
                                ->name('api.v1.schools.teams.show');

                            Route::patch('/{team}', 'update')
                                ->name('api.v1.schools.teams.patch');

                            Route::delete('/{team}', 'destroy')
                                ->name('api.v1.schools.teams.destroy');
                        });

                    Route::prefix('{team}/members')
                        ->controller(TeamMemberController::class)
                        ->group(function () {

                            Route::get('/', 'index')
                                ->name('api.v1.schools.teams.members.index');

                            Route::post('/', 'store')
                                ->name('api.v1.schools.teams.members.store');

                            Route::get('/{teamMember}', 'show')
                                ->name('api.v1.schools.teams.members.show');

                            Route::patch('/{teamMember}', 'update')
                                ->name('api.v1.schools.teams.members.patch');

                            Route::delete('/{teamMember}', 'destroy')
                                ->name('api.v1.schools.teams.members.destroy');
                        });
                });

            Route::prefix('{school}/registrations')
                ->controller(RegistrationController::class)
                ->group(function () {

                    Route::get('/', 'index')
                        ->name('api.v1.schools.registrations.index');

                    Route::post('/', 'store')
                        ->name('api.v1.schools.registrations.store');

                    Route::get('/{registration}', 'show')
                        ->name('api.v1.schools.registrations.show');

                    Route::patch('/{registration}', 'update')
                        ->name('api.v1.schools.registrations.patch');

                    Route::delete('/{registration}', 'destroy')
                        ->name('api.v1.schools.registrations.destroy');
                });
        });
});
