<?php

namespace App\Http\Controllers\Api;

use App\Enums\SchoolDocumentStatusEnum;
use App\Enums\SchoolDocumentTypeEnum;
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

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->schoolPayloadRules());

        $school = $this->schoolService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'School registered successfully.',
            'data' => $school,
        ], 201);
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
            'status' => ['nullable', Rule::enum(SchoolStatusEnum::class)],
            'documents' => ['nullable', 'array'],
            'documents.*.id' => [
                'nullable',
                'integer',
                $schoolId
                    ? Rule::exists('school_documents', 'id')->where('school_id', $schoolId)
                    : 'prohibited',
            ],
            'documents.*.document_type' => ['required', Rule::enum(SchoolDocumentTypeEnum::class)],
            'documents.*.file_path' => ['required', 'string', 'max:255'],
            'documents.*.status' => ['nullable', Rule::enum(SchoolDocumentStatusEnum::class)],
        ];
    }
}
