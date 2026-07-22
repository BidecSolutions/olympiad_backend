<?php

namespace App\Services;

use App\Models\Competition;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompetitionService
{
    /**
     * @return LengthAwarePaginator<int, Competition>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Competition::query()
            ->with(['event', 'competitionType'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Competition
    {
        $competition = Competition::create($data);

        return $competition->load(['event', 'competitionType']);
    }

    public function find(Competition $competition): Competition
    {
        return $competition->load(['event', 'competitionType']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Competition $competition, array $data): Competition
    {
        if ($data !== []) {
            $competition->update($data);
        }

        return $competition->fresh()->load(['event', 'competitionType']);
    }

    public function delete(Competition $competition): void
    {
        $competition->delete();
    }
}
