<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolAdmin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SchoolAdminService
{
    /**
     * @return LengthAwarePaginator<int, SchoolAdmin>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return SchoolAdmin::query()
            ->with(['school', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, SchoolAdmin>
     */
    public function listBySchool(School $school): Collection
    {
        return $school->admins()->with('user')->latest()->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): SchoolAdmin
    {
        $admin = SchoolAdmin::create($data);

        return $admin->load(['school', 'user']);
    }

    public function find(SchoolAdmin $admin): SchoolAdmin
    {
        return $admin->load(['school', 'user']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SchoolAdmin $admin, array $data): SchoolAdmin
    {
        $admin->update($data);

        return $admin->fresh()->load(['school', 'user']);
    }

    public function delete(SchoolAdmin $admin): void
    {
        $admin->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $admins
     */
    public function syncForSchool(School $school, array $admins): void
    {
        $adminIds = [];

        foreach ($admins as $adminData) {
            if (! empty($adminData['id'])) {
                $admin = $school->admins()->findOrFail($adminData['id']);
                $admin->update(['user_id' => $adminData['user_id']]);
                $adminIds[] = $admin->id;
            } else {
                $admin = $school->admins()->create(['user_id' => $adminData['user_id']]);
                $adminIds[] = $admin->id;
            }
        }

        if ($adminIds === []) {
            $school->admins()->delete();

            return;
        }

        $school->admins()->whereNotIn('id', $adminIds)->delete();
    }
}
