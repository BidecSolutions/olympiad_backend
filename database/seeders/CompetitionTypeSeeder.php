<?php

namespace Database\Seeders;

use App\Models\CompetitionType;
use Illuminate\Database\Seeder;

class CompetitionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Academic', 'Sports'] as $name) {
            CompetitionType::query()->updateOrCreate(
                ['name' => $name],
            );
        }
    }
}
