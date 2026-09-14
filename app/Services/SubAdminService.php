<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\SubAdmin;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubAdminService
{
    /**
     * @return LengthAwarePaginator<int, SubAdmin>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return SubAdmin::query()
            ->with(['user.permissions', 'user.roles'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): SubAdmin
    {
        $password = $data['password'];
        $permissions = $data['permissions'] ?? null;
        unset($data['password'], $data['password_confirmation'], $data['permissions']);

        return DB::transaction(function () use ($data, $password, $permissions): SubAdmin {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
            ]);

            $user->assignRole(RolesEnum::SubAdmin);

            if (is_array($permissions)) {
                $user->syncPermissions($permissions);
            }

            $subAdmin = SubAdmin::query()->create([
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'region' => $data['region'],
                'status' => $data['status'] ?? 'active',
            ]);

            return $subAdmin->fresh()->load(['user.permissions', 'user.roles']);
        });
    }

    public function find(SubAdmin $subAdmin): SubAdmin
    {
        return $subAdmin->load(['user.permissions', 'user.roles']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SubAdmin $subAdmin, array $data): SubAdmin
    {
        $password = $data['password'] ?? null;
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $permissions = $data['permissions'] ?? null;
        unset($data['password'], $data['password_confirmation'], $data['name'], $data['email'], $data['permissions']);

        return DB::transaction(function () use ($subAdmin, $data, $password, $name, $email, $permissions): SubAdmin {
            if ($data !== []) {
                $subAdmin->update($data);
            }

            $userUpdates = [];

            if (is_string($name) && $name !== '') {
                $userUpdates['name'] = $name;
            }

            if (is_string($email) && $email !== '') {
                $userUpdates['email'] = $email;
            }

            if (is_string($password) && $password !== '') {
                $userUpdates['password'] = $password;
            }

            if ($userUpdates !== []) {
                $subAdmin->user?->update($userUpdates);
            }

            if (is_array($permissions) && $subAdmin->user) {
                $subAdmin->user->syncPermissions($permissions);
            }

            return $subAdmin->fresh()->load(['user.permissions', 'user.roles']);
        });
    }

    public function delete(SubAdmin $subAdmin): void
    {
        DB::transaction(function () use ($subAdmin): void {
            $user = $subAdmin->user;

            $subAdmin->delete();

            $user?->delete();
        });
    }
}
