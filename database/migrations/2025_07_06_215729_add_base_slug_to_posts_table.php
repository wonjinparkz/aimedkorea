<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('base_slug')->nullable()->after('slug')->index();
        });

        // Update existing posts to have base_slug
        DB::table('posts')->get()->each(function ($post) {
            $baseSlug = $post->slug ?: Str::slug($post->title);
            DB::table('posts')
                ->where('id', $post->id)
                ->update(['base_slug' => $baseSlug]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('base_slug');
        });
    }
};
