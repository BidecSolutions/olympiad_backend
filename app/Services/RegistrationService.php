<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\School;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RegistrationService
{
    /**
     * @return list<string>
     */
    public static function defaultRelations(): array
    {
        return [
            'school',
            'student',
            'team.members.student',
            'competitionCategoryParticipation.competitionCategory.competition',
        ];
    }

    /**
     * @return LengthAwarePaginator<int, Registration>
     */
    public function list(School $school, int $perPage = 15): LengthAwarePaginator
    {
        return $school->registrations()
            ->with(self::defaultRelations())
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(School $school, array $data): Registration
    {
        unset($data['school_id']);

        $registration = $school->registrations()->create($data);

        return $registration->load(self::defaultRelations());
    }

    public function find(Registration $registration): Registration
    {
        return $registration->load(self::defaultRelations());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Registration $registration, array $data): Registration
    {
        unset($data['school_id']);

        if ($data !== []) {
            $registration->update($data);
        }

        return $registration->fresh()->load(self::defaultRelations());
    }

    public function delete(Registration $registration): void
    {
        $registration->delete();
    }
}
