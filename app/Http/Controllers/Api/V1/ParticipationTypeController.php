<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ParticipationTypeStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\ParticipationType;
use App\Services\ParticipationTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipationTypeController extends Controller
{
    public function __construct(private ParticipationTypeService $participationTypeService) {}

    public function index(): JsonResponse
    {
        $participationTypes = $this->participationTypeService->list();

        return response()->json([
            'status' => true,
            'message' => 'Participation types retrieved successfully.',
            'data' => $participationTypes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->participationTypePayloadRules());

        $participationType = $this->participationTypeService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Participation type created successfully.',
            'data' => $participationType,
        ], 201);
    }

    public function show(ParticipationType $participationType): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Participation type retrieved successfully.',
            'data' => $this->participationTypeService->find($participationType),
        ]);
    }

    public function update(Request $request, ParticipationType $participationType): JsonResponse
    {
        $payloadData = $request->validate($this->participationTypePayloadRules($participationType));

        $participationType = $this->participationTypeService->update($participationType, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Participation type updated successfully.',
            'data' => $participationType,
        ]);
    }

    public function destroy(ParticipationType $participationType): JsonResponse
    {
        $this->participationTypeService->delete($participationType);

        return response()->json([
            'status' => true,
            'message' => 'Participation type deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function participationTypePayloadRules(?ParticipationType $participationType = null): array
    {
        $participationTypeId = $participationType?->id;

        return [
            'name' => [
                $participationTypeId ? 'sometimes' : 'required',
                'required',
                'string',
                'max:255',
                $participationTypeId
                    ? Rule::unique('participation_types', 'name')->ignore($participationTypeId)
                    : Rule::unique('participation_types', 'name'),
            ],
            'status' => ['nullable', Rule::enum(ParticipationTypeStatusEnum::class)],
        ];
    }
}
