<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// Seeder에서 가져온 원본 데이터
$originalData = [
    'title' => '눈 노화 컨디션 셀프 테스트',
    'description' => '디지털 기기 사용으로 인한 눈의 피로도와 노화 정도를 자가진단합니다.',
    'checklist_items' => [
        ['label' => '전혀 그렇지 않다', 'score' => 0],
        ['label' => '그렇지 않다', 'score' => 1],
        ['label' => '보통이다', 'score' => 2],
        ['label' => '그렇다', 'score' => 3],
        ['label' => '매우 그렇다', 'score' => 4],
    ],
    'questions' => [
        ['label' => '하루에 디지털 기기(컴퓨터, 스마트폰 등)를 4시간 이상 사용한다'],
        ['label' => '눈이 자주 피로하고 뻑뻑한 느낌이 든다'],
        ['label' => '가까운 곳에서 먼 곳으로 시선을 옮길 때 초점이 잘 맞지 않는다'],
        ['label' => '밝은 빛이나 화면을 볼 때 눈부심이 심하다'],
        ['label' => '글자나 사물이 흐릿하게 보이거나 겹쳐 보인다'],
        ['label' => '눈 주위가 무겁고 두통이 자주 발생한다'],
        ['label' => '눈을 자주 비비거나 깜빡이게 된다'],
        ['label' => '야간에 시력이 낮보다 현저히 떨어진다'],
        ['label' => '눈물이 자주 나거나 반대로 눈이 매우 건조하다'],
        ['label' => '휴식을 취해도 눈의 피로가 쉽게 회복되지 않는다'],
        ['label' => '디지털 화면의 작은 글씨를 읽기 어렵다'],
        ['label' => '색상 구분이 예전보다 어려워졌다'],
        ['label' => '눈 앞에 날파리나 점이 떠다니는 것처럼 보인다'],
        ['label' => '장시간 독서나 작업 후 시야가 흐려진다'],
        ['label' => '눈꺼풀이 무겁고 자주 떨린다'],
        ['label' => '안구 통증이나 압박감을 느낀다'],
        ['label' => '빛 번짐이나 후광이 보인다'],
        ['label' => '시야의 일부가 흐릿하거나 왜곡되어 보인다'],
        ['label' => '눈의 충혈이 자주 발생한다'],
        ['label' => '안경이나 콘택트렌즈를 착용해도 시력 개선이 미미하다'],
        ['label' => '눈 주변 근육의 경련이 자주 발생한다'],
        ['label' => '강한 빛에 노출 후 회복 시간이 오래 걸린다'],
        ['label' => '눈물샘이 막힌 느낌이 든다'],
        ['label' => '아침에 눈을 뜨기 힘들 정도로 눈이 붓는다'],
        ['label' => '컴퓨터 작업 중 자주 휴식을 취해야 한다'],
    ],
    'frequency_items' => [
        ['label' => '전혀 없음', 'score' => 0],
        ['label' => '월 1-2회', 'score' => 1],
        ['label' => '주 1-2회', 'score' => 2],
        ['label' => '주 3-4회', 'score' => 3],
        ['label' => '거의 매일', 'score' => 4],
    ],
];

// ID 1 복구 (간편 분석)
$survey1 = Survey::find(1);
if ($survey1) {
    echo "ID 1 설문 복구 시작...\n";
    
    $survey1->title = $originalData['title'];
    $survey1->description = $originalData['description'];
    $survey1->checklist_items = $originalData['checklist_items'];
    $survey1->questions = $originalData['questions'];
    $survey1->frequency_items = $originalData['frequency_items'];
    
    // 기본 다국어 데이터 설정
    $survey1->checklist_items_translations = [
        'kor' => $originalData['checklist_items'],
        'eng' => [
            ['label' => 'Not at all', 'score' => 0],
            ['label' => 'Rarely', 'score' => 1],
            ['label' => 'Sometimes', 'score' => 2],
            ['label' => 'Often', 'score' => 3],
            ['label' => 'Very often', 'score' => 4],
        ],
    ];
    
    $survey1->frequency_items_translations = [
        'kor' => $originalData['frequency_items'],
        'eng' => [
            ['label' => 'Never', 'score' => 0],
            ['label' => '1-2 times/month', 'score' => 1],
            ['label' => '1-2 times/week', 'score' => 2],
            ['label' => '3-4 times/week', 'score' => 3],
            ['label' => 'Almost daily', 'score' => 4],
        ],
    ];
    
    $survey1->questions_translations = [
        'kor' => $originalData['questions'],
    ];
    
    $survey1->save();
    echo "ID 1 설문 복구 완료!\n";
}

// ID 8 복구 (심층 분석)
$survey8 = Survey::find(8);
if ($survey8) {
    echo "ID 8 설문 복구 시작...\n";
    
    $survey8->title = $originalData['title'] . ' (심층 분석)';
    $survey8->description = $originalData['description'];
    $survey8->checklist_items = $originalData['checklist_items'];
    $survey8->questions = $originalData['questions'];
    $survey8->frequency_items = $originalData['frequency_items'];
    
    // 다국어 데이터 복사
    $survey8->checklist_items_translations = [
        'kor' => $originalData['checklist_items'],
        'eng' => [
            ['label' => 'Not at all', 'score' => 0],
            ['label' => 'Rarely', 'score' => 1],
            ['label' => 'Sometimes', 'score' => 2],
            ['label' => 'Often', 'score' => 3],
            ['label' => 'Very often', 'score' => 4],
        ],
    ];
    
    $survey8->frequency_items_translations = [
        'kor' => $originalData['frequency_items'],
        'eng' => [
            ['label' => 'Never', 'score' => 0],
            ['label' => '1-2 times/month', 'score' => 1],
            ['label' => '1-2 times/week', 'score' => 2],
            ['label' => '3-4 times/week', 'score' => 3],
            ['label' => 'Almost daily', 'score' => 4],
        ],
    ];
    
    $survey8->questions_translations = [
        'kor' => $originalData['questions'],
    ];
    
    $survey8->parent_id = 1;
    $survey8->is_detailed = true;
    
    $survey8->save();
    echo "ID 8 설문 복구 완료!\n";
}

echo "\n복구 작업 완료!\n";
echo "ID 1: " . $survey1->title . " - 문항 수: " . count($survey1->questions) . "\n";
echo "ID 8: " . $survey8->title . " - 문항 수: " . count($survey8->questions) . "\n";