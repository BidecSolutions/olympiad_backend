<?php

namespace App\Http\Controllers\Api;

use App\Enums\ParticipationTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\CompetitionCategory;
use App\Models\CompetitionCategoryParticipation;
use App\Services\CompetitionCategoryParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionCategoryParticipationController extends Controller
{
    public function __construct(
        private CompetitionCategoryParticipationService $competitionCategoryParticipationService,
    ) {}

    public function index(CompetitionCategory $competitionCategory): JsonResponse
    {
        $participations = $this->competitionCategoryParticipationService->list($competitionCategory);

        return response()->json([
            'status' => true,
            'message' => 'Competition category participations retrieved successfully.',
            'data' => $participations,
        ]);
    }

    public function store(Request $request, CompetitionCategory $competitionCategory): JsonResponse
    {
        $payloadData = $request->validate(
            $this->competitionCategoryParticipationPayloadRules($competitionCategory),
        );

        $participation = $this->competitionCategoryParticipationService->create(
            $competitionCategory,
            $payloadData,
        );

        return response()->json([
            'status' => true,
            'message' => 'Competition category participation created successfully.',
            'data' => $participation,
        ], 201);
    }

    public function show(
        CompetitionCategory $competitionCategory,
        CompetitionCategoryParticipation $participation,
    ): JsonResponse {
        $this->ensureParticipationBelongsToCategory($competitionCategory, $participation);

        return response()->json([
            'status' => true,
            'message' => 'Competition category participation retrieved successfully.',
            'data' => $this->competitionCategoryParticipationService->find($participation),
        ]);
    }

    public function update(
        Request $request,
        CompetitionCategory $competitionCategory,
        CompetitionCategoryParticipation $participation,
    ): JsonResponse {
        $this->ensureParticipationBelongsToCategory($competitionCategory, $participation);

        $payloadData = $request->validate(
            $this->competitionCategoryParticipationPayloadRules($competitionCategory, $participation),
        );

        $participation = $this->competitionCategoryParticipationService->update($participation, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition category participation updated successfully.',
            'data' => $participation,
        ]);
    }

    public function destroy(
        CompetitionCategory $competitionCategory,
        CompetitionCategoryParticipation $participation,
    ): JsonResponse {
        $this->ensureParticipationBelongsToCategory($competitionCategory, $participation);

        $this->competitionCategoryParticipationService->delete($participation);

        return response()->json([
            'status' => true,
            'message' => 'Competition category participation deleted successfully.',
        ]);
    }

    private function ensureParticipationBelongsToCategory(
        CompetitionCategory $competitionCategory,
        CompetitionCategoryParticipation $participation,
    ): void {
        abort_unless($participation->competition_category_id === $competitionCategory->id, 404);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function competitionCategoryParticipationPayloadRules(
        CompetitionCategory $competitionCategory,
        ?CompetitionCategoryParticipation $participation = null,
    ): array {
        $participationId = $participation?->id;

        return [
            'participation_type' => [
                $participationId ? 'sometimes' : 'required',
                'required',
                Rule::enum(ParticipationTypeEnum::class),
                Rule::unique('competition_category_participations', 'participation_type')
                    ->where('competition_category_id', $competitionCategory->id)
                    ->ignore($participationId),
            ],
        ];
    }
}
