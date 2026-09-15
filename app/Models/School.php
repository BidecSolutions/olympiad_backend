<?php

namespace App\Models;

use App\Enums\SchoolStatusEnum;
use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'school_code',
    'name',
    'registration_no',
    'school_type',
    'establishment_year',
    'website',
    'about',
    'email',
    'phone',
    'alternate_phone',
    'address',
    'city',
    'state',
    'country',
    'postal_code',
    'contact_designation',
    'requested_quota',
    'approved_quota',
    'interested_competitions',
    'status',
])]
class School extends Model
{
    /** @use HasFactory<SchoolFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SchoolStatusEnum::class,
            'establishment_year' => 'integer',
            'requested_quota' => 'integer',
            'approved_quota' => 'integer',
            'interested_competitions' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SchoolDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SchoolDocument::class);
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * @return HasMany<Team, $this>
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @return HasMany<StudentRegistration, $this>
     */
    public function studentRegistrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class);
    }
}
