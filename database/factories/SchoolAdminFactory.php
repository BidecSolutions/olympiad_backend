<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolAdmin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolAdmin>
 */
class SchoolAdminFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'user_id' => User::factory(),
        ];
    }
}
