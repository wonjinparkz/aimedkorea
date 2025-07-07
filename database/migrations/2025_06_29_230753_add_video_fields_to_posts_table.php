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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('video_type')->nullable()->after('image')->comment('youtube or upload');
            $table->string('youtube_url')->nullable()->after('video_type');
            $table->string('video_file')->nullable()->after('youtube_url');
            $table->string('video_thumbnail')->nullable()->after('video_file');
            $table->integer('video_duration')->nullable()->after('video_thumbnail')->comment('duration in seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'video_type',
                'youtube_url',
                'video_file',
                'video_thumbnail',
                'video_duration'
            ]);
        });
    }
};