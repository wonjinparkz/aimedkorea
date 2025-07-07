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
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->integer('recovery_score')->nullable()->after('total_score');
            $table->json('recovery_data')->nullable()->after('recovery_score');
            $table->timestamp('last_viewed_at')->nullable()->after('recovery_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropColumn(['recovery_score', 'recovery_data', 'last_viewed_at']);
        });
    }
};