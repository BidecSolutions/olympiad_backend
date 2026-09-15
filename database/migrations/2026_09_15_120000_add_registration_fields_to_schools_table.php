<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('school_type')->nullable()->after('registration_no');
            $table->unsignedSmallInteger('establishment_year')->nullable()->after('school_type');
            $table->string('website')->nullable()->after('establishment_year');
            $table->text('about')->nullable()->after('website');
            $table->string('contact_designation')->nullable()->after('city');
            $table->string('alternate_phone')->nullable()->after('phone');
            $table->string('state')->nullable()->after('alternate_phone');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            $table->unsignedInteger('requested_quota')->nullable()->after('postal_code');
            $table->unsignedInteger('approved_quota')->nullable()->after('requested_quota');
            $table->json('interested_competitions')->nullable()->after('approved_quota');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'school_type',
                'establishment_year',
                'website',
                'about',
                'contact_designation',
                'alternate_phone',
                'state',
                'country',
                'postal_code',
                'requested_quota',
                'approved_quota',
                'interested_competitions',
            ]);
        });
    }
};
