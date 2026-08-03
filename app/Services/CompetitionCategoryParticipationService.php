<?php

namespace App\Services;

use App\Models\CompetitionCategory;
use App\Models\CompetitionCategoryParticipation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompetitionCategoryParticipationService
{
    /**
     * @return LengthAwarePaginator<int, CompetitionCategoryParticipation>
     */
    public function list(CompetitionCategory $competitionCategory, int $perPage = 15): LengthAwarePaginator
    {
        return $competitionCategory->participations()
            ->with(['competitionCategory'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(CompetitionCategory $competitionCategory, array $data): CompetitionCategoryParticipation
    {
        unset($data['competition_category_id']);

        $participation = $competitionCategory->participations()->create($data);

        return $participation->load(['competitionCategory']);
    }

    public function find(CompetitionCategoryParticipation $participation): CompetitionCategoryParticipation
    {
        return $participation->load(['competitionCategory']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(
        CompetitionCategoryParticipation $participation,
        array $data,
    ): CompetitionCategoryParticipation {
        unset($data['competition_category_id']);

        if ($data !== []) {
            $participation->update($data);
        }

        return $participation->fresh()->load(['competitionCategory']);
    }

    public function delete(CompetitionCategoryParticipation $participation): void
    {
        $participation->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $participations
     */
    public function syncForCategory(CompetitionCategory $competitionCategory, array $participations): void
    {
        $participationIds = [];

        foreach ($participations as $participationData) {
            if (! empty($participationData['id'])) {
                $participation = $competitionCategory->participations()->findOrFail($participationData['id']);
                $participation->update([
                    'participation_type' => $participationData['participation_type'],
                ]);
                $participationIds[] = $participation->id;
            } else {
                $participation = $competitionCategory->participations()->create([
                    'participation_type' => $participationData['participation_type'],
                ]);
                $participationIds[] = $participation->id;
            }
        }

        if ($participationIds === []) {
            $competitionCategory->participations()->delete();

            return;
        }

        $competitionCategory->participations()->whereNotIn('id', $participationIds)->delete();
    }
}
