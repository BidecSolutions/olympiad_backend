<?php

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns user roles and permissions on login', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);
    $user->assignRole(RolesEnum::SuperAdmin);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('status', true)
        ->assertJsonStructure([
            'token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'roles',
                'permissions',
            ],
        ])
        ->assertJsonPath('user.email', 'admin@example.com')
        ->assertJsonPath('user.roles', [RolesEnum::SuperAdmin->value]);

    expect($response->json('user.permissions'))
        ->toContain(PermissionsEnum::EventsList->value);
});

it('returns user roles and permissions on register', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', true)
        ->assertJsonStructure([
            'token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'roles',
                'permissions',
            ],
        ])
        ->assertJsonPath('user.name', 'Jane Doe')
        ->assertJsonPath('user.email', 'jane@example.com')
        ->assertJsonPath('user.roles', [])
        ->assertJsonPath('user.permissions', []);
});
