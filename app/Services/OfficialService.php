<?php

namespace App\Services;

use App\Enums\OfficialTypeEnum;
use App\Enums\RolesEnum;
use App\Models\Official;
use App\Models\User;
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
        $official->load('user');

        $this->assignOfficialRole($official);

        return $official;
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
        $previousUserId = $official->user_id;
        $previousType = $official->type;

        if ($data !== []) {
            $official->update($data);
        }

        $official = $official->fresh()->load('user');

        if ($previousUserId !== $official->user_id) {
            $this->removeOfficialRole(User::query()->find($previousUserId), $previousType);
            $this->assignOfficialRole($official);
        } elseif (array_key_exists('type', $data) && $previousType !== $official->type) {
            $this->removeOfficialRole($official->user, $previousType);
            $this->assignOfficialRole($official);
        }

        return $official;
    }

    public function delete(Official $official): void
    {
        $user = $official->user;
        $type = $official->type;

        $official->delete();

        $this->removeOfficialRole($user, $type);
    }

    private function assignOfficialRole(Official $official): void
    {
        $official->user?->assignRole(RolesEnum::fromOfficialType($official->type)->value);
    }

    private function removeOfficialRole(?User $user, OfficialTypeEnum $type): void
    {
        $user?->removeRole(RolesEnum::fromOfficialType($type)->value);
    }
}
