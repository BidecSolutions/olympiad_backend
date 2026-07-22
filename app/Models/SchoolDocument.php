<?php

namespace App\Models;

use App\Enums\SchoolDocumentStatusEnum;
use App\Enums\SchoolDocumentTypeEnum;
use Database\Factories\SchoolDocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'school_id',
    'document_type',
    'file_path',
    'status',
])]
class SchoolDocument extends Model
{
    /** @use HasFactory<SchoolDocumentFactory> */
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
            'document_type' => SchoolDocumentTypeEnum::class,
            'status' => SchoolDocumentStatusEnum::class,
        ];
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
