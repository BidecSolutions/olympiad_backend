<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\School;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SchoolService
{
    /**
     * @return LengthAwarePaginator<int, School>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return School::query()
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): School
    {
        $password = $data['password'];
        unset($data['password'], $data['password_confirmation']);

        return DB::transaction(function () use ($data, $password): School {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
            ]);

            $user->assignRole(RolesEnum::SchoolAdmin);

            $school = School::query()->create([
                ...$data,
                'user_id' => $user->id,
            ]);

            return $school->fresh()->load('user');
        });
    }

    public function find(School $school): School
    {
        return $school->load('user');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(School $school, array $data): School
    {
        $password = $data['password'] ?? null;
        unset($data['password'], $data['password_confirmation']);

        return DB::transaction(function () use ($school, $data, $password): School {
            if ($data !== []) {
                $school->update($data);
            }

            $userUpdates = [];

            if (array_key_exists('name', $data)) {
                $userUpdates['name'] = $data['name'];
            }

            if (array_key_exists('email', $data)) {
                $userUpdates['email'] = $data['email'];
            }

            if (is_string($password) && $password !== '') {
                $userUpdates['password'] = $password;
            }

            if ($userUpdates !== []) {
                $school->user?->update($userUpdates);
            }

            return $school->fresh()->load('user');
        });
    }

    public function delete(School $school): void
    {
        DB::transaction(function () use ($school): void {
            $user = $school->user;

            $school->delete();

            $user?->delete();
        });
    }
}
