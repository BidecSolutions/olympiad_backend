<?php

namespace App\Services;

use App\Models\Competition;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompetitionService
{
    public function __construct(
        private CompetitionCategoryService $categoryService,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Competition>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Competition::query()
            ->with(['event', 'competitionCategories.rule'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Competition
    {
        $competitionCategories = $data['competition_categories'] ?? null;
        unset($data['competition_categories']);

        if (is_array($competitionCategories)) {
            return DB::transaction(fn (): Competition => $this->persistCompetition(
                competition: null,
                data: $data,
                competitionCategories: $competitionCategories,
            ));
        }

        $competition = Competition::create($data);

        return $competition->load(['event', 'competitionCategories']);
    }

    public function find(Competition $competition): Competition
    {
        return $competition->load(['event', 'competitionCategories']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Competition $competition, array $data): Competition
    {
        $competitionCategories = array_key_exists('competition_categories', $data)
            ? $data['competition_categories']
            : null;
        unset($data['competition_categories']);

        $hasRelatedChanges = is_array($competitionCategories);
        $hasCompetitionChanges = $data !== [];

        if ($hasRelatedChanges && $hasCompetitionChanges) {
            return DB::transaction(fn (): Competition => $this->persistCompetition(
                competition: $competition,
                data: $data,
                competitionCategories: $competitionCategories,
            ));
        }

        if ($hasRelatedChanges) {
            return DB::transaction(fn (): Competition => $this->persistCompetition(
                competition: $competition,
                data: [],
                competitionCategories: $competitionCategories,
            ));
        }

        if ($hasCompetitionChanges) {
            $competition->update($data);
        }

        return $competition->fresh()->load(['event', 'competitionCategories']);
    }

    public function delete(Competition $competition): void
    {
        $competition->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>|null  $competitionCategories
     */
    private function persistCompetition(
        ?Competition $competition,
        array $data,
        ?array $competitionCategories,
    ): Competition {
        if ($competition === null) {
            $competition = Competition::create($data);
        } elseif ($data !== []) {
            $competition->update($data);
        }

        if (is_array($competitionCategories)) {
            $this->categoryService->syncForCompetition($competition, $competitionCategories);
        }

        return $competition->fresh()->load(['event', 'competitionCategories']);
    }
}
