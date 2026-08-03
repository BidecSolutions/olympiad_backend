<?php

namespace App\Http\Controllers\Api;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Enums\CompetitionStatusEnum;
use App\Enums\CompetitionTypeEnum;
use App\Enums\ParticipationTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Services\CompetitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionController extends Controller
{
    public function __construct(private CompetitionService $competitionService) {}

    public function index(): JsonResponse
    {
        $competitions = $this->competitionService->list();

        return response()->json([
            'status' => true,
            'message' => 'Competitions retrieved successfully.',
            'data' => $competitions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payloadData = $request->validate($this->competitionPayloadRules());

        $competition = $this->competitionService->create($payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition created successfully.',
            'data' => $competition,
        ], 201);
    }

    public function show(Competition $competition): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Competition retrieved successfully.',
            'data' => $this->competitionService->find($competition),
        ]);
    }

    public function update(Request $request, Competition $competition): JsonResponse
    {
        $payloadData = $request->validate($this->competitionPayloadRules($competition));

        $competition = $this->competitionService->update($competition, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition updated successfully.',
            'data' => $competition,
        ]);
    }

    public function destroy(Competition $competition): JsonResponse
    {
        $this->competitionService->delete($competition);

        return response()->json([
            'status' => true,
            'message' => 'Competition deleted successfully.',
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function competitionPayloadRules(?Competition $competition = null): array
    {
        $competitionId = $competition?->id;

        return [
            'event_id' => $competitionId
                ? ['sometimes', 'required', 'integer', 'exists:events,id']
                : ['required', 'integer', 'exists:events,id'],
            'competition_type' => $competitionId
                ? ['sometimes', 'required', Rule::enum(CompetitionTypeEnum::class)]
                : ['required', Rule::enum(CompetitionTypeEnum::class)],
            'name' => $competitionId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(CompetitionStatusEnum::class)],

            // Competition Categories
            'competition_categories' => ['nullable', 'array'],
            'competition_categories.*.id' => [
                'nullable',
                'integer',
                $competitionId
                    ? Rule::exists('competition_categories', 'id')->where('competition_id', $competitionId)
                    : 'prohibited',
            ],
            'competition_categories.*.name' => ['required', 'string', 'max:255'],
            'competition_categories.*.status' => ['nullable', Rule::enum(CompetitionCategoryStatusEnum::class)],

            // Category Participations
            'competition_categories.*.participations' => ['nullable', 'array'],
            'competition_categories.*.participations.*.id' => [
                'nullable',
                'integer',
                $competitionId
                    ? 'exists:competition_category_participations,id'
                    : 'prohibited',
            ],
            'competition_categories.*.participations.*.participation_type' => [
                'required',
                Rule::enum(ParticipationTypeEnum::class),
            ],
        ];
    }
}
