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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_mega_menu')->default(false);
            $table->json('mega_menu_content')->nullable(); // For storing structured mega menu data
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('target')->default('_self'); // _self, _blank, etc.
            $table->string('css_class')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
