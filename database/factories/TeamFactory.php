<?php

namespace Database\Factories;

use App\Enums\TeamStatusEnum;
use App\Models\School;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => fake()->unique()->words(2, true),
            'status' => TeamStatusEnum::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TeamStatusEnum::Inactive,
        ]);
    }
}
