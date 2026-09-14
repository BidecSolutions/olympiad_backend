<?php

namespace App\Models;

use App\Enums\RegistrationStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'school_id',
    'student_id',
    'competition_id',
    'competition_category_id',
    'registration_code',
    'shirt_number',
    'remarks',
    'status',
    'psid',
    'payment_status',
    'amount',
])]
class StudentRegistration extends Model
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'submitted',
        'payment_status' => 'Pending Payment',
        'amount' => 'PKR 5,000',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RegistrationStatusEnum::class,
        ];
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * @return BelongsTo<CompetitionCategory, $this>
     */
    public function competitionCategory(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class);
    }
}
