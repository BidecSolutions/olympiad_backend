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
            ->with(['event', 'competitionType', 'categories'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Competition
    {
        $categories = $data['categories'] ?? null;
        unset($data['categories']);

        if (is_array($categories)) {
            return DB::transaction(fn (): Competition => $this->persistCompetition(
                competition: null,
                data: $data,
                categories: $categories,
            ));
        }

        $competition = Competition::create($data);

        return $competition->load(['event', 'competitionType', 'categories']);
    }

    public function find(Competition $competition): Competition
    {
        return $competition->load(['event', 'competitionType', 'categories']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Competition $competition, array $data): Competition
    {
        $categories = array_key_exists('categories', $data) ? $data['categories'] : null;
        unset($data['categories']);

        $hasRelatedChanges = is_array($categories);
        $hasCompetitionChanges = $data !== [];

        if ($hasRelatedChanges && $hasCompetitionChanges) {
            return DB::transaction(fn (): Competition => $this->persistCompetition(
                competition: $competition,
                data: $data,
                categories: $categories,
            ));
        }

        if ($hasRelatedChanges) {
            return DB::transaction(fn (): Competition => $this->persistCompetition(
                competition: $competition,
                data: [],
                categories: $categories,
            ));
        }

        if ($hasCompetitionChanges) {
            $competition->update($data);
        }

        return $competition->fresh()->load(['event', 'competitionType', 'categories']);
    }

    public function delete(Competition $competition): void
    {
        $competition->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>|null  $categories
     */
    private function persistCompetition(?Competition $competition, array $data, ?array $categories): Competition
    {
        if ($competition === null) {
            $competition = Competition::create($data);
        } elseif ($data !== []) {
            $competition->update($data);
        }

        if (is_array($categories)) {
            $this->categoryService->syncForCompetition($competition, $categories);
        }

        return $competition->fresh()->load(['event', 'competitionType', 'categories']);
    }
}
