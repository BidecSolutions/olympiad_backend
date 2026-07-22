<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Enums\StudentStatusEnum;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'student_code' => fake()->unique()->bothify('STU-####'),
            'name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-5 years'),
            'gender' => fake()->randomElement(GenderEnum::cases()),
            'class' => (string) fake()->numberBetween(1, 12),
            'section' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'photo' => null,
            'status' => StudentStatusEnum::Active,
            'blacklist_reason' => null,
        ];
    }

    public function blacklisted(?string $reason = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StudentStatusEnum::Blacklisted,
            'blacklist_reason' => $reason ?? fake()->sentence(),
        ]);
    }
}
