<?php

use App\Enums\RegistrationStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('competition_category_participation_id')
                ->constrained('competition_category_participations')
                ->cascadeOnDelete();
            $table->string('status')->default(RegistrationStatusEnum::Pending->value)->index();
            $table->timestamps();

            $table->unique(
                ['competition_category_participation_id', 'student_id'],
                'registration_student_participation_unique',
            );
            $table->unique(
                ['competition_category_participation_id', 'team_id'],
                'registration_team_participation_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
