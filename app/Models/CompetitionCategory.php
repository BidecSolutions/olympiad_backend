<?php

namespace App\Models;

use App\Enums\CompetitionCategoryStatusEnum;
use Database\Factories\CompetitionCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'competition_id',
    'name',
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
        'status' => 'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
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
     * @return HasMany<CompetitionCategoryParticipation, $this>
     */
    public function participations(): HasMany
    {
        return $this->hasMany(CompetitionCategoryParticipation::class);
    }
}
