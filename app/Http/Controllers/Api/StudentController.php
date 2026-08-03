<?php

namespace App\Http\Controllers\Api;

use App\Enums\GenderEnum;
use App\Enums\StudentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function __construct(private StudentService $studentService) {}

    public function index(School $school): JsonResponse
    {
        $students = $this->studentService->list($school);

        return response()->json([
            'status' => true,
            'message' => 'Students retrieved successfully.',
            'data' => $students,
        ]);
    }

    public function store(Request $request, School $school): JsonResponse
    {
        $payloadData = $request->validate($this->studentPayloadRules());

        $student = $this->studentService->create($school, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Student created successfully.',
            'data' => $student,
        ], 201);
    }

    public function show(School $school, Student $student): JsonResponse
    {
        $this->ensureStudentBelongsToSchool($school, $student);

        return response()->json([
            'status' => true,
            'message' => 'Student retrieved successfully.',
            'data' => $this->studentService->find($student),
        ]);
    }

    public function update(Request $request, School $school, Student $student): JsonResponse
    {
        $this->ensureStudentBelongsToSchool($school, $student);

        $payloadData = $request->validate($this->studentPayloadRules($student));

        $student = $this->studentService->update($student, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Student updated successfully.',
            'data' => $student,
        ]);
    }

    public function destroy(School $school, Student $student): JsonResponse
    {
        $this->ensureStudentBelongsToSchool($school, $student);

        $this->studentService->delete($student);

        return response()->json([
            'status' => true,
            'message' => 'Student deleted successfully.',
        ]);
    }

    private function ensureStudentBelongsToSchool(School $school, Student $student): void
    {
        abort_unless($student->school_id === $school->id, 404);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function studentPayloadRules(?Student $student = null): array
    {
        $studentId = $student?->id;

        return [
            'student_code' => [
                'nullable',
                'string',
                'max:255',
                $studentId
                    ? Rule::unique('students', 'student_code')->ignore($studentId)
                    : Rule::unique('students', 'student_code'),
            ],
            'name' => $studentId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'father_name' => $studentId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'date_of_birth' => $studentId
                ? ['sometimes', 'required', 'date', 'before:today']
                : ['required', 'date', 'before:today'],
            'gender' => $studentId
                ? ['sometimes', 'required', Rule::enum(GenderEnum::class)]
                : ['required', Rule::enum(GenderEnum::class)],
            'class' => $studentId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(StudentStatusEnum::class)],
            'blacklist_reason' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => request()->input('status') === StudentStatusEnum::Blacklisted->value),
            ],
        ];
    }
}
