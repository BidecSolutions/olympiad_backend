<?php

namespace Database\Factories;

use App\Enums\SchoolStatusEnum;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_code' => fake()->unique()->bothify('SCH-####'),
            'name' => fake()->company().' School',
            'registration_no' => fake()->unique()->numerify('REG-######'),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'status' => SchoolStatusEnum::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SchoolStatusEnum::Approved,
        ]);
    }
}
