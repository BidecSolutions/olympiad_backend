<?php

namespace Database\Factories;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+2 months');

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => EventStatusEnum::Draft,
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatusEnum::Active,
        ]);
    }
}
