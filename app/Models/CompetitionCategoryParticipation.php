<?php

namespace App\Models;

use App\Enums\ParticipationTypeEnum;
use Database\Factories\CompetitionCategoryParticipationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'competition_category_id',
    'participation_type',
])]
class CompetitionCategoryParticipation extends Model
{
    /** @use HasFactory<CompetitionCategoryParticipationFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'participation_type' => 'individual',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'participation_type' => ParticipationTypeEnum::class,
        ];
    }

    /**
     * @return BelongsTo<CompetitionCategory, $this>
     */
    public function competitionCategory(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class);
    }

    /**
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
