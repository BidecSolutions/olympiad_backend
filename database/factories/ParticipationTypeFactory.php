<?php

namespace Database\Factories;

use App\Enums\ParticipationTypeStatusEnum;
use App\Models\ParticipationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParticipationType>
 */
class ParticipationTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'status' => ParticipationTypeStatusEnum::Active,
        ];
    }
}
