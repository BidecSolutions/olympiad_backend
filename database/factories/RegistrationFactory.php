<?php

namespace Database\Factories;

use App\Enums\RegistrationStatusEnum;
use App\Models\CompetitionCategoryParticipation;
use App\Models\Registration;
use App\Models\School;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'student_id' => fn (array $attributes) => Student::factory()->create([
                'school_id' => $attributes['school_id'],
            ])->id,
            'team_id' => null,
            'competition_category_participation_id' => CompetitionCategoryParticipation::factory(),
            'status' => RegistrationStatusEnum::Pending,
        ];
    }

    public function forTeam(Team $team, CompetitionCategoryParticipation $participation): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $team->school_id,
            'student_id' => null,
            'team_id' => $team->id,
            'competition_category_participation_id' => $participation->id,
        ]);
    }

    public function forStudent(Student $student, CompetitionCategoryParticipation $participation): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'team_id' => null,
            'competition_category_participation_id' => $participation->id,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RegistrationStatusEnum::Approved,
        ]);
    }
}
