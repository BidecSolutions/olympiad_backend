<?php

namespace App\Services;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompetitionCategoryService
{
    /**
     * @return LengthAwarePaginator<int, CompetitionCategory>
     */
    public function list(Competition $competition, int $perPage = 15): LengthAwarePaginator
    {
        return $competition->competitionCategories()
            ->with('competition')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Competition $competition, array $data): CompetitionCategory
    {
        unset($data['competition_id']);

        $category = $competition->competitionCategories()->create($data);

        return $category->load('competition');
    }

    public function find(CompetitionCategory $competitionCategory): CompetitionCategory
    {
        return $competitionCategory->load('competition');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CompetitionCategory $competitionCategory, array $data): CompetitionCategory
    {
        unset($data['competition_id']);

        if ($data !== []) {
            $competitionCategory->update($data);
        }

        return $competitionCategory->fresh()->load('competition');
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
            if (! empty($categoryData['id'])) {
                $category = $competition->competitionCategories()->findOrFail($categoryData['id']);
                $category->update([
                    'name' => $categoryData['name'],
                    'min_age' => $categoryData['min_age'],
                    'max_age' => $categoryData['max_age'],
                    'status' => $categoryData['status'] ?? $category->status,
                ]);
                $categoryIds[] = $category->id;
            } else {
                $category = $competition->competitionCategories()->create([
                    'name' => $categoryData['name'],
                    'min_age' => $categoryData['min_age'],
                    'max_age' => $categoryData['max_age'],
                    'status' => $categoryData['status'] ?? CompetitionCategoryStatusEnum::Active,
                ]);
                $categoryIds[] = $category->id;
            }
        }

        if ($categoryIds === []) {
            $competition->competitionCategories()->delete();

            return;
        }

        $competition->competitionCategories()->whereNotIn('id', $categoryIds)->delete();
    }
}
