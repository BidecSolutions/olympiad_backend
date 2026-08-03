<?php

use App\Enums\CompetitionStatusEnum;
use App\Enums\CompetitionTypeEnum;
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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('competition_type')->default(CompetitionTypeEnum::Academic->value)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default(CompetitionStatusEnum::Draft->value)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
