<?php

namespace Database\Factories;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionCategory>
 */
class CompetitionCategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'name' => fake()->words(2, true),
            'status' => CompetitionCategoryStatusEnum::Active,
        ];
    }
}
