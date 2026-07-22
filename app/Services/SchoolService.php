<?php

namespace App\Services;

use App\Models\School;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SchoolService
{
    public function __construct(
        private SchoolDocumentService $documentService,
        private SchoolAdminService $adminService,
    ) {}

    /**
     * @return LengthAwarePaginator<int, School>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return School::query()
            ->with(['documents', 'admins.user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): School
    {
        $documents = $data['documents'] ?? null;
        $admins = $data['admins'] ?? null;
        unset($data['documents'], $data['admins']);

        if (is_array($documents) || is_array($admins)) {
            return DB::transaction(fn (): School => $this->persistSchool(
                school: null,
                data: $data,
                documents: $documents,
                admins: $admins,
            ));
        }

        $school = School::create($data);

        return $school->load(['documents', 'admins.user']);
    }

    public function find(School $school): School
    {
        return $school->load(['documents', 'admins.user']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(School $school, array $data): School
    {
        $documents = array_key_exists('documents', $data) ? $data['documents'] : null;
        $admins = array_key_exists('admins', $data) ? $data['admins'] : null;
        unset($data['documents'], $data['admins']);

        $hasRelatedChanges = is_array($documents) || is_array($admins);
        $hasSchoolChanges = $data !== [];

        if ($hasRelatedChanges && $hasSchoolChanges) {
            return DB::transaction(fn (): School => $this->persistSchool(
                school: $school,
                data: $data,
                documents: $documents,
                admins: $admins,
            ));
        }

        if ($hasRelatedChanges) {
            return DB::transaction(fn (): School => $this->persistSchool(
                school: $school,
                data: [],
                documents: $documents,
                admins: $admins,
            ));
        }

        if ($hasSchoolChanges) {
            $school->update($data);
        }

        return $school->fresh()->load(['documents', 'admins.user']);
    }

    public function delete(School $school): void
    {
        $school->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>|null  $documents
     * @param  list<array<string, mixed>>|null  $admins
     */
    private function persistSchool(?School $school, array $data, ?array $documents, ?array $admins): School
    {
        if ($school === null) {
            $school = School::create($data);
        } elseif ($data !== []) {
            $school->update($data);
        }

        if (is_array($documents)) {
            $this->documentService->syncForSchool($school, $documents);
        }

        if (is_array($admins)) {
            $this->adminService->syncForSchool($school, $admins);
        }

        return $school->fresh()->load(['documents', 'admins.user']);
    }
}
