<?php

namespace Database\Factories;

use App\Enums\SchoolDocumentStatusEnum;
use App\Enums\SchoolDocumentTypeEnum;
use App\Models\School;
use App\Models\SchoolDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolDocument>
 */
class SchoolDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'document_type' => fake()->randomElement(SchoolDocumentTypeEnum::cases()),
            'file_path' => 'school-documents/'.fake()->uuid().'.pdf',
            'status' => SchoolDocumentStatusEnum::Pending,
        ];
    }
}
