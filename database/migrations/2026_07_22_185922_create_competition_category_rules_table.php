<?php

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
        Schema::create('competition_category_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_category_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('max_teams')->nullable();
            $table->unsignedInteger('min_team_members')->nullable();
            $table->unsignedInteger('max_team_members')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_category_rules');
    }
};
