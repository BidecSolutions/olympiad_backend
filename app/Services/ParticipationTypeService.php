<?php

namespace App\Services;

use App\Models\ParticipationType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParticipationTypeService
{
    /**
     * @return LengthAwarePaginator<int, ParticipationType>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return ParticipationType::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ParticipationType
    {
        return ParticipationType::create($data);
    }

    public function find(ParticipationType $participationType): ParticipationType
    {
        return $participationType;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ParticipationType $participationType, array $data): ParticipationType
    {
        if ($data !== []) {
            $participationType->update($data);
        }

        return $participationType->fresh();
    }

    public function delete(ParticipationType $participationType): void
    {
        $participationType->delete();
    }
}
