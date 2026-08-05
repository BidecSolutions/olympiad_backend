<?php

namespace Database\Factories;

use App\Models\CompetitionCategory;
use App\Models\CompetitionCategoryRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionCategoryRule>
 */
class CompetitionCategoryRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxTeams = fake()->numberBetween(1, 16);
        $minTeamMembers = fake()->numberBetween(1, 3);
        $maxTeamMembers = fake()->numberBetween($minTeamMembers, 10);

        return [
            'competition_category_id' => CompetitionCategory::factory(),
            'max_teams' => $maxTeams,
            'min_team_members' => $minTeamMembers,
            'max_team_members' => $maxTeamMembers,
            'max_participants' => $maxTeams * $maxTeamMembers,
        ];
    }
}
