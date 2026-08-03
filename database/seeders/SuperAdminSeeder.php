<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            [
                'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@example.com'),
            ],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'password' => env('SUPER_ADMIN_PASSWORD', 'password'),
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole(RolesEnum::SuperAdmin);
    }
}
