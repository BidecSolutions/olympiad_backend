<?php

namespace App\Services;

use App\Models\School;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentService
{
    /**
     * @return LengthAwarePaginator<int, Student>
     */
    public function list(School $school, int $perPage = 15): LengthAwarePaginator
    {
        return $school->students()
            ->with('school')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(School $school, array $data): Student
    {
        unset($data['school_id']);

        $student = $school->students()->create($data);

        return $student->load('school');
    }

    public function find(Student $student): Student
    {
        return $student->load('school');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Student $student, array $data): Student
    {
        unset($data['school_id']);

        if ($data !== []) {
            $student->update($data);
        }

        return $student->fresh()->load('school');
    }

    public function delete(Student $student): void
    {
        $student->delete();
    }
}
