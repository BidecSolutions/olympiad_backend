<?php

namespace App\Http\Controllers\Api;

use App\Enums\SchoolStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\SchoolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SchoolController extends Controller
{
    public function __construct(private SchoolService $schoolService) {}

    public function index(): JsonResponse
    {
        $schools = $this->schoolService->list();

        return response()->json([
            'status' => true,
            'message' => 'Schools retrieved successfully.',
            'data' => $schools,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->schoolRegistrationRules());

        $school = $this->schoolService->register($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Registration submitted. Super Admin will review your application.',
            'data' => $school,
        ], 201);
    }

    public function registrationStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $school = School::query()
            ->with('user')
            ->where('email', $validated['email'])
            ->first();

        if ($school === null) {
            return response()->json([
                'status' => false,
                'message' => 'No registration found for this email.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Registration status retrieved successfully.',
            'data' => [
                'id' => $school->id,
                'application_id' => sprintf('APP-%d-%06d', now()->year, $school->id),
                'school_id' => $school->school_code ?? sprintf('SCH-%d-%04d', now()->year, $school->id),
                'name' => $school->name,
                'email' => $school->email,
                'status' => $school->status->value,
                'requested_quota' => $school->requested_quota,
                'approved_quota' => $school->approved_quota ?? $school->requested_quota,
                'submitted_at' => $school->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function show(School $school): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'School retrieved successfully.',
            'data' => $this->schoolService->find($school),
        ]);
    }

    public function update(Request $request, School $school): JsonResponse
    {
        $user = $request->user();
        if ($user?->school && $user->school->id !== $school->id) {
            abort(403, 'You can only update your own school profile.');
        }

        $payloadData = $request->validate($this->schoolPayloadRules($school));

        $school = $this->schoolService->update($school, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'School updated successfully.',
            'data' => $school,
        ]);
    }

    public function destroy(School $school): JsonResponse
    {
        $this->schoolService->delete($school);

        return response()->json([
            'status' => true,
            'message' => 'School deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function schoolPayloadRules(?School $school = null): array
    {
        $schoolId = $school?->id;
        $userId = $school?->user_id;

        return [
            'school_code' => [
                'nullable',
                'string',
                'max:255',
                $schoolId
                    ? Rule::unique('schools', 'school_code')->ignore($schoolId)
                    : Rule::unique('schools', 'school_code'),
            ],
            'name' => $schoolId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'email' => $schoolId
                ? [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('schools', 'email')->ignore($schoolId),
                    Rule::unique('users', 'email')->ignore($userId),
                ]
                : [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('schools', 'email'),
                    Rule::unique('users', 'email'),
                ],
            'password' => $schoolId
                ? ['sometimes', 'nullable', 'string', 'confirmed', Password::defaults()]
                : ['required', 'string', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'school_type' => ['nullable', 'string', 'max:255'],
            'establishment_year' => ['nullable', 'integer', 'min:1800', 'max:'.(int) date('Y')],
            'website' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
            'contact_designation' => ['nullable', 'string', 'max:255'],
            'alternate_phone' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'requested_quota' => ['nullable', 'integer', 'min:1'],
            'approved_quota' => ['nullable', 'integer', 'min:1'],
            'interested_competitions' => ['nullable', 'array'],
            'interested_competitions.*' => ['string', 'max:255'],
            'status' => ['nullable', Rule::enum(SchoolStatusEnum::class)],
        ];
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function schoolRegistrationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'registration_no' => ['nullable', 'string', 'max:255'],
            'school_type' => ['nullable', 'string', 'max:255'],
            'establishment_year' => ['nullable', 'integer', 'min:1800', 'max:'.(int) date('Y')],
            'website' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('schools', 'email'),
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:255'],
            'alternate_phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'contact_designation' => ['nullable', 'string', 'max:255'],
            'requested_quota' => ['nullable', 'integer', 'min:1'],
            'interested_competitions' => ['nullable', 'array'],
            'interested_competitions.*' => ['string', 'max:255'],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => [
                'required',
                'string',
                Rule::enum(\App\Enums\SchoolDocumentTypeEnum::class),
            ],
            'documents.*.file_path' => ['required', 'string', 'max:255'],
        ];
    }
}
