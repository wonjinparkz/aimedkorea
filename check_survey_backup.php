<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;
use Illuminate\Support\Facades\DB;

// ID 1과 8의 현재 상태 확인
$survey1 = Survey::find(1);
$survey8 = Survey::find(8);

echo "=== 현재 상태 ===\n";
echo "ID 1 설문:\n";
if ($survey1) {
    echo "  제목: {$survey1->title}\n";
    echo "  설명: " . substr($survey1->description ?? '', 0, 50) . "...\n";
    echo "  문항 수: " . count($survey1->questions ?? []) . "\n";
    echo "  체크리스트 항목 수: " . count($survey1->checklist_items ?? []) . "\n";
}

echo "\nID 8 설문:\n";
if ($survey8) {
    echo "  제목: {$survey8->title}\n";
    echo "  설명: " . substr($survey8->description ?? '', 0, 50) . "...\n";
    echo "  문항 수: " . count($survey8->questions ?? []) . "\n";
    echo "  체크리스트 항목 수: " . count($survey8->checklist_items ?? []) . "\n";
}

// 카테고리 데이터 확인
echo "\n=== 카테고리 데이터 ===\n";
$categories1 = get_option('survey_categories_1');
$categories8 = get_option('survey_categories_8');

echo "ID 1 카테고리: " . ($categories1 ? "있음" : "없음") . "\n";
echo "ID 8 카테고리: " . ($categories8 ? "있음" : "없음") . "\n";

// 백업이나 이전 데이터가 있는지 확인
echo "\n=== 데이터베이스 로그 확인 ===\n";
$recentUpdates = DB::table('surveys')
    ->whereIn('id', [1, 8])
    ->select('id', 'updated_at')
    ->get();

foreach ($recentUpdates as $update) {
    echo "ID {$update->id} 마지막 업데이트: {$update->updated_at}\n";
}