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
            $table->json('authors')->nullable()->after('author_id')->comment('논문 저자들 (JSON 배열)');
            $table->string('publisher', 255)->nullable()->after('authors')->comment('발행기관');
            $table->text('link')->nullable()->after('publisher')->comment('논문 링크');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['authors', 'publisher', 'link']);
        });
    }
};