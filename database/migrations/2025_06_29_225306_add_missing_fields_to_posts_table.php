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
            $table->string('slug')->nullable()->after('title');
            $table->json('content_sections')->nullable()->after('content');
            $table->json('related_articles')->nullable()->after('content_sections');
            $table->boolean('is_published')->default(true)->after('featured');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->foreignId('author_id')->nullable()->constrained('users')->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn([
                'slug',
                'content_sections',
                'related_articles',
                'is_published',
                'published_at',
                'author_id'
            ]);
        });
    }
};