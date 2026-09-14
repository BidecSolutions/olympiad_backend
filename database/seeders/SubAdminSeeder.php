<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Enums\SubAdminStatusEnum;
use App\Models\SubAdmin;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('SUB_ADMIN_EMAIL', 'subadmin@baitussalam.com');

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SUB_ADMIN_NAME', 'Ahmed Khan'),
                'password' => env('SUB_ADMIN_PASSWORD', 'password123'),
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole(RolesEnum::SubAdmin)) {
            $user->assignRole(RolesEnum::SubAdmin);
        }

        SubAdmin::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => '03001234567',
                'region' => 'Karachi',
                'status' => SubAdminStatusEnum::Active,
            ],
        );

        $subAdminId = SubAdmin::query()->where('user_id', $user->id)->value('id');
        $this->command?->info("Sub Admin seeded: {$user->email} (Sub Admin ID {$subAdminId}) / password123");
    }
}
