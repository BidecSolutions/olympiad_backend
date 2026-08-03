<?php

use App\Enums\ParticipationTypeEnum;
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
        Schema::create('competition_category_participations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('competition_category_id');
            $table->string('participation_type')->default(ParticipationTypeEnum::Individual->value)->index();

            $table->foreign('competition_category_id', 'ccp_category_fk')
                ->references('id')
                ->on('competition_categories')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['competition_category_id', 'participation_type'],
                'category_participation_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_category_participations');
    }
};
