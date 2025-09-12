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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('module'); // 모듈 (예: posts, users, settings)
            $table->string('action'); // 액션 (예: view, create, edit, delete)
            $table->string('display_name_ko')->nullable();
            $table->string('display_name_en')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['module', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
