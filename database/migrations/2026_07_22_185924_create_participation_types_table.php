<?php

use App\Enums\ParticipationTypeStatusEnum;
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
        Schema::create('participation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('status')->default(ParticipationTypeStatusEnum::Active->value)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participation_types');
    }
};
