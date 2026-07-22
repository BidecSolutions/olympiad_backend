<?php

namespace App\Services;

use App\Models\School;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeamService
{
    public function __construct(
        private TeamMemberService $memberService,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Team>
     */
    public function list(School $school, int $perPage = 15): LengthAwarePaginator
    {
        return $school->teams()
            ->with(['school', 'members.student'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(School $school, array $data): Team
    {
        $members = $data['members'] ?? null;
        unset($data['members'], $data['school_id']);

        if (is_array($members)) {
            return DB::transaction(fn (): Team => $this->persistTeam(
                school: $school,
                team: null,
                data: $data,
                members: $members,
            ));
        }

        $team = $school->teams()->create($data);

        return $team->load(['school', 'members.student']);
    }

    public function find(Team $team): Team
    {
        return $team->load(['school', 'members.student']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Team $team, array $data): Team
    {
        $members = array_key_exists('members', $data) ? $data['members'] : null;
        unset($data['members'], $data['school_id']);

        $hasRelatedChanges = is_array($members);
        $hasTeamChanges = $data !== [];

        if ($hasRelatedChanges && $hasTeamChanges) {
            return DB::transaction(fn (): Team => $this->persistTeam(
                school: $team->school,
                team: $team,
                data: $data,
                members: $members,
            ));
        }

        if ($hasRelatedChanges) {
            return DB::transaction(fn (): Team => $this->persistTeam(
                school: $team->school,
                team: $team,
                data: [],
                members: $members,
            ));
        }

        if ($hasTeamChanges) {
            $team->update($data);
        }

        return $team->fresh()->load(['school', 'members.student']);
    }

    public function delete(Team $team): void
    {
        $team->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array<string, mixed>>|null  $members
     */
    private function persistTeam(
        School $school,
        ?Team $team,
        array $data,
        ?array $members,
    ): Team {
        if ($team === null) {
            $team = $school->teams()->create($data);
        } elseif ($data !== []) {
            $team->update($data);
        }

        if (is_array($members)) {
            $this->memberService->syncForTeam($team, $members);
        }

        return $team->fresh()->load(['school', 'members.student']);
    }
}
