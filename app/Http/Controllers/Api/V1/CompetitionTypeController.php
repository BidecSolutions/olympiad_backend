<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CompetitionType;
use App\Services\CompetitionTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionTypeController extends Controller
{
    public function __construct(private CompetitionTypeService $competitionTypeService) {}

    public function index(): JsonResponse
    {
        $competitionTypes = $this->competitionTypeService->list();

        return response()->json([
            'status' => true,
            'message' => 'Competition types retrieved successfully.',
            'data' => $competitionTypes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->competitionTypePayloadRules());

        $competitionType = $this->competitionTypeService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition type created successfully.',
            'data' => $competitionType,
        ], 201);
    }

    public function show(CompetitionType $competitionType): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Competition type retrieved successfully.',
            'data' => $this->competitionTypeService->find($competitionType),
        ]);
    }

    public function update(Request $request, CompetitionType $competitionType): JsonResponse
    {
        $payloadData = $request->validate($this->competitionTypePayloadRules($competitionType));

        $competitionType = $this->competitionTypeService->update($competitionType, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition type updated successfully.',
            'data' => $competitionType,
        ]);
    }

    public function destroy(CompetitionType $competitionType): JsonResponse
    {
        $this->competitionTypeService->delete($competitionType);

        return response()->json([
            'status' => true,
            'message' => 'Competition type deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function competitionTypePayloadRules(?CompetitionType $competitionType = null): array
    {
        $competitionTypeId = $competitionType?->id;

        return [
            'name' => [
                $competitionTypeId ? 'sometimes' : 'required',
                'required',
                'string',
                'max:255',
                $competitionTypeId
                    ? Rule::unique('competition_types', 'name')->ignore($competitionTypeId)
                    : Rule::unique('competition_types', 'name'),
            ],
        ];
    }
}
