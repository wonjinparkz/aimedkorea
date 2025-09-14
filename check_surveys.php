<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// 모든 설문 조사 출력
$surveys = Survey::all();

echo "총 설문 수: " . $surveys->count() . "\n\n";

foreach ($surveys as $survey) {
    echo "ID: {$survey->id}\n";
    echo "제목: {$survey->title}\n";
    echo "심층 분석: " . ($survey->is_detailed ? '예' : '아니오') . "\n";
    echo "부모 ID: " . ($survey->parent_id ?? '없음') . "\n";
    echo "-------------------\n";
}

// 인지 기능 관련 설문 찾기
echo "\n인지 기능 관련 설문 검색:\n";
$cognitiveSurveys = Survey::where('title', 'LIKE', '%인지%')
                          ->orWhere('title', 'LIKE', '%노화%')
                          ->get();

foreach ($cognitiveSurveys as $survey) {
    echo "ID: {$survey->id}, 제목: {$survey->title}, 심층: " . ($survey->is_detailed ? '예' : '아니오') . ", 부모: " . ($survey->parent_id ?? '없음') . "\n";
}