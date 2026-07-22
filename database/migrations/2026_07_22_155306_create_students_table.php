<?php

use App\Enums\StudentStatusEnum;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('student_code')->nullable()->unique();
            $table->string('name');
            $table->string('father_name');
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('class');
            $table->string('section')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default(StudentStatusEnum::Active->value)->index();
            $table->text('blacklist_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
