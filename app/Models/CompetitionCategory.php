<?php

namespace App\Models;

use App\Enums\CompetitionCategoryStatusEnum;
use App\Enums\ParticipationTypeEnum;
use Database\Factories\CompetitionCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'competition_id',
    'name',
    'participation_type',
    'status',
])]
class CompetitionCategory extends Model
{
    /** @use HasFactory<CompetitionCategoryFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'participation_type' => 'individual',
        'status' => 'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'participation_type' => ParticipationTypeEnum::class,
            'status' => CompetitionCategoryStatusEnum::class,
        ];
    }

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * @return HasOne<CompetitionCategoryRule, $this>
     */
    public function rule(): HasOne
    {
        return $this->hasOne(CompetitionCategoryRule::class);
    }

    /**
     * @return HasMany<Team, $this>
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
