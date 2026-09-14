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
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('competition_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('competition_category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('registration_code')->unique();
            $table->string('shirt_number')->nullable();
            $table->text('remarks')->nullable();

            $table->string('status')
                ->default(RegistrationStatusEnum::Submitted->value)
                ->index();

            $table->string('psid')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('amount')->nullable();

            $table->timestamps();

            $table->unique([
                'school_id',
                'student_id',
                'competition_category_id',
            ], 'student_registrations_unique_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
