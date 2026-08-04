<?php

namespace App\Http\Controllers\Api;

use App\Enums\SchoolDocumentStatusEnum;
use App\Enums\SchoolDocumentTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolDocument;
use App\Services\SchoolDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolDocumentController extends Controller
{
    public function __construct(private SchoolDocumentService $schoolDocumentService) {}

    public function index(School $school): JsonResponse
    {
        $documents = $this->schoolDocumentService->list($school);

        return response()->json([
            'status' => true,
            'message' => 'School documents retrieved successfully.',
            'data' => $documents,
        ]);
    }

    public function store(Request $request, School $school): JsonResponse
    {
        $payloadData = $request->validate($this->documentPayloadRules());

        $document = $this->schoolDocumentService->create($school, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'School document created successfully.',
            'data' => $document,
        ], 201);
    }

    public function show(School $school, SchoolDocument $document): JsonResponse
    {
        $this->ensureDocumentBelongsToSchool($school, $document);

        return response()->json([
            'status' => true,
            'message' => 'School document retrieved successfully.',
            'data' => $this->schoolDocumentService->find($document),
        ]);
    }

    public function update(Request $request, School $school, SchoolDocument $document): JsonResponse
    {
        $this->ensureDocumentBelongsToSchool($school, $document);

        $payloadData = $request->validate($this->documentPayloadRules($document));

        $document = $this->schoolDocumentService->update($document, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'School document updated successfully.',
            'data' => $document,
        ]);
    }

    public function destroy(School $school, SchoolDocument $document): JsonResponse
    {
        $this->ensureDocumentBelongsToSchool($school, $document);

        $this->schoolDocumentService->delete($document);

        return response()->json([
            'status' => true,
            'message' => 'School document deleted successfully.',
        ]);
    }

    private function ensureDocumentBelongsToSchool(School $school, SchoolDocument $document): void
    {
        abort_unless($document->school_id === $school->id, 404);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function documentPayloadRules(?SchoolDocument $document = null): array
    {
        $documentId = $document?->id;

        return [
            'document_type' => $documentId
                ? ['sometimes', 'required', Rule::enum(SchoolDocumentTypeEnum::class)]
                : ['required', Rule::enum(SchoolDocumentTypeEnum::class)],
            'file_path' => $documentId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(SchoolDocumentStatusEnum::class)],
        ];
    }
}
