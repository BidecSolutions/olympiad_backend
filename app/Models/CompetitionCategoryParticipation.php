<?php

namespace App\Models;

use Database\Factories\CompetitionCategoryParticipationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'competition_category_id',
    'participation_type_id',
])]
class CompetitionCategoryParticipation extends Model
{
    /** @use HasFactory<CompetitionCategoryParticipationFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<CompetitionCategory, $this>
     */
    public function competitionCategory(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class);
    }

    /**
     * @return BelongsTo<ParticipationType, $this>
     */
    public function participationType(): BelongsTo
    {
        return $this->belongsTo(ParticipationType::class);
    }

    /**
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
