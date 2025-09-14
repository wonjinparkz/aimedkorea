<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// ID 1인 간편 분석 설문 찾기
$simpleSurvey = Survey::find(1);

if (!$simpleSurvey) {
    echo "ID 1 설문을 찾을 수 없습니다.\n";
    exit;
}

echo "간편 분석 설문 찾음: ID {$simpleSurvey->id}, 제목: {$simpleSurvey->title}\n";

// ID 8인 심층 분석 설문 찾기
$detailedSurvey = Survey::find(8);

if (!$detailedSurvey) {
    echo "ID 8 설문을 찾을 수 없습니다.\n";
    exit;
}

echo "심층 분석 설문 찾음: ID {$detailedSurvey->id}, 제목: {$detailedSurvey->title}\n";
echo "데이터 복사 시작...\n";

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

// 제목은 (심층 분석) 유지
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
echo "ID 1의 데이터를 ID 8로 복사했습니다.\n";