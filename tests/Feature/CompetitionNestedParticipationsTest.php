<?php

use App\Enums\CompetitionTypeEnum;
use App\Enums\ParticipationTypeEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates competition with nested categories and participation types', function () {
    Sanctum::actingAs(User::factory()->create());

    $event = Event::factory()->create();

    $response = $this->postJson('/api/competitions', [
        'event_id' => $event->id,
        'competition_type' => CompetitionTypeEnum::Academic->value,
        'name' => 'Mathematics Olympiad',
        'competition_categories' => [
            [
                'name' => 'A Level',
                'participation_type' => ParticipationTypeEnum::Individual->value,
            ],
            [
                'name' => 'Jr',
                'participation_type' => ParticipationTypeEnum::Team->value,
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Mathematics Olympiad')
        ->assertJsonCount(2, 'data.competition_categories')
        ->assertJsonPath('data.competition_categories.0.participation_type', ParticipationTypeEnum::Individual->value)
        ->assertJsonPath('data.competition_categories.1.participation_type', ParticipationTypeEnum::Team->value);

    expect(Competition::query()->count())->toBe(1)
        ->and(CompetitionCategory::query()->count())->toBe(2);
});

it('creates category with participation type', function () {
    Sanctum::actingAs(User::factory()->create());

    $competition = Competition::factory()->create();

    $response = $this->postJson("/api/competitions/{$competition->id}/competition-categories", [
        'name' => 'O Level',
        'participation_type' => ParticipationTypeEnum::Team->value,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'O Level')
        ->assertJsonPath('data.participation_type', ParticipationTypeEnum::Team->value);

    expect(CompetitionCategory::query()->count())->toBe(1);
});
