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
        // 타임라인 메인 테이블
        Schema::create('survey_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('initial_response_id')->constrained('survey_responses')->onDelete('cascade');
            $table->date('start_date'); // 타임라인 시작일
            $table->date('end_date'); // 12주 후 종료일
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // 인덱스
            $table->index(['user_id', 'status']);
            $table->index(['survey_id', 'status']);
        });
        
        // 체크포인트 테이블
        Schema::create('timeline_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timeline_id')->constrained('survey_timelines')->onDelete('cascade');
            $table->integer('week_number'); // 0, 2, 4, 6, 8, 10, 12
            $table->date('scheduled_date'); // 예정일
            $table->date('completed_date')->nullable(); // 실제 완료일
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'missed'])->default('scheduled');
            $table->foreignId('response_id')->nullable()->constrained('survey_responses')->onDelete('set null');
            $table->integer('score')->nullable(); // 해당 주차 점수
            $table->json('category_scores')->nullable(); // 카테고리별 점수
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // 인덱스
            $table->index(['timeline_id', 'week_number']);
            $table->index(['scheduled_date', 'status']);
            $table->unique(['timeline_id', 'week_number']);
        });
        
        // 타임라인 알림 설정 (선택사항)
        Schema::create('timeline_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timeline_id')->constrained('survey_timelines')->onDelete('cascade');
            $table->foreignId('checkpoint_id')->constrained('timeline_checkpoints')->onDelete('cascade');
            $table->enum('type', ['reminder', 'missed', 'completed']);
            $table->datetime('scheduled_at');
            $table->datetime('sent_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
            
            $table->index(['scheduled_at', 'is_sent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeline_notifications');
        Schema::dropIfExists('timeline_checkpoints');
        Schema::dropIfExists('survey_timelines');
    }
};