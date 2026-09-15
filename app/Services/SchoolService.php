<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Enums\SchoolStatusEnum;
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
        $contactName = $data['contact_name'] ?? $data['name'];
        unset($data['password'], $data['password_confirmation'], $data['contact_name']);

        return DB::transaction(function () use ($data, $password, $contactName): School {
            $user = User::query()->create([
                'name' => $contactName,
                'email' => $data['email'],
                'password' => $password,
            ]);

            $user->assignRole(RolesEnum::SchoolAdmin);

            $school = School::query()->create([
                ...$data,
                'user_id' => $user->id,
                'status' => $data['status'] ?? SchoolStatusEnum::Pending->value,
            ]);

            return $school->fresh()->load('user');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): School
    {
        $data['status'] = SchoolStatusEnum::Pending->value;

        $documents = $data['documents'] ?? [];
        unset($data['documents']);

        $school = $this->create($data);

        foreach ($documents as $document) {
            $school->documents()->create([
                'document_type' => $document['document_type'],
                'file_path' => $document['file_path'],
            ]);
        }

        return $school->fresh()->load(['user', 'documents']);
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
        $contactName = $data['contact_name'] ?? null;
        unset($data['password'], $data['password_confirmation'], $data['contact_name']);

        return DB::transaction(function () use ($school, $data, $password, $contactName): School {
            if ($data !== []) {
                $school->update($data);
            }

            $userUpdates = [];

            if (is_string($contactName) && $contactName !== '') {
                $userUpdates['name'] = $contactName;
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
