<?php

namespace App\Services;

use App\Models\CompetitionType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompetitionTypeService
{
    /**
     * @return LengthAwarePaginator<int, CompetitionType>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return CompetitionType::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): CompetitionType
    {
        return CompetitionType::create($data);
    }

    public function find(CompetitionType $competitionType): CompetitionType
    {
        return $competitionType;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CompetitionType $competitionType, array $data): CompetitionType
    {
        if ($data !== []) {
            $competitionType->update($data);
        }

        return $competitionType->fresh();
    }

    public function delete(CompetitionType $competitionType): void
    {
        $competitionType->delete();
    }
}
