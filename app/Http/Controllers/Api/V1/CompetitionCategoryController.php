<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionCategory;
use App\Services\CompetitionCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionCategoryController extends Controller
{
    public function __construct(private CompetitionCategoryService $competitionCategoryService) {}

    public function index(Competition $competition): JsonResponse
    {
        $competitionCategories = $this->competitionCategoryService->list($competition);

        return response()->json([
            'status' => true,
            'message' => 'Competition categories retrieved successfully.',
            'data' => $competitionCategories,
        ]);
    }

    public function store(Request $request, Competition $competition): JsonResponse
    {
        $payloadData = $request->validate($this->competitionCategoryPayloadRules());

        $category = $this->competitionCategoryService->create($competition, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function show(Competition $competition, CompetitionCategory $competitionCategory): JsonResponse
    {
        $this->ensureCategoryBelongsToCompetition($competition, $competitionCategory);

        return response()->json([
            'status' => true,
            'message' => 'Competition category retrieved successfully.',
            'data' => $this->competitionCategoryService->find($competitionCategory),
        ]);
    }

    public function update(
        Request $request,
        Competition $competition,
        CompetitionCategory $competitionCategory,
    ): JsonResponse {
        $this->ensureCategoryBelongsToCompetition($competition, $competitionCategory);

        $payloadData = $request->validate($this->competitionCategoryPayloadRules($competitionCategory));

        $competitionCategory = $this->competitionCategoryService->update($competitionCategory, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Competition category updated successfully.',
            'data' => $competitionCategory,
        ]);
    }

    public function destroy(Competition $competition, CompetitionCategory $competitionCategory): JsonResponse
    {
        $this->ensureCategoryBelongsToCompetition($competition, $competitionCategory);

        $this->competitionCategoryService->delete($competitionCategory);

        return response()->json([
            'status' => true,
            'message' => 'Competition category deleted successfully.',
        ]);
    }

    private function ensureCategoryBelongsToCompetition(
        Competition $competition,
        CompetitionCategory $competitionCategory,
    ): void {
        abort_unless($competitionCategory->competition_id === $competition->id, 404);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function competitionCategoryPayloadRules(?CompetitionCategory $competitionCategory = null): array
    {
        $competitionCategoryId = $competitionCategory?->id;

        return [
            'name' => $competitionCategoryId
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(CompetitionCategoryStatusEnum::class)],
        ];
    }
}
