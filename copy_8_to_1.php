<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// ID 8인 심층 분석 설문 찾기 (소스)
$detailedSurvey = Survey::find(8);

if (!$detailedSurvey) {
    echo "ID 8 설문을 찾을 수 없습니다.\n";
    exit;
}

echo "소스 설문 찾음: ID {$detailedSurvey->id}, 제목: {$detailedSurvey->title}\n";

// ID 1인 간편 분석 설문 찾기 (대상)
$simpleSurvey = Survey::find(1);

if (!$simpleSurvey) {
    echo "ID 1 설문을 찾을 수 없습니다.\n";
    exit;
}

echo "대상 설문 찾음: ID {$simpleSurvey->id}, 제목: {$simpleSurvey->title}\n";
echo "데이터 복사 시작 (ID 8 → ID 1)...\n";

// ID 8의 모든 데이터를 ID 1로 복사 (제목 제외)
$simpleSurvey->checklist_items = $detailedSurvey->checklist_items;
$simpleSurvey->checklist_items_translations = $detailedSurvey->checklist_items_translations;
$simpleSurvey->frequency_items = $detailedSurvey->frequency_items;
$simpleSurvey->frequency_items_translations = $detailedSurvey->frequency_items_translations;
$simpleSurvey->questions = $detailedSurvey->questions;
$simpleSurvey->questions_translations = $detailedSurvey->questions_translations;
$simpleSurvey->result_commentary = $detailedSurvey->result_commentary;
$simpleSurvey->category_analysis_description = $detailedSurvey->category_analysis_description;
$simpleSurvey->survey_image = $detailedSurvey->survey_image;

// 제목은 간편 분석 그대로 유지
// $simpleSurvey->title은 변경하지 않음

// 설명도 복사
$simpleSurvey->description = $detailedSurvey->description;
$simpleSurvey->description_translations = $detailedSurvey->description_translations;

// 저장
$simpleSurvey->save();

echo "데이터 복사 완료!\n";

// 카테고리 데이터도 복사 (ID 8 → ID 1)
$categories8 = get_option('survey_categories_8');
if ($categories8) {
    // ID 1용 카테고리 데이터 업데이트
    $categories1 = $categories8;
    $categories1['survey_id'] = 1;  // survey_id를 1로 변경
    update_option('survey_categories_1', $categories1);
    echo "카테고리 데이터도 복사되었습니다.\n";
} else {
    echo "ID 8에 카테고리 데이터가 없습니다.\n";
}

echo "\n복구 완료!\n";
echo "ID 8의 데이터를 ID 1로 복사했습니다.\n";
echo "ID 1의 제목은 유지: {$simpleSurvey->title}\n";