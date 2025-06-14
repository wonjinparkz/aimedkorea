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
        Schema::create('project_options', function (Blueprint $table) {
            $table->id();
            $table->string('option_name', 191)->unique();
            $table->longText('option_value')->nullable();
            $table->string('autoload', 20)->default('yes');
            $table->timestamps();
            
            // 인덱스 추가
            $table->index('option_name');
            $table->index('autoload');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_options');
    }
};
