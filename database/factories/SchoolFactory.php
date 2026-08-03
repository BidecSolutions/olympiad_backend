<?php

namespace Database\Factories;

use App\Enums\SchoolStatusEnum;
use App\Models\School;
use App\Models\User;
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
        $name = fake()->company().' School';
        $email = fake()->unique()->companyEmail();

        return [
            'user_id' => User::factory()->state([
                'name' => $name,
                'email' => $email,
            ]),
            'school_code' => fake()->unique()->bothify('SCH-####'),
            'name' => $name,
            'registration_no' => fake()->unique()->numerify('REG-######'),
            'email' => $email,
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
