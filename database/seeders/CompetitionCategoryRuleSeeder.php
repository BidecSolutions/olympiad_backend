<?php

namespace Database\Seeders;

use App\Models\CompetitionCategoryRule;
use Illuminate\Database\Seeder;

class CompetitionCategoryRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompetitionCategoryRule::factory()->count(10)->create();
    }
}
