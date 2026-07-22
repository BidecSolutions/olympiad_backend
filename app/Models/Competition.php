<?php

namespace App\Models;

use App\Enums\CompetitionStatusEnum;
use App\Enums\ScoringTypeEnum;
use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'event_id',
    'competition_type_id',
    'name',
    'description',
    'scoring_type',
    'status',
])]
class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'scoring_type' => 'points',
        'status' => 'draft',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scoring_type' => ScoringTypeEnum::class,
            'status' => CompetitionStatusEnum::class,
        ];
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<CompetitionType, $this>
     */
    public function competitionType(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class);
    }

    /**
     * @return HasMany<CompetitionCategory, $this>
     */
    public function competitionCategories(): HasMany
    {
        return $this->hasMany(CompetitionCategory::class);
    }
}
