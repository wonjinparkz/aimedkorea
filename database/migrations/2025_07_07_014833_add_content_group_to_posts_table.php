<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('content_group_id')->nullable()->after('base_slug');
            $table->boolean('is_primary')->default(false)->after('content_group_id');
            $table->index(['content_group_id', 'language']);
            $table->index(['is_primary', 'type', 'is_published']);
        });
        
        // Generate content_group_id for existing posts
        DB::statement("
            UPDATE posts 
            SET content_group_id = CONCAT(type, '_', COALESCE(base_slug, slug, id))
            WHERE content_group_id IS NULL
        ");
        
        // Set primary posts (Korean posts or first post in each group)
        DB::statement("
            UPDATE posts p1
            SET is_primary = 1
            WHERE p1.language = 'kor'
            AND p1.content_group_id IS NOT NULL
        ");
        
        // For groups without Korean posts, set the first post as primary
        DB::statement("
            UPDATE posts p1
            SET is_primary = 1
            WHERE p1.id = (
                SELECT MIN(p2.id) 
                FROM (SELECT * FROM posts) p2 
                WHERE p2.content_group_id = p1.content_group_id
                AND p1.content_group_id NOT IN (
                    SELECT DISTINCT content_group_id 
                    FROM (SELECT * FROM posts) p3 
                    WHERE p3.language = 'kor' AND p3.content_group_id IS NOT NULL
                )
            )
            AND p1.is_primary = 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['content_group_id', 'language']);
            $table->dropIndex(['is_primary', 'type', 'is_published']);
            $table->dropColumn(['content_group_id', 'is_primary']);
        });
    }
};
