<?php

namespace Database\Factories;

use App\Enums\CompetitionStatusEnum;
use App\Enums\CompetitionTypeEnum;
use App\Enums\ScoringTypeEnum;
use App\Models\Competition;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'competition_type' => CompetitionTypeEnum::Academic,
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'scoring_type' => ScoringTypeEnum::Points,
            'status' => CompetitionStatusEnum::Draft,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CompetitionStatusEnum::Active,
        ]);
    }
}
