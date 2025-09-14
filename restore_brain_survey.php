<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// 뇌신경 노화 셀프 테스트 찾기 (간편 분석)
$simpleSurvey = Survey::where('id', 2)->first();

if (!$simpleSurvey) {
    echo "간편 분석 설문을 찾을 수 없습니다.\n";
    exit;
}

echo "간편 분석 설문 찾음: ID {$simpleSurvey->id}, 제목: {$simpleSurvey->title}\n";

// 심층 분석 설문 찾기 또는 생성
$detailedSurvey = Survey::where('parent_id', $simpleSurvey->id)
                        ->where('is_detailed', true)
                        ->first();

if (!$detailedSurvey) {
    echo "심층 분석 설문이 없습니다. 새로 생성합니다.\n";
    
    // 심층 분석 설문 생성
    $detailedSurvey = new Survey();
    $detailedSurvey->parent_id = $simpleSurvey->id;
    $detailedSurvey->is_detailed = true;
    $detailedSurvey->title = $simpleSurvey->title . ' (심층 분석)';
    $detailedSurvey->save();
    
    echo "심층 분석 설문 생성됨: ID {$detailedSurvey->id}\n";
} else {
    echo "심층 분석 설문 찾음: ID {$detailedSurvey->id}, 제목: {$detailedSurvey->title}\n";
}

// 간편 분석의 모든 데이터를 심층 분석으로 복사
$detailedSurvey->checklist_items = $simpleSurvey->checklist_items;
$detailedSurvey->checklist_items_translations = $simpleSurvey->checklist_items_translations;
$detailedSurvey->frequency_items = $simpleSurvey->frequency_items;
$detailedSurvey->frequency_items_translations = $simpleSurvey->frequency_items_translations;
$detailedSurvey->questions = $simpleSurvey->questions;
$detailedSurvey->questions_translations = $simpleSurvey->questions_translations;
$detailedSurvey->result_commentary = $simpleSurvey->result_commentary;
$detailedSurvey->category_analysis_description = $simpleSurvey->category_analysis_description;
$detailedSurvey->survey_image = $simpleSurvey->survey_image;

// 제목은 (심층 분석) 추가
$detailedSurvey->title = $simpleSurvey->title . ' (심층 분석)';

// 다국어 제목도 업데이트
if ($simpleSurvey->title_translations) {
    $titleTranslations = $simpleSurvey->title_translations;
    foreach ($titleTranslations as $lang => $title) {
        if ($title) {
            $titleTranslations[$lang] = $title . ' (Detailed)';
        }
    }
    $detailedSurvey->title_translations = $titleTranslations;
}

// 설명도 복사
$detailedSurvey->description = $simpleSurvey->description;
$detailedSurvey->description_translations = $simpleSurvey->description_translations;

// 저장
$detailedSurvey->save();

echo "데이터 복사 완료!\n";

// 카테고리 데이터도 복사
$originalCategories = get_option('survey_categories_' . $simpleSurvey->id);
if ($originalCategories) {
    update_option('survey_categories_' . $detailedSurvey->id, $originalCategories);
    echo "카테고리 데이터도 복사되었습니다.\n";
} else {
    echo "카테고리 데이터가 없습니다.\n";
}

echo "\n복구 완료!\n";
echo "심층 분석 설문 ID: {$detailedSurvey->id}\n";
echo "제목: {$detailedSurvey->title}\n";