<?php

namespace App\Services;

use App\Enums\SchoolDocumentStatusEnum;
use App\Models\School;
use App\Models\SchoolDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SchoolDocumentService
{
    /**
     * @return LengthAwarePaginator<int, SchoolDocument>
     */
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return SchoolDocument::query()
            ->with('school')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, SchoolDocument>
     */
    public function listBySchool(School $school): Collection
    {
        return $school->documents()->latest()->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): SchoolDocument
    {
        $document = SchoolDocument::create($data);

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
        $document->update($data);

        return $document->fresh()->load('school');
    }

    public function delete(SchoolDocument $document): void
    {
        $document->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $documents
     */
    public function syncForSchool(School $school, array $documents): void
    {
        $documentIds = [];

        foreach ($documents as $documentData) {
            if (! empty($documentData['id'])) {
                $document = $school->documents()->findOrFail($documentData['id']);
                $document->update([
                    'document_type' => $documentData['document_type'],
                    'file_path' => $documentData['file_path'],
                    'status' => $documentData['status'] ?? $document->status,
                ]);
                $documentIds[] = $document->id;
            } else {
                $document = $school->documents()->create([
                    'document_type' => $documentData['document_type'],
                    'file_path' => $documentData['file_path'],
                    'status' => $documentData['status'] ?? SchoolDocumentStatusEnum::Pending,
                ]);
                $documentIds[] = $document->id;
            }
        }

        if ($documentIds === []) {
            $school->documents()->delete();

            return;
        }

        $school->documents()->whereNotIn('id', $documentIds)->delete();
    }
}
