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
        // 기존 데이터 백업
        $surveys = DB::table('surveys')->get();
        
        // 새로운 JSON 필드 추가
        Schema::table('surveys', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('description_translations')->nullable()->after('description');
            $table->json('checklist_items_translations')->nullable()->after('checklist_items');
            $table->json('questions_translations')->nullable()->after('questions');
        });
        
        // 기존 데이터를 JSON 형식으로 마이그레이션
        $groupedSurveys = $surveys->groupBy('content_group_id');
        
        foreach ($groupedSurveys as $groupId => $group) {
            if (empty($groupId)) {
                // content_group_id가 없는 경우 각각 처리
                foreach ($group as $survey) {
                    DB::table('surveys')->where('id', $survey->id)->update([
                        'title_translations' => json_encode([$survey->language => $survey->title]),
                        'description_translations' => json_encode([$survey->language => $survey->description]),
                        'checklist_items_translations' => json_encode([$survey->language => json_decode($survey->checklist_items)]),
                        'questions_translations' => json_encode([$survey->language => json_decode($survey->questions)]),
                    ]);
                }
            } else {
                // 같은 그룹의 설문들을 하나로 합침
                $titleTranslations = [];
                $descriptionTranslations = [];
                $checklistTranslations = [];
                $questionsTranslations = [];
                $primaryId = null;
                
                foreach ($group as $survey) {
                    $titleTranslations[$survey->language] = $survey->title;
                    $descriptionTranslations[$survey->language] = $survey->description;
                    $checklistTranslations[$survey->language] = json_decode($survey->checklist_items, true);
                    $questionsTranslations[$survey->language] = json_decode($survey->questions, true);
                    
                    if ($survey->is_primary) {
                        $primaryId = $survey->id;
                    }
                }
                
                // Primary 설문 업데이트
                if ($primaryId) {
                    DB::table('surveys')->where('id', $primaryId)->update([
                        'title_translations' => json_encode($titleTranslations),
                        'description_translations' => json_encode($descriptionTranslations),
                        'checklist_items_translations' => json_encode($checklistTranslations),
                        'questions_translations' => json_encode($questionsTranslations),
                    ]);
                    
                    // 나머지 삭제
                    DB::table('surveys')
                        ->where('content_group_id', $groupId)
                        ->where('id', '!=', $primaryId)
                        ->delete();
                }
            }
        }
        
        // 기존 필드 제거
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropIndex(['language', 'content_group_id']);
            $table->dropIndex(['content_group_id']);
            $table->dropIndex(['language']);
            
            $table->dropColumn(['language', 'content_group_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 다국어 필드 복원
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('language', 3)->default('kor')->after('id');
            $table->uuid('content_group_id')->nullable()->after('language');
            $table->boolean('is_primary')->default(true)->after('content_group_id');
            
            $table->index('language');
            $table->index('content_group_id');
            $table->index(['language', 'content_group_id']);
        });
        
        // JSON 데이터를 개별 레코드로 복원
        $surveys = DB::table('surveys')->get();
        
        foreach ($surveys as $survey) {
            $titleTranslations = json_decode($survey->title_translations, true) ?? [];
            $descriptionTranslations = json_decode($survey->description_translations, true) ?? [];
            $checklistTranslations = json_decode($survey->checklist_items_translations, true) ?? [];
            $questionsTranslations = json_decode($survey->questions_translations, true) ?? [];
            
            if (count($titleTranslations) > 1) {
                $contentGroupId = \Illuminate\Support\Str::uuid();
                $isFirst = true;
                
                foreach ($titleTranslations as $lang => $title) {
                    if ($isFirst) {
                        // 기존 레코드 업데이트
                        DB::table('surveys')->where('id', $survey->id)->update([
                            'language' => $lang,
                            'content_group_id' => $contentGroupId,
                            'is_primary' => true,
                            'title' => $title,
                            'description' => $descriptionTranslations[$lang] ?? null,
                            'checklist_items' => json_encode($checklistTranslations[$lang] ?? []),
                            'questions' => json_encode($questionsTranslations[$lang] ?? []),
                        ]);
                        $isFirst = false;
                    } else {
                        // 새 레코드 생성
                        DB::table('surveys')->insert([
                            'language' => $lang,
                            'content_group_id' => $contentGroupId,
                            'is_primary' => false,
                            'title' => $title,
                            'description' => $descriptionTranslations[$lang] ?? null,
                            'checklist_items' => json_encode($checklistTranslations[$lang] ?? []),
                            'questions' => json_encode($questionsTranslations[$lang] ?? []),
                            'created_at' => $survey->created_at,
                            'updated_at' => $survey->updated_at,
                        ]);
                    }
                }
            } else {
                // 단일 언어만 있는 경우
                $lang = array_key_first($titleTranslations) ?? 'kor';
                DB::table('surveys')->where('id', $survey->id)->update([
                    'language' => $lang,
                    'is_primary' => true,
                ]);
            }
        }
        
        // JSON 필드 제거
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['title_translations', 'description_translations', 'checklist_items_translations', 'questions_translations']);
        });
    }
};
