<?php

namespace App\Models;

use App\Enums\ParticipationTypeStatusEnum;
use Database\Factories\ParticipationTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'status',
])]
class ParticipationType extends Model
{
    /** @use HasFactory<ParticipationTypeFactory> */
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
            'status' => ParticipationTypeStatusEnum::class,
        ];
    }

    /**
     * @return HasMany<CompetitionCategoryParticipation, $this>
     */
    public function categoryParticipations(): HasMany
    {
        return $this->hasMany(CompetitionCategoryParticipation::class);
    }
}
