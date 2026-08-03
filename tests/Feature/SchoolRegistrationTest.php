<?php

use App\Enums\RolesEnum;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a school by creating a user and linking user_id', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $response = $this->postJson('/api/schools', [
        'name' => 'ABC School',
        'email' => 'school@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'phone' => '+923001234567',
        'city' => 'Karachi',
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.name', 'ABC School')
        ->assertJsonPath('data.email', 'school@example.com')
        ->assertJsonPath('data.user.email', 'school@example.com');

    $school = School::query()->first();
    $user = User::query()->where('email', 'school@example.com')->first();

    expect($school)->not->toBeNull()
        ->and($user)->not->toBeNull()
        ->and($school->user_id)->toBe($user->id)
        ->and($user->hasRole(RolesEnum::SchoolAdmin))->toBeTrue();
});

it('allows the registered school user to login', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->postJson('/api/schools', [
        'name' => 'ABC School',
        'email' => 'school@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertCreated();

    $this->postJson('/api/auth/login', [
        'email' => 'school@example.com',
        'password' => 'password',
    ])
        ->assertSuccessful()
        ->assertJsonPath('user.email', 'school@example.com')
        ->assertJsonPath('user.roles', [RolesEnum::SchoolAdmin->value]);
});

it('requires password confirmation when registering a school', function () {
    $this->postJson('/api/schools', [
        'name' => 'ABC School',
        'email' => 'school@example.com',
        'password' => 'password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
