<?php

namespace App\Services;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamMemberService
{
    /**
     * @return LengthAwarePaginator<int, TeamMember>
     */
    public function list(Team $team, int $perPage = 15): LengthAwarePaginator
    {
        return $team->members()
            ->with(['team', 'student'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Team $team, array $data): TeamMember
    {
        unset($data['team_id']);

        $member = $team->members()->create($data);

        return $member->load(['team', 'student']);
    }

    public function find(TeamMember $teamMember): TeamMember
    {
        return $teamMember->load(['team', 'student']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TeamMember $teamMember, array $data): TeamMember
    {
        unset($data['team_id']);

        if ($data !== []) {
            $teamMember->update($data);
        }

        return $teamMember->fresh()->load(['team', 'student']);
    }

    public function delete(TeamMember $teamMember): void
    {
        $teamMember->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $members
     */
    public function syncForTeam(Team $team, array $members): void
    {
        $memberIds = [];

        foreach ($members as $memberData) {
            $attributes = ['student_id' => $memberData['student_id']];

            if (array_key_exists('shirt_number', $memberData)) {
                $attributes['shirt_number'] = $memberData['shirt_number'];
            }

            if (! empty($memberData['id'])) {
                $member = $team->members()->findOrFail($memberData['id']);
                $member->update($attributes);
                $memberIds[] = $member->id;
            } else {
                $member = $team->members()->create($attributes);
                $memberIds[] = $member->id;
            }
        }

        if ($memberIds === []) {
            $team->members()->delete();

            return;
        }

        $team->members()->whereNotIn('id', $memberIds)->delete();
    }
}
