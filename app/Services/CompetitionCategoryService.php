<?php

namespace App\Services;

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
        return $competition->categories()
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

        $category = $competition->categories()->create($data);

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
}
