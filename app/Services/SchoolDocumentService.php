<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SchoolDocumentService
{
    /**
     * @return LengthAwarePaginator<int, SchoolDocument>
     */
    public function list(School $school, int $perPage = 15): LengthAwarePaginator
    {
        return $school->documents()
            ->with('school')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(School $school, array $data): SchoolDocument
    {
        unset($data['school_id']);

        $document = $school->documents()->create($data);

        return $document->load('school');
    }

    public function find(SchoolDocument $document): SchoolDocument
    {
        return $document->load('school');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SchoolDocument $document, array $data): SchoolDocument
    {
        unset($data['school_id']);

        if ($data !== []) {
            $document->update($data);
        }

        return $document->fresh()->load('school');
    }

    public function delete(SchoolDocument $document): void
    {
        $document->delete();
    }
}
