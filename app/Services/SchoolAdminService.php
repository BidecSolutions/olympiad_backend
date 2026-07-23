<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\School;
use App\Models\SchoolAdmin;
use App\Models\User;
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
        $admin->load(['school', 'user']);

        $this->assignSchoolAdminRole($admin->user);

        return $admin;
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
        $previousUserId = $admin->user_id;

        $admin->update($data);
        $admin = $admin->fresh()->load(['school', 'user']);

        if ($previousUserId !== $admin->user_id) {
            $this->removeSchoolAdminRoleIfNeeded($previousUserId);
            $this->assignSchoolAdminRole($admin->user);
        }

        return $admin;
    }

    public function delete(SchoolAdmin $admin): void
    {
        $userId = $admin->user_id;

        $admin->delete();

        $this->removeSchoolAdminRoleIfNeeded($userId);
    }

    /**
     * @param  list<array<string, mixed>>  $admins
     */
    public function syncForSchool(School $school, array $admins): void
    {
        $existingAdmins = $school->admins()->get();
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

        $adminsToRemove = $existingAdmins->whereNotIn('id', $adminIds);

        if ($adminIds === []) {
            $adminsToRemove = $existingAdmins;
            $school->admins()->delete();
        } else {
            $school->admins()->whereNotIn('id', $adminIds)->delete();
        }

        foreach ($admins as $adminData) {
            $this->assignSchoolAdminRole(User::query()->find($adminData['user_id']));
        }

        foreach ($adminsToRemove as $admin) {
            $this->removeSchoolAdminRoleIfNeeded($admin->user_id);
        }
    }

    private function assignSchoolAdminRole(?User $user): void
    {
        $user?->assignRole(RolesEnum::SchoolAdmin->value);
    }

    private function removeSchoolAdminRoleIfNeeded(int $userId): void
    {
        $user = User::query()->find($userId);

        if ($user === null || $user->schoolAdmins()->exists()) {
            return;
        }

        $user->removeRole(RolesEnum::SchoolAdmin->value);
    }
}
