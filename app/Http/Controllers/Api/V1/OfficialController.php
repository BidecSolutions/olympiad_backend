<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OfficialStatusEnum;
use App\Enums\OfficialTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Official;
use App\Services\OfficialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficialController extends Controller
{
    public function __construct(private OfficialService $officialService) {}

    public function index(): JsonResponse
    {
        $officials = $this->officialService->list();

        return response()->json([
            'status' => true,
            'message' => 'Officials retrieved successfully.',
            'data' => $officials,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->officialPayloadRules());

        $official = $this->officialService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Official created successfully.',
            'data' => $official,
        ], 201);
    }

    public function show(Official $official): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Official retrieved successfully.',
            'data' => $this->officialService->find($official),
        ]);
    }

    public function update(Request $request, Official $official): JsonResponse
    {
        $payloadData = $request->validate($this->officialPayloadRules($official));

        $official = $this->officialService->update($official, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Official updated successfully.',
            'data' => $official,
        ]);
    }

    public function destroy(Official $official): JsonResponse
    {
        $this->officialService->delete($official);

        return response()->json([
            'status' => true,
            'message' => 'Official deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function officialPayloadRules(?Official $official = null): array
    {
        $officialId = $official?->id;

        return [
            'user_id' => [
                $officialId ? 'sometimes' : 'required',
                'required',
                'integer',
                Rule::exists('users', 'id'),
                $officialId
                    ? Rule::unique('officials', 'user_id')->ignore($officialId)
                    : Rule::unique('officials', 'user_id'),
            ],
            'type' => [
                $officialId ? 'sometimes' : 'required',
                'required',
                Rule::enum(OfficialTypeEnum::class),
            ],
            'status' => ['nullable', Rule::enum(OfficialStatusEnum::class)],
        ];
    }
}
