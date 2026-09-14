<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentRegistration;
use App\Services\StudentRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentRegistrationController extends Controller
{
    public function __construct(private StudentRegistrationService $registrationService) {}

    public function index(School $school): JsonResponse
    {
        $registrations = $this->registrationService->list($school);

        return response()->json([
            'status' => true,
            'message' => 'Registrations retrieved successfully.',
            'data' => $registrations,
        ]);
    }

    public function store(Request $request, School $school): JsonResponse
    {
        $payloadData = $request->validate($this->registrationPayloadRules($school));

        $this->ensureStudentBelongsToSchool($school, (int) $payloadData['student_id']);
        $this->ensureCategoryBelongsToCompetition(
            (int) $payloadData['competition_id'],
            (int) $payloadData['competition_category_id'],
        );

        $registration = $this->registrationService->create($school, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Registration submitted successfully.',
            'data' => $registration,
        ], 201);
    }

    public function show(School $school, StudentRegistration $registration): JsonResponse
    {
        $this->ensureRegistrationBelongsToSchool($school, $registration);

        return response()->json([
            'status' => true,
            'message' => 'Registration retrieved successfully.',
            'data' => $this->registrationService->find($registration),
        ]);
    }

    private function ensureRegistrationBelongsToSchool(
        School $school,
        StudentRegistration $registration,
    ): void {
        abort_unless($registration->school_id === $school->id, 404);
    }

    private function ensureStudentBelongsToSchool(School $school, int $studentId): void
    {
        $exists = Student::query()
            ->whereKey($studentId)
            ->where('school_id', $school->id)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'student_id' => ['The selected student is invalid for this school.'],
            ]);
        }
    }

    private function ensureCategoryBelongsToCompetition(
        int $competitionId,
        int $competitionCategoryId,
    ): void {
        $exists = \App\Models\CompetitionCategory::query()
            ->whereKey($competitionCategoryId)
            ->where('competition_id', $competitionId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'competition_category_id' => ['The selected category does not belong to this competition.'],
            ]);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function registrationPayloadRules(School $school): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')->where('school_id', $school->id),
            ],
            'competition_id' => [
                'required',
                'integer',
                Rule::exists('competitions', 'id'),
            ],
            'competition_category_id' => [
                'required',
                'integer',
                Rule::exists('competition_categories', 'id'),
            ],
            'shirt_number' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
