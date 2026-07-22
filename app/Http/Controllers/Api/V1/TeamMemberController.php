<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Team;
use App\Models\TeamMember;
use App\Services\TeamMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
    public function __construct(private TeamMemberService $teamMemberService) {}

    public function index(School $school, Team $team): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);

        $members = $this->teamMemberService->list($team);

        return response()->json([
            'status' => true,
            'message' => 'Team members retrieved successfully.',
            'data' => $members,
        ]);
    }

    public function store(Request $request, School $school, Team $team): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);

        $payloadData = $request->validate($this->teamMemberPayloadRules($school, $team));

        $member = $this->teamMemberService->create($team, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Team member created successfully.',
            'data' => $member,
        ], 201);
    }

    public function show(School $school, Team $team, TeamMember $teamMember): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);
        $this->ensureMemberBelongsToTeam($team, $teamMember);

        return response()->json([
            'status' => true,
            'message' => 'Team member retrieved successfully.',
            'data' => $this->teamMemberService->find($teamMember),
        ]);
    }

    public function update(
        Request $request,
        School $school,
        Team $team,
        TeamMember $teamMember,
    ): JsonResponse {
        $this->ensureTeamBelongsToSchool($school, $team);
        $this->ensureMemberBelongsToTeam($team, $teamMember);

        $payloadData = $request->validate($this->teamMemberPayloadRules($school, $team, $teamMember));

        $teamMember = $this->teamMemberService->update($teamMember, $payloadData);

        return response()->json([
            'status' => true,
            'message' => 'Team member updated successfully.',
            'data' => $teamMember,
        ]);
    }

    public function destroy(School $school, Team $team, TeamMember $teamMember): JsonResponse
    {
        $this->ensureTeamBelongsToSchool($school, $team);
        $this->ensureMemberBelongsToTeam($team, $teamMember);

        $this->teamMemberService->delete($teamMember);

        return response()->json([
            'status' => true,
            'message' => 'Team member deleted successfully.',
        ]);
    }

    private function ensureTeamBelongsToSchool(School $school, Team $team): void
    {
        abort_unless($team->school_id === $school->id, 404);
    }

    private function ensureMemberBelongsToTeam(Team $team, TeamMember $teamMember): void
    {
        abort_unless($teamMember->team_id === $team->id, 404);
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function teamMemberPayloadRules(
        School $school,
        Team $team,
        ?TeamMember $teamMember = null,
    ): array {
        $teamMemberId = $teamMember?->id;

        return [
            'student_id' => [
                $teamMemberId ? 'sometimes' : 'required',
                'required',
                'integer',
                Rule::exists('students', 'id')->where('school_id', $school->id),
                Rule::unique('team_members', 'student_id')
                    ->where('team_id', $team->id)
                    ->ignore($teamMemberId),
            ],
        ];
    }
}
