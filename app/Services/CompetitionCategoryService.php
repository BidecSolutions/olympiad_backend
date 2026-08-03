<?php

namespace App\Services;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompetitionCategoryService
{
    public function __construct(
        private CompetitionCategoryParticipationService $participationService,
    ) {}

    /**
     * @return LengthAwarePaginator<int, CompetitionCategory>
     */
    public function list(Competition $competition, int $perPage = 15): LengthAwarePaginator
    {
        return $competition->competitionCategories()
            ->with(['competition', 'participations'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Competition $competition, array $data): CompetitionCategory
    {
        $participations = $data['participations'] ?? null;
        unset($data['participations'], $data['competition_id']);

        if (is_array($participations)) {
            return DB::transaction(fn (): CompetitionCategory => $this->persistCategory(
                competition: $competition,
                category: null,
                data: $data,
                participations: $participations,
            ));
        }

        $category = $competition->competitionCategories()->create($data);

        return $category->load(['competition', 'participations']);
    }

    public function find(CompetitionCategory $competitionCategory): CompetitionCategory
    {
        return $competitionCategory->load(['competition', 'participations']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CompetitionCategory $competitionCategory, array $data): CompetitionCategory
    {
        $participations = array_key_exists('participations', $data)
            ? $data['participations']
            : null;
        unset($data['participations'], $data['competition_id']);

        $hasRelatedChanges = is_array($participations);
        $hasCategoryChanges = $data !== [];

        if ($hasRelatedChanges && $hasCategoryChanges) {
            return DB::transaction(fn (): CompetitionCategory => $this->persistCategory(
                competition: $competitionCategory->competition,
                category: $competitionCategory,
                data: $data,
                participations: $participations,
            ));
        }

        if ($hasRelatedChanges) {
            return DB::transaction(fn (): CompetitionCategory => $this->persistCategory(
                competition: $competitionCategory->competition,
                category: $competitionCategory,
                data: [],
                participations: $participations,
            ));
        }

        if ($hasCategoryChanges) {
            $competitionCategory->update($data);
        }

        return $competitionCategory->fresh()->load(['competition', 'participations']);
    }

    public function delete(CompetitionCategory $competitionCategory): void
    {
        $competitionCategory->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $competitionCategories
     */
    public function syncForCompetition(Competition $competition, array $competitionCategories): void
    {
        $categoryIds = [];

        foreach ($competitionCategories as $categoryData) {
            $participations = array_key_exists('participations', $categoryData)
                ? $categoryData['participations']
                : null;
            unset($categoryData['participations']);

            if (! empty($categoryData['id'])) {
                $category = $competition->competitionCategories()->findOrFail($categoryData['id']);
                $category->update([
                    'name' => $categoryData['name'],
                    'status' => $categoryData['status'] ?? $category->status,
                ]);
                $categoryIds[] = $category->id;
            } else {
                $category = $competition->competitionCategories()->create([
                    'name' => $categoryData['name'],
                    'status' => $categoryData['status'] ?? CompetitionCategoryStatusEnum::Active,
                ]);
                $categoryIds[] = $category->id;
            }

            if (is_array($participations)) {
                $this->participationService->syncForCategory($category, $participations);
            }
        }

        if ($categoryIds === []) {
            $competition->competitionCategories()->delete();

            return;
        }

        $competition->competitionCategories()->whereNotIn('id', $categoryIds)->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>|null  $participations
     */
    private function persistCategory(
        Competition $competition,
        ?CompetitionCategory $category,
        array $data,
        ?array $participations,
    ): CompetitionCategory {
        if ($category === null) {
            $category = $competition->competitionCategories()->create($data);
        } elseif ($data !== []) {
            $category->update($data);
        }

        if (is_array($participations)) {
            $this->participationService->syncForCategory($category, $participations);
        }

        return $category->fresh()->load(['competition', 'participations']);
    }
}
