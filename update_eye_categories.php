<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Survey;

// 새로운 카테고리 구조 (25개 문항 기준)
$newCategories = [
    [
        'name' => [
            'kor' => '눈 피로도 회복',
            'eng' => 'Eye Fatigue Recovery',
            'chn' => '眼疲劳恢复',
            'hin' => 'आंखों की थकान से उबरना',
            'arb' => 'التعافي من إجهاد العين'
        ],
        'description' => [
            'kor' => '눈의 피로 회복 능력 평가',
            'eng' => 'Assessment of eye fatigue recovery ability',
            'chn' => '眼疲劳恢复能力评估',
            'hin' => 'आंखों की थकान की रिकवरी क्षमता का मूल्यांकन',
            'arb' => 'تقييم قدرة التعافي من إجهاد العين'
        ],
        'result_description' => [
            'kor' => '충분한 휴식과 눈 운동이 필요합니다.',
            'eng' => 'Adequate rest and eye exercises are needed.',
            'chn' => '需要充足的休息和眼部运动。',
            'hin' => 'पर्याप्त आराम और आंखों के व्यायाम की आवश्यकता है।',
            'arb' => 'هناك حاجة إلى الراحة الكافية وتمارين العين.'
        ],
        'question_indices' => [0, 1, 2] // 문항 1,2,3
    ],
    [
        'name' => [
            'kor' => '눈 예민도',
            'eng' => 'Eye Sensitivity',
            'chn' => '眼睛敏感度',
            'hin' => 'आंखों की संवेदनशीलता',
            'arb' => 'حساسية العين'
        ],
        'description' => [
            'kor' => '빛과 자극에 대한 눈의 민감도',
            'eng' => 'Eye sensitivity to light and stimuli',
            'chn' => '眼睛对光和刺激的敏感度',
            'hin' => 'प्रकाश और उत्तेजनाओं के प्रति आंखों की संवेदनशीलता',
            'arb' => 'حساسية العين للضوء والمحفزات'
        ],
        'result_description' => [
            'kor' => '블루라이트 차단과 화면 밝기 조절이 필요합니다.',
            'eng' => 'Blue light blocking and screen brightness adjustment are needed.',
            'chn' => '需要阻挡蓝光并调节屏幕亮度。',
            'hin' => 'ब्लू लाइट ब्लॉकिंग और स्क्रीन की चमक समायोजन की आवश्यकता है।',
            'arb' => 'هناك حاجة إلى حجب الضوء الأزرق وضبط سطوع الشاشة.'
        ],
        'question_indices' => [3, 4, 5] // 문항 4,5,6
    ],
    [
        'name' => [
            'kor' => '눈 건강 상황',
            'eng' => 'Eye Health Status',
            'chn' => '眼健康状况',
            'hin' => 'आंखों के स्वास्थ्य की स्थिति',
            'arb' => 'حالة صحة العين'
        ],
        'description' => [
            'kor' => '전반적인 눈 건강 상태 평가',
            'eng' => 'Overall eye health status assessment',
            'chn' => '整体眼健康状况评估',
            'hin' => 'समग्र आंख स्वास्थ्य स्थिति का मूल्यांकन',
            'arb' => 'تقييم حالة صحة العين الشاملة'
        ],
        'result_description' => [
            'kor' => '정기적인 안과 검진을 권장합니다.',
            'eng' => 'Regular eye examinations are recommended.',
            'chn' => '建议定期进行眼科检查。',
            'hin' => 'नियमित आंखों की जांच की सिफारिश की जाती है।',
            'arb' => 'يُنصح بإجراء فحوصات العين المنتظمة.'
        ],
        'question_indices' => [6, 7, 8] // 문항 7,8,9
    ],
    [
        'name' => [
            'kor' => '시력 회복력',
            'eng' => 'Vision Recovery',
            'chn' => '视力恢复力',
            'hin' => 'दृष्टि रिकवरी',
            'arb' => 'استعادة الرؤية'
        ],
        'description' => [
            'kor' => '시력 변화와 회복 능력',
            'eng' => 'Vision changes and recovery ability',
            'chn' => '视力变化和恢复能力',
            'hin' => 'दृष्टि परिवर्तन और रिकवरी क्षमता',
            'arb' => 'تغيرات الرؤية وقدرة الاستعادة'
        ],
        'result_description' => [
            'kor' => '시력 교정과 눈 영양제 섭취를 고려하세요.',
            'eng' => 'Consider vision correction and eye supplements.',
            'chn' => '考虑视力矫正和眼部营养补充。',
            'hin' => 'दृष्टि सुधार और आंखों के पूरक पर विचार करें।',
            'arb' => 'فكر في تصحيح الرؤية ومكملات العين.'
        ],
        'question_indices' => [9, 10, 11] // 문항 10,11,12
    ],
    [
        'name' => [
            'kor' => '시야 반응성',
            'eng' => 'Visual Responsiveness',
            'chn' => '视野反应性',
            'hin' => 'दृश्य प्रतिक्रियाशीलता',
            'arb' => 'الاستجابة البصرية'
        ],
        'description' => [
            'kor' => '시야의 반응 속도와 적응력',
            'eng' => 'Visual response speed and adaptability',
            'chn' => '视野反应速度和适应性',
            'hin' => 'दृश्य प्रतिक्रिया गति और अनुकूलनशीलता',
            'arb' => 'سرعة الاستجابة البصرية والقدرة على التكيف'
        ],
        'result_description' => [
            'kor' => '눈 운동과 시야 훈련이 도움이 됩니다.',
            'eng' => 'Eye exercises and visual training can help.',
            'chn' => '眼部运动和视野训练会有帮助。',
            'hin' => 'आंखों के व्यायाम और दृश्य प्रशिक्षण मदद कर सकते हैं।',
            'arb' => 'يمكن أن تساعد تمارين العين والتدريب البصري.'
        ],
        'question_indices' => [12, 13, 14] // 문항 13,14,15
    ],
    [
        'name' => [
            'kor' => '시야 정확도',
            'eng' => 'Visual Accuracy',
            'chn' => '视野准确度',
            'hin' => 'दृश्य सटीकता',
            'arb' => 'الدقة البصرية'
        ],
        'description' => [
            'kor' => '시야의 선명도와 정확성',
            'eng' => 'Visual clarity and accuracy',
            'chn' => '视野的清晰度和准确性',
            'hin' => 'दृश्य स्पष्टता और सटीकता',
            'arb' => 'الوضوح والدقة البصرية'
        ],
        'result_description' => [
            'kor' => '적절한 조명과 화면 거리 조절이 중요합니다.',
            'eng' => 'Proper lighting and screen distance adjustment are important.',
            'chn' => '适当的照明和屏幕距离调节很重要。',
            'hin' => 'उचित प्रकाश और स्क्रीन दूरी समायोजन महत्वपूर्ण है।',
            'arb' => 'الإضاءة المناسبة وضبط مسافة الشاشة مهمان.'
        ],
        'question_indices' => [15, 16, 17] // 문항 16,17,18
    ],
    [
        'name' => [
            'kor' => '눈 불편함 정도',
            'eng' => 'Eye Discomfort Level',
            'chn' => '眼部不适程度',
            'hin' => 'आंखों की असुविधा का स्तर',
            'arb' => 'مستوى انزعاج العين'
        ],
        'description' => [
            'kor' => '일상생활에서 느끼는 눈의 불편함',
            'eng' => 'Eye discomfort experienced in daily life',
            'chn' => '日常生活中感受到的眼部不适',
            'hin' => 'दैनिक जीवन में अनुभव की जाने वाली आंखों की असुविधा',
            'arb' => 'عدم الراحة في العين في الحياة اليومية'
        ],
        'result_description' => [
            'kor' => '작업 환경 개선과 휴식 시간 확보가 필요합니다.',
            'eng' => 'Work environment improvement and rest time are needed.',
            'chn' => '需要改善工作环境并确保休息时间。',
            'hin' => 'कार्य वातावरण में सुधार और आराम का समय आवश्यक है।',
            'arb' => 'هناك حاجة لتحسين بيئة العمل ووقت الراحة.'
        ],
        'question_indices' => [18, 19, 20] // 문항 19,20,21
    ],
    [
        'name' => [
            'kor' => '눈 스트레스',
            'eng' => 'Eye Stress',
            'chn' => '眼部压力',
            'hin' => 'आंखों का तनाव',
            'arb' => 'إجهاد العين'
        ],
        'description' => [
            'kor' => '눈의 스트레스와 긴장도',
            'eng' => 'Eye stress and tension level',
            'chn' => '眼部压力和紧张程度',
            'hin' => 'आंखों का तनाव और तनाव का स्तर',
            'arb' => 'مستوى إجهاد وتوتر العين'
        ],
        'result_description' => [
            'kor' => '스트레스 관리와 이완 운동이 도움이 됩니다.',
            'eng' => 'Stress management and relaxation exercises help.',
            'chn' => '压力管理和放松运动会有帮助。',
            'hin' => 'तनाव प्रबंधन और विश्राम व्यायाम मदद करते हैं।',
            'arb' => 'إدارة الإجهاد وتمارين الاسترخاء تساعد.'
        ],
        'question_indices' => [21, 22, 23] // 문항 22,23,24
    ],
    [
        'name' => [
            'kor' => '눈 건강 예방',
            'eng' => 'Eye Health Prevention',
            'chn' => '眼健康预防',
            'hin' => 'आंखों के स्वास्थ्य की रोकथाम',
            'arb' => 'الوقاية من صحة العين'
        ],
        'description' => [
            'kor' => '눈 건강 악화 예방 필요성',
            'eng' => 'Need for preventing eye health deterioration',
            'chn' => '预防眼健康恶化的必要性',
            'hin' => 'आंखों के स्वास्थ्य में गिरावट को रोकने की आवश्यकता',
            'arb' => 'الحاجة إلى منع تدهور صحة العين'
        ],
        'result_description' => [
            'kor' => '정기적인 관리와 예방 조치가 중요합니다.',
            'eng' => 'Regular care and preventive measures are important.',
            'chn' => '定期护理和预防措施很重要。',
            'hin' => 'नियमित देखभाल और निवारक उपाय महत्वपूर्ण हैं।',
            'arb' => 'الرعاية المنتظمة والتدابير الوقائية مهمة.'
        ],
        'question_indices' => [24] // 문항 25
    ]
];

// 카테고리 데이터 구조 생성
$categoryData = [
    'survey_id' => 1,
    'categories' => $newCategories
];

// ID 1과 ID 8에 새로운 카테고리 저장
echo "ID 1 카테고리 업데이트 중...\n";
update_option('survey_categories_1', $categoryData);

$categoryData8 = $categoryData;
$categoryData8['survey_id'] = 8;
echo "ID 8 카테고리 업데이트 중...\n";
update_option('survey_categories_8', $categoryData8);

// 확인
$check1 = get_option('survey_categories_1');
$check8 = get_option('survey_categories_8');

echo "\n=== 업데이트 결과 ===\n";
echo "ID 1 카테고리: " . ($check1 ? count($check1['categories']) . "개 카테고리" : "없음") . "\n";
echo "ID 8 카테고리: " . ($check8 ? count($check8['categories']) . "개 카테고리" : "없음") . "\n";

if ($check1) {
    echo "\n새로운 카테고리 목록:\n";
    foreach ($check1['categories'] as $idx => $cat) {
        $questionNumbers = array_map(function($i) { return $i + 1; }, $cat['question_indices']);
        echo ($idx + 1) . ". " . $cat['name']['kor'] . " - 문항 " . implode(',', $questionNumbers) . "\n";
    }
}

echo "\n카테고리 업데이트 완료!\n";