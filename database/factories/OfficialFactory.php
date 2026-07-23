<?php

namespace Database\Factories;

use App\Enums\OfficialStatusEnum;
use App\Enums\OfficialTypeEnum;
use App\Models\Official;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Official>
 */
class OfficialFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(OfficialTypeEnum::cases()),
            'status' => OfficialStatusEnum::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OfficialStatusEnum::Inactive,
        ]);
    }
}
