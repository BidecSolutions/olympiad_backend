<?php

namespace Database\Seeders;

use App\Enums\CompetitionStatusEnum;
use App\Enums\CompetitionTypeEnum;
use App\Enums\EventStatusEnum;
use App\Enums\ParticipationTypeEnum;
use App\Enums\RolesEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\CompetitionCategoryRule;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->role(RolesEnum::SuperAdmin->value)
            ->first();

        if (! $admin) {
            return;
        }

        $event = Event::query()->updateOrCreate(
            ['name' => 'Baitussalam Olympiad 2025'],
            [
                'description' => 'Annual inter-school olympiad event',
                'year' => 2025,
                'status' => EventStatusEnum::Active,
                'created_by' => $admin->id,
            ],
        );

        $competitions = [
            [
                'name' => 'Math Olympiad 2025',
                'competition_type' => CompetitionTypeEnum::Academic,
                'categories' => [
                    ['name' => 'Junior Boys (U-14)', 'participation_type' => ParticipationTypeEnum::Team],
                    ['name' => 'Junior Girls (U-14)', 'participation_type' => ParticipationTypeEnum::Individual],
                    ['name' => 'Senior Boys', 'participation_type' => ParticipationTypeEnum::Individual],
                ],
            ],
            [
                'name' => 'Basketball Championship',
                'competition_type' => CompetitionTypeEnum::Sports,
                'categories' => [
                    ['name' => 'Junior Boys', 'participation_type' => ParticipationTypeEnum::Team],
                    ['name' => 'Girls Team - Senior', 'participation_type' => ParticipationTypeEnum::Team],
                ],
            ],
            [
                'name' => 'Football Tournament',
                'competition_type' => CompetitionTypeEnum::Sports,
                'categories' => [
                    ['name' => 'Junior Boys', 'participation_type' => ParticipationTypeEnum::Team],
                    ['name' => 'Senior Boys', 'participation_type' => ParticipationTypeEnum::Team],
                ],
            ],
            [
                'name' => 'Science Olympiad',
                'competition_type' => CompetitionTypeEnum::Academic,
                'categories' => [
                    ['name' => 'Junior Girls', 'participation_type' => ParticipationTypeEnum::Individual],
                    ['name' => 'Solo Event - Girls', 'participation_type' => ParticipationTypeEnum::Individual],
                ],
            ],
            [
                'name' => 'Art Competition',
                'competition_type' => CompetitionTypeEnum::Academic,
                'categories' => [
                    ['name' => 'Junior Girls (U-14)', 'participation_type' => ParticipationTypeEnum::Individual],
                    ['name' => 'Senior Girls', 'participation_type' => ParticipationTypeEnum::Individual],
                ],
            ],
        ];

        foreach ($competitions as $item) {
            $competition = Competition::query()->updateOrCreate(
                [
                    'event_id' => $event->id,
                    'name' => $item['name'],
                ],
                [
                    'competition_type' => $item['competition_type'],
                    'description' => $item['name'].' competition',
                    'status' => CompetitionStatusEnum::Active,
                ],
            );

            foreach ($item['categories'] as $categoryData) {
                $category = CompetitionCategory::query()->updateOrCreate(
                    [
                        'competition_id' => $competition->id,
                        'name' => $categoryData['name'],
                    ],
                    [
                        'participation_type' => $categoryData['participation_type'],
                    ],
                );

                CompetitionCategoryRule::query()->updateOrCreate(
                    ['competition_category_id' => $category->id],
                    [
                        'max_participants' => $categoryData['participation_type'] === ParticipationTypeEnum::Individual ? 1 : null,
                        'max_teams' => $categoryData['participation_type'] === ParticipationTypeEnum::Team ? 16 : null,
                        'min_team_members' => $categoryData['participation_type'] === ParticipationTypeEnum::Team ? 5 : null,
                        'max_team_members' => $categoryData['participation_type'] === ParticipationTypeEnum::Team ? 12 : null,
                    ],
                );
            }
        }
    }
}
