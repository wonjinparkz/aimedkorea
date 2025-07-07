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
            $table->string('background_type')->default('image')->after('background_image')->comment('image or video');
            $table->string('background_video')->nullable()->after('background_type');
            $table->string('video_poster')->nullable()->after('background_video')->comment('video poster image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_video', 'video_poster']);
        });
    }
};