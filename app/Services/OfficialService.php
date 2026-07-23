<?php

namespace App\Services;

use App\Models\Official;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OfficialService
{
    /**
     * @return LengthAwarePaginator<int, Official>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Official::query()
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Official
    {
        $official = Official::create($data);

        return $official->load('user');
    }

    public function find(Official $official): Official
    {
        return $official->load('user');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Official $official, array $data): Official
    {
        if ($data !== []) {
            $official->update($data);
        }

        return $official->fresh()->load('user');
    }

    public function delete(Official $official): void
    {
        $official->delete();
    }
}
