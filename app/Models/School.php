<?php

namespace App\Models;

use App\Enums\SchoolStatusEnum;
use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'school_code',
    'name',
    'registration_no',
    'email',
    'phone',
    'address',
    'city',
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
        ];
    }

    /**
     * @return HasMany<SchoolDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SchoolDocument::class);
    }

    /**
     * @return HasMany<SchoolAdmin, $this>
     */
    public function admins(): HasMany
    {
        return $this->hasMany(SchoolAdmin::class);
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
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
