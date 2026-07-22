<?php

namespace Database\Factories;

use App\Models\CompetitionCategory;
use App\Models\CompetitionCategoryParticipation;
use App\Models\ParticipationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionCategoryParticipation>
 */
class CompetitionCategoryParticipationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'competition_category_id' => CompetitionCategory::factory(),
            'participation_type_id' => ParticipationType::factory(),
        ];
    }
}
