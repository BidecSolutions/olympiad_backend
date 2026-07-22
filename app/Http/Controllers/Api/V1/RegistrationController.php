<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Enums\CompetitionStatusEnum;
use App\Enums\ParticipationTypeStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\SchoolStatusEnum;
use App\Enums\StudentStatusEnum;
use App\Enums\TeamStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CompetitionCategoryParticipation;
use App\Models\Registration;
use App\Models\School;
use App\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

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
        $payloadData = $this->validateRegistrationPayload($request, $school);

        $registration = $this->registrationService->create($school, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Registration created successfully.',
            'data' => $registration,
        ], 201);
    }

    public function show(School $school, Registration $registration): JsonResponse
    {
        $this->ensureRegistrationBelongsToSchool($school, $registration);

        return response()->json([
            'status' => true,
            'message' => 'Registration retrieved successfully.',
            'data' => $this->registrationService->find($registration),
        ]);
    }

    public function update(Request $request, School $school, Registration $registration): JsonResponse
    {
        $this->ensureRegistrationBelongsToSchool($school, $registration);

        $payloadData = $this->validateRegistrationPayload($request, $school, $registration);

        $registration = $this->registrationService->update($registration, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Registration updated successfully.',
            'data' => $registration,
        ]);
    }

    public function destroy(School $school, Registration $registration): JsonResponse
    {
        $this->ensureRegistrationBelongsToSchool($school, $registration);

        $this->registrationService->delete($registration);

        return response()->json([
            'status' => true,
            'message' => 'Registration deleted successfully.',
        ]);
    }

    private function ensureRegistrationBelongsToSchool(School $school, Registration $registration): void
    {
        abort_unless($registration->school_id === $school->id, 404);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateRegistrationPayload(
        Request $request,
        School $school,
        ?Registration $registration = null,
    ): array {
        $registrationId = $registration?->id;
        $participationId = $request->input(
            'competition_category_participation_id',
            $registration?->competition_category_participation_id,
        );

        $validator = Validator::make(
            $request->all(),
            $this->registrationPayloadRules($school, $registrationId, $participationId),
        );

        $validator->after(function ($validator) use ($school, $registration, $participationId): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $data = $validator->getData();

            $this->validateRegistrationBusinessRules(
                validator: $validator,
                school: $school,
                participationId: is_numeric($participationId) ? (int) $participationId : null,
                studentId: array_key_exists('student_id', $data)
                    ? $data['student_id']
                    : $registration?->student_id,
                teamId: array_key_exists('team_id', $data)
                    ? $data['team_id']
                    : $registration?->team_id,
            );
        });

        return $validator->validate();
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function registrationPayloadRules(
        School $school,
        ?int $registrationId,
        mixed $participationId,
    ): array {
        return [
            'competition_category_participation_id' => [
                $registrationId ? 'sometimes' : 'required',
                'required',
                'integer',
                'exists:competition_category_participations,id',
            ],
            'student_id' => array_values(array_filter([
                $registrationId ? 'sometimes' : null,
                'required_without:team_id',
                'missing_with:team_id',
                'nullable',
                'integer',
                Rule::exists('students', 'id')
                    ->where('school_id', $school->id)
                    ->where('status', StudentStatusEnum::Active->value),
                Rule::unique('registrations', 'student_id')
                    ->where(
                        'competition_category_participation_id',
                        $participationId,
                    )
                    ->ignore($registrationId),
            ])),
            'team_id' => array_values(array_filter([
                $registrationId ? 'sometimes' : null,
                'required_without:student_id',
                'missing_with:student_id',
                'nullable',
                'integer',
                Rule::exists('teams', 'id')
                    ->where('school_id', $school->id)
                    ->where('status', TeamStatusEnum::Active->value),
                Rule::unique('registrations', 'team_id')
                    ->where(
                        'competition_category_participation_id',
                        $participationId,
                    )
                    ->ignore($registrationId),
            ])),
            'status' => ['nullable', Rule::enum(RegistrationStatusEnum::class)],
        ];
    }

    private function validateRegistrationBusinessRules(
        \Illuminate\Validation\Validator $validator,
        School $school,
        ?int $participationId,
        mixed $studentId,
        mixed $teamId,
    ): void {
        if ($school->status === SchoolStatusEnum::Blacklisted) {
            $validator->errors()->add('school_id', 'Blacklisted schools cannot register for competitions.');
        }

        if ($participationId === null) {
            return;
        }

        $participation = CompetitionCategoryParticipation::query()
            ->with([
                'participationType',
                'competitionCategory.competition',
            ])
            ->find($participationId);

        if ($participation === null) {
            return;
        }

        $category = $participation->competitionCategory;
        $competition = $category?->competition;
        $participationType = $participation->participationType;

        if ($category === null || $competition === null || $participationType === null) {
            $validator->errors()->add(
                'competition_category_participation_id',
                'The selected competition category participation is invalid.',
            );

            return;
        }

        if ($category->status !== CompetitionCategoryStatusEnum::Active) {
            $validator->errors()->add(
                'competition_category_participation_id',
                'Registrations are not allowed for inactive competition categories.',
            );
        }

        if ($competition->status !== CompetitionStatusEnum::Active) {
            $validator->errors()->add(
                'competition_category_participation_id',
                'Registrations are not allowed for inactive competitions.',
            );
        }

        if ($participationType->status !== ParticipationTypeStatusEnum::Active) {
            $validator->errors()->add(
                'competition_category_participation_id',
                'Registrations are not allowed for inactive participation types.',
            );
        }

        $participationTypeName = strtolower($participationType->name);

        if ($studentId !== null && $participationTypeName !== 'individual') {
            $validator->errors()->add(
                'student_id',
                'Individual registration requires an individual participation type.',
            );
        }

        if ($teamId !== null && $participationTypeName !== 'team') {
            $validator->errors()->add(
                'team_id',
                'Team registration requires a team participation type.',
            );
        }

        if ($studentId !== null && $teamId !== null) {
            $validator->errors()->add(
                'student_id',
                'A registration cannot include both a student and a team.',
            );
            $validator->errors()->add(
                'team_id',
                'A registration cannot include both a student and a team.',
            );
        }
    }
}
