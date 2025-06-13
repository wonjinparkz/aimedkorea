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
        Schema::table('heroes', function (Blueprint $table) {
            $table->json('hero_settings')->nullable()->after('background_image');
            $table->string('background_video')->nullable()->after('background_image');
            $table->enum('background_type', ['image', 'video'])->default('image')->after('background_video');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropColumn(['hero_settings', 'background_video', 'background_type']);
        });
    }
};
