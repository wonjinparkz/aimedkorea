<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// 눈 노화 설문의 카테고리 구성
// 25개 문항을 카테고리별로 분류
$categoryData = [
    'survey_id' => 1,
    'categories' => [
        [
            'name' => [
                'kor' => '디지털 피로도',
                'eng' => 'Digital Fatigue',
                'chn' => '数字疲劳',
                'hin' => 'डिजिटल थकान',
                'arb' => 'التعب الرقمي'
            ],
            'description' => [
                'kor' => '디지털 기기 사용으로 인한 눈의 피로 정도',
                'eng' => 'Eye fatigue level due to digital device usage',
                'chn' => '因使用数字设备而导致的眼睛疲劳程度',
                'hin' => 'डिजिटल उपकरण के उपयोग से आंखों की थकान का स्तर',
                'arb' => 'مستوى إجهاد العين بسبب استخدام الأجهزة الرقمية'
            ],
            'result_description' => [
                'kor' => '디지털 기기 사용 시간을 줄이고 정기적인 휴식이 필요합니다.',
                'eng' => 'Reduce digital device usage time and take regular breaks.',
                'chn' => '减少数字设备使用时间并定期休息。',
                'hin' => 'डिजिटल उपकरण के उपयोग का समय कम करें और नियमित ब्रेक लें।',
                'arb' => 'قلل من وقت استخدام الأجهزة الرقمية وخذ فترات راحة منتظمة.'
            ],
            'question_indices' => [0, 1, 6, 8, 24] // 문항 인덱스 (0부터 시작)
        ],
        [
            'name' => [
                'kor' => '시력 변화',
                'eng' => 'Vision Changes',
                'chn' => '视力变化',
                'hin' => 'दृष्टि परिवर्तन',
                'arb' => 'تغيرات الرؤية'
            ],
            'description' => [
                'kor' => '시력 저하 및 초점 조절 문제',
                'eng' => 'Vision deterioration and focus adjustment problems',
                'chn' => '视力下降和焦点调节问题',
                'hin' => 'दृष्टि में गिरावट और फोकस समायोजन की समस्याएं',
                'arb' => 'تدهور الرؤية ومشاكل تعديل التركيز'
            ],
            'result_description' => [
                'kor' => '안과 검진을 받아보시고 적절한 시력 교정이 필요할 수 있습니다.',
                'eng' => 'Consider an eye examination and appropriate vision correction may be needed.',
                'chn' => '考虑进行眼科检查，可能需要适当的视力矫正。',
                'hin' => 'आंखों की जांच करवाएं और उचित दृष्टि सुधार की आवश्यकता हो सकती है।',
                'arb' => 'فكر في فحص العين وقد تكون هناك حاجة لتصحيح الرؤية المناسب.'
            ],
            'question_indices' => [2, 4, 7, 10, 11, 13, 17, 19]
        ],
        [
            'name' => [
                'kor' => '눈 건강 증상',
                'eng' => 'Eye Health Symptoms',
                'chn' => '眼健康症状',
                'hin' => 'आंखों के स्वास्थ्य के लक्षण',
                'arb' => 'أعراض صحة العين'
            ],
            'description' => [
                'kor' => '안구 건조, 충혈 등 눈 건강 관련 증상',
                'eng' => 'Eye health symptoms such as dry eyes and redness',
                'chn' => '眼睛健康相关症状，如干眼和充血',
                'hin' => 'आंखों के स्वास्थ्य के लक्षण जैसे सूखी आंखें और लालिमा',
                'arb' => 'أعراض صحة العين مثل جفاف العين والاحمرار'
            ],
            'result_description' => [
                'kor' => '인공눈물 사용과 함께 눈 건강 관리에 신경 쓰셔야 합니다.',
                'eng' => 'Pay attention to eye health care along with using artificial tears.',
                'chn' => '使用人工泪液的同时注意眼部健康护理。',
                'hin' => 'कृत्रिम आंसू के उपयोग के साथ आंखों के स्वास्थ्य की देखभाल पर ध्यान दें।',
                'arb' => 'انتبه لرعاية صحة العين بالإضافة إلى استخدام الدموع الاصطناعية.'
            ],
            'question_indices' => [8, 12, 14, 18, 22, 23]
        ],
        [
            'name' => [
                'kor' => '빛 민감도',
                'eng' => 'Light Sensitivity',
                'chn' => '光敏感度',
                'hin' => 'प्रकाश संवेदनशीलता',
                'arb' => 'حساسية الضوء'
            ],
            'description' => [
                'kor' => '빛에 대한 민감도 및 적응력',
                'eng' => 'Sensitivity and adaptability to light',
                'chn' => '对光的敏感性和适应性',
                'hin' => 'प्रकाश के प्रति संवेदनशीलता और अनुकूलनशीलता',
                'arb' => 'الحساسية والتكيف مع الضوء'
            ],
            'result_description' => [
                'kor' => '블루라이트 차단 안경 착용과 화면 밝기 조절이 도움이 될 수 있습니다.',
                'eng' => 'Wearing blue light blocking glasses and adjusting screen brightness may help.',
                'chn' => '佩戴防蓝光眼镜和调节屏幕亮度可能会有所帮助。',
                'hin' => 'ब्लू लाइट ब्लॉकिंग चश्मा पहनना और स्क्रीन की चमक को समायोजित करना मदद कर सकता है।',
                'arb' => 'قد يساعد ارتداء نظارات حجب الضوء الأزرق وضبط سطوع الشاشة.'
            ],
            'question_indices' => [3, 16, 21]
        ],
        [
            'name' => [
                'kor' => '눈 주변 증상',
                'eng' => 'Periocular Symptoms',
                'chn' => '眼周症状',
                'hin' => 'आंख के आसपास के लक्षण',
                'arb' => 'أعراض محيط العين'
            ],
            'description' => [
                'kor' => '눈 주변 근육 및 신경 관련 증상',
                'eng' => 'Symptoms related to periocular muscles and nerves',
                'chn' => '与眼周肌肉和神经相关的症状',
                'hin' => 'आंख के आसपास की मांसपेशियों और तंत्रिकाओं से संबंधित लक्षण',
                'arb' => 'الأعراض المتعلقة بالعضلات والأعصاب المحيطة بالعين'
            ],
            'result_description' => [
                'kor' => '눈 마사지와 충분한 수면이 증상 완화에 도움이 됩니다.',
                'eng' => 'Eye massage and adequate sleep help relieve symptoms.',
                'chn' => '眼部按摩和充足的睡眠有助于缓解症状。',
                'hin' => 'आंखों की मालिश और पर्याप्त नींद लक्षणों को कम करने में मदद करती है।',
                'arb' => 'تساعد تدليك العين والنوم الكافي في تخفيف الأعراض.'
            ],
            'question_indices' => [5, 9, 14, 15, 20]
        ]
    ]
];

// ID 1 카테고리 저장
echo "ID 1 카테고리 저장 중...\n";
update_option('survey_categories_1', $categoryData);
echo "ID 1 카테고리 저장 완료!\n";

// ID 8 카테고리 저장 (survey_id만 변경)
$categoryData8 = $categoryData;
$categoryData8['survey_id'] = 8;
echo "ID 8 카테고리 저장 중...\n";
update_option('survey_categories_8', $categoryData8);
echo "ID 8 카테고리 저장 완료!\n";

// 확인
$check1 = get_option('survey_categories_1');
$check8 = get_option('survey_categories_8');

echo "\n=== 복구 결과 ===\n";
echo "ID 1 카테고리: " . ($check1 ? count($check1['categories']) . "개 카테고리" : "없음") . "\n";
echo "ID 8 카테고리: " . ($check8 ? count($check8['categories']) . "개 카테고리" : "없음") . "\n";

if ($check1) {
    echo "\n카테고리 목록:\n";
    foreach ($check1['categories'] as $idx => $cat) {
        echo ($idx + 1) . ". " . $cat['name']['kor'] . " - " . count($cat['question_indices']) . "개 문항\n";
    }
}

echo "\n카테고리 복구 완료!\n";