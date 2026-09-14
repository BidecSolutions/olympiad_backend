<?php

namespace App\Services;

use App\Enums\RegistrationStatusEnum;
use App\Models\School;
use App\Models\StudentRegistration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class StudentRegistrationService
{
    /**
     * @return LengthAwarePaginator<int, StudentRegistration>
     */
    public function list(School $school, int $perPage = 50): LengthAwarePaginator
    {
        return $school->studentRegistrations()
            ->with([
                'student',
                'competition',
                'competitionCategory',
            ])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(School $school, array $data): StudentRegistration
    {
        unset($data['school_id']);

        $data['registration_code'] = $this->nextRegistrationCode();
        $data['status'] = $data['status'] ?? RegistrationStatusEnum::Submitted;
        $data['payment_status'] = $data['payment_status'] ?? 'Pending Payment';
        $data['amount'] = $data['amount'] ?? 'PKR 5,000';
        $data['psid'] = $data['psid'] ?? $this->generatePsid();

        $registration = $school->studentRegistrations()->create($data);

        return $this->find($registration);
    }

    public function find(StudentRegistration $registration): StudentRegistration
    {
        return $registration->load([
            'student',
            'competition',
            'competitionCategory',
        ]);
    }

    private function nextRegistrationCode(): string
    {
        $prefix = 'REG-'.now()->format('ym').'-';
        $latest = StudentRegistration::query()
            ->where('registration_code', 'like', $prefix.'%')
            ->orderByDesc('registration_code')
            ->value('registration_code');

        $next = 1;
        if (is_string($latest)) {
            $suffix = (int) substr($latest, strlen($prefix));
            $next = $suffix + 1;
        }

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function generatePsid(): string
    {
        return 'PSID-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
    }
}
