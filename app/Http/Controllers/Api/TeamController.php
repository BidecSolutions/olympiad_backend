<?php

namespace App\Http\Controllers\Api;

use App\Enums\TeamStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function __construct(private TeamService $teamService) {}

    public function index(School $school): JsonResponse
    {
        $teams = $this->teamService->list($school);

        return response()->json([
            'status' => true,
            'message' => 'Teams retrieved successfully.',
            'data' => $teams,
        ]);
    }

    public function store(Request $request, School $school): JsonResponse
    {
        $payloadData = $request->validate($this->teamPayloadRules($request, $school));

        $team = $this->teamService->create($school, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Team created successfully.',
            'data' => $team,
        ], 201);
    }

    public function show(School $school, Team $team): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);

        return response()->json([
            'status' => true,
            'message' => 'Team retrieved successfully.',
            'data' => $this->teamService->find($team),
        ]);
    }

    public function update(Request $request, School $school, Team $team): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);

        $payloadData = $request->validate($this->teamPayloadRules($request, $school, $team));

        $team = $this->teamService->update($team, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Team updated successfully.',
            'data' => $team,
        ]);
    }

    public function destroy(School $school, Team $team): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);

        $this->teamService->delete($team);

        return response()->json([
            'status' => true,
            'message' => 'Team deleted successfully.',
        ]);
    }

    private function ensureTeamBelongsToSchool(School $school, Team $team): void
    {
        abort_unless($team->school_id === $school->id, 404);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function teamPayloadRules(Request $request, School $school, ?Team $team = null): array
    {
        $teamId = $team?->id;
        $competitionCategoryId = $request->input(
            'competition_category_id',
            $team?->competition_category_id,
        );

        return [
            'competition_category_id' => [
                $teamId ? 'sometimes' : 'required',
                'required',
                'integer',
                Rule::exists('competition_categories', 'id'),
            ],
            'name' => [
                $teamId ? 'sometimes' : 'required',
                'required',
                'string',
                'max:255',
                Rule::unique('teams', 'name')
                    ->where('school_id', $school->id)
                    ->where('competition_category_id', $competitionCategoryId)
                    ->ignore($teamId),
            ],
            'status' => ['nullable', Rule::enum(TeamStatusEnum::class)],

            // Team Members
            'members' => ['nullable', 'array'],
            'members.*.id' => [
                'nullable',
                'integer',
                $teamId
                    ? Rule::exists('team_members', 'id')->where('team_id', $teamId)
                    : 'prohibited',
            ],
            'members.*.student_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('students', 'id')->where('school_id', $school->id),
            ],
            'members.*.shirt_number' => ['nullable', 'string', 'max:255', 'distinct'],
        ];
    }
}
