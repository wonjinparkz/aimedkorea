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
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('language', 3)->default('kor')->after('id');
            $table->uuid('content_group_id')->nullable()->after('language');
            $table->boolean('is_primary')->default(true)->after('content_group_id');
            
            // 인덱스 추가
            $table->index('language');
            $table->index('content_group_id');
            $table->index(['language', 'content_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropIndex(['language', 'content_group_id']);
            $table->dropIndex(['content_group_id']);
            $table->dropIndex(['language']);
            
            $table->dropColumn(['language', 'content_group_id', 'is_primary']);
        });
    }
};
