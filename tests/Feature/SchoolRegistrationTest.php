<?php

use App\Enums\RolesEnum;
use App\Enums\SchoolStatusEnum;
use App\Models\School;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a school publicly with pending status', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $response = $this->postJson('/api/schools/register', [
        'name' => 'ABC School',
        'contact_name' => 'John Principal',
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
        ->assertJsonPath('data.status', SchoolStatusEnum::Pending->value)
        ->assertJsonPath('data.user.email', 'school@example.com');

    $school = School::query()->first();
    $user = User::query()->where('email', 'school@example.com')->first();

    expect($school)->not->toBeNull()
        ->and($user)->not->toBeNull()
        ->and($school->user_id)->toBe($user->id)
        ->and($user->hasRole(RolesEnum::SchoolAdmin))->toBeTrue();
});

it('returns pending school status on login before approval', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->postJson('/api/schools/register', [
        'name' => 'ABC School',
        'contact_name' => 'John Principal',
        'email' => 'school@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertCreated();

    $this->postJson('/api/auth/login', [
        'login' => '1',
        'password' => 'password',
    ])
        ->assertSuccessful()
        ->assertJsonPath('login_state', 'school_pending')
        ->assertJsonPath('data.status', SchoolStatusEnum::Pending->value)
        ->assertJsonMissing(['token']);
});

it('allows the approved school user to login', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->postJson('/api/schools/register', [
        'name' => 'ABC School',
        'contact_name' => 'John Principal',
        'email' => 'school@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertCreated();

    $school = School::query()->first();
    $school->update(['status' => SchoolStatusEnum::Approved]);

    $this->postJson('/api/auth/login', [
        'login' => (string) $school->id,
        'password' => 'password',
    ])
        ->assertSuccessful()
        ->assertJsonPath('login_state', 'school_approved')
        ->assertJsonPath('data.status', SchoolStatusEnum::Approved->value)
        ->assertJsonPath('user.login_id', $school->id)
        ->assertJsonPath('user.roles', [RolesEnum::SchoolAdmin->value]);
});

it('requires password confirmation when registering a school', function () {
    $this->postJson('/api/schools/register', [
        'name' => 'ABC School',
        'contact_name' => 'John Principal',
        'email' => 'school@example.com',
        'password' => 'password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('does not require authentication to register a school', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->postJson('/api/schools/register', [
        'name' => 'ABC School',
        'contact_name' => 'John Principal',
        'email' => 'school@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertCreated();
});

it('returns registration status by email', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->postJson('/api/schools/register', [
        'name' => 'ABC School',
        'contact_name' => 'John Principal',
        'email' => 'school@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'requested_quota' => 50,
    ])->assertCreated();

    $this->getJson('/api/schools/register/status?email=school@example.com')
        ->assertSuccessful()
        ->assertJsonPath('data.email', 'school@example.com')
        ->assertJsonPath('data.status', SchoolStatusEnum::Pending->value)
        ->assertJsonPath('data.requested_quota', 50);
});
