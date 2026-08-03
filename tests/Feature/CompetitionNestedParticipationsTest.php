<?php

use App\Enums\CompetitionTypeEnum;
use App\Enums\ParticipationTypeEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\CompetitionCategoryParticipation;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates competition with nested categories and participations', function () {
    Sanctum::actingAs(User::factory()->create());

    $event = Event::factory()->create();

    $response = $this->postJson('/api/competitions', [
        'event_id' => $event->id,
        'competition_type' => CompetitionTypeEnum::Academic->value,
        'name' => 'Mathematics Olympiad',
        'competition_categories' => [
            [
                'name' => 'A Level',
                'participations' => [
                    ['participation_type' => ParticipationTypeEnum::Individual->value],
                    ['participation_type' => ParticipationTypeEnum::Team->value],
                ],
            ],
            [
                'name' => 'Jr',
                'participations' => [
                    ['participation_type' => ParticipationTypeEnum::Individual->value],
                ],
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Mathematics Olympiad')
        ->assertJsonCount(2, 'data.competition_categories')
        ->assertJsonCount(2, 'data.competition_categories.0.participations')
        ->assertJsonCount(1, 'data.competition_categories.1.participations');

    expect(Competition::query()->count())->toBe(1)
        ->and(CompetitionCategory::query()->count())->toBe(2)
        ->and(CompetitionCategoryParticipation::query()->count())->toBe(3);
});

it('creates category with nested participations', function () {
    Sanctum::actingAs(User::factory()->create());

    $competition = Competition::factory()->create();

    $response = $this->postJson("/api/competitions/{$competition->id}/competition-categories", [
        'name' => 'O Level',
        'participations' => [
            ['participation_type' => ParticipationTypeEnum::Team->value],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'O Level')
        ->assertJsonCount(1, 'data.participations')
        ->assertJsonPath('data.participations.0.participation_type', ParticipationTypeEnum::Team->value);

    expect(CompetitionCategoryParticipation::query()->count())->toBe(1);
});
