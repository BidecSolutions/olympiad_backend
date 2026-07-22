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
            ->with(['competitionCategory', 'participationType'])
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

        return $participation->load(['competitionCategory', 'participationType']);
    }

    public function find(CompetitionCategoryParticipation $participation): CompetitionCategoryParticipation
    {
        return $participation->load(['competitionCategory', 'participationType']);
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

        return $participation->fresh()->load(['competitionCategory', 'participationType']);
    }

    public function delete(CompetitionCategoryParticipation $participation): void
    {
        $participation->delete();
    }
}
