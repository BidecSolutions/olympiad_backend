<?php

namespace App\Models;

use Database\Factories\CompetitionCategoryRuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'competition_category_id',
    'max_teams',
    'min_team_members',
    'max_team_members',
    'max_participants',
])]
class CompetitionCategoryRule extends Model
{
    /** @use HasFactory<CompetitionCategoryRuleFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_teams' => 'integer',
            'min_team_members' => 'integer',
            'max_team_members' => 'integer',
            'max_participants' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<CompetitionCategory, $this>
     */
    public function competitionCategory(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class);
    }
}
