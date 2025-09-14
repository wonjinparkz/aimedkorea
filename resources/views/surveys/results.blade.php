<x-app-layout>
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex, nofollow">
        <meta name="description" content="Private survey results - not for indexing">
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $currentLang = session('locale', 'kor');
                
                // 다국어 텍스트 정의
                $translations = [
                    'start_simple_check' => [
                        'kor' => '간편체크 시작',
                        'eng' => 'Start Simple Check',
                        'chn' => '开始简单检查',
                        'hin' => 'सरल जांच शुरू करें',
                        'arb' => 'بدء الفحص البسيط'
                    ],
                    'start_deep_check' => [
                        'kor' => '심층체크(12주) 시작',
                        'eng' => 'Start Deep Check (12 weeks)',
                        'chn' => '开始深度检查（12周）',
                        'hin' => 'गहन जांच शुरू करें (12 सप्ताह)',
                        'arb' => 'بدء الفحص العميق (12 أسبوع)'
                    ],
                    'aging_index_desc' => [
                        'kor' => '귀하의 디지털 노화 상태를 나타내는 노화지수입니다.',
                        'eng' => 'This is your aging index indicating your digital aging status.',
                        'chn' => '这是表示您数字老化状态的老化指数。',
                        'hin' => 'यह आपकी डिजिटल एजिंग स्थिति को दर्शाने वाला एजिंग इंडेक्स है।',
                        'arb' => 'هذا هو مؤشر الشيخوخة الذي يشير إلى حالة الشيخوخة الرقمية الخاصة بك.'
                    ],
                    'important_notice' => [
                        'kor' => '중요 안내',
                        'eng' => 'Important Notice',
                        'chn' => '重要通知',
                        'hin' => 'महत्वपूर्ण सूचना',
                        'arb' => 'إشعار مهم'
                    ],
                    'disclaimer_1' => [
                        'kor' => '본 결과는 웰니스 목적의 자가평가 정보이며, 질병의 진단·치료·예방을 위한 것이 아닙니다.',
                        'eng' => 'These results are self-assessment information for wellness purposes and are not intended for diagnosis, treatment, or prevention of diseases.',
                        'chn' => '本结果是用于健康目的的自我评估信息，不用于疾病的诊断、治疗或预防。',
                        'hin' => 'ये परिणाम वेलनेस उद्देश्यों के लिए स्व-मूल्यांकन जानकारी हैं और रोगों के निदान, उपचार या रोकथाम के लिए नहीं हैं।',
                        'arb' => 'هذه النتائج هي معلومات تقييم ذاتي لأغراض الصحة وليست مخصصة لتشخيص أو علاج أو الوقاية من الأمراض.'
                    ],
                    'disclaimer_2' => [
                        'kor' => '화면의 수치는 내부 컨디션 지표(H/레벨/Model Confidence)로, 개인 특성에 따라 달라질 수 있습니다.',
                        'eng' => 'The values on the screen are internal condition indicators (H/Level/Model Confidence) and may vary depending on individual characteristics.',
                        'chn' => '屏幕上的数值是内部状态指标（H/级别/模型置信度），可能因个人特征而异。',
                        'hin' => 'स्क्रीन पर दिए गए मान आंतरिक स्थिति संकेतक (H/स्तर/मॉडल विश्वास) हैं और व्यक्तिगत विशेषताओं के आधार पर भिन्न हो सकते हैं।',
                        'arb' => 'القيم على الشاشة هي مؤشرات الحالة الداخلية (H/المستوى/ثقة النموذج) وقد تختلف حسب الخصائص الفردية.'
                    ],
                    'aging_index' => [
                        'kor' => '노화지수',
                        'eng' => 'Aging Index',
                        'chn' => '老化指数',
                        'hin' => 'एजिंग इंडेक्स',
                        'arb' => 'مؤشر الشيخوخة'
                    ],
                    'digital_aging_index' => [
                        'kor' => '디지털 노화지수',
                        'eng' => 'Digital Aging Index',
                        'chn' => '数字老化指数',
                        'hin' => 'डिजिटल एजिंग इंडेक्स',
                        'arb' => 'مؤشر الشيخوخة الرقمية'
                    ],
                    'actual_score' => [
                        'kor' => '실제 점수',
                        'eng' => 'Actual Score',
                        'chn' => '实际分数',
                        'hin' => 'वास्तविक स्कोर',
                        'arb' => 'النتيجة الفعلية'
                    ],
                    'lower_better' => [
                        'kor' => '낮은 노화지수일수록 좋은 상태입니다',
                        'eng' => 'Lower aging index indicates better condition',
                        'chn' => '老化指数越低，状态越好',
                        'hin' => 'कम एजिंग इंडेक्स बेहतर स्थिति का संकेत देता है',
                        'arb' => 'يشير مؤشر الشيخوخة المنخفض إلى حالة أفضل'
                    ],
                    'status' => [
                        'kor' => '상태',
                        'eng' => 'Status',
                        'chn' => '状态',
                        'hin' => 'स्थिति',
                        'arb' => 'الحالة'
                    ],
                    'segments' => [
                        'optimal' => [
                            'kor' => '최적',
                            'eng' => 'Optimal',
                            'chn' => '最佳',
                            'hin' => 'इष्टतम',
                            'arb' => 'الأمثل'
                        ],
                        'excellent' => [
                            'kor' => '우수',
                            'eng' => 'Excellent',
                            'chn' => '优秀',
                            'hin' => 'उत्कृष्ट',
                            'arb' => 'ممتاز'
                        ],
                        'good' => [
                            'kor' => '양호',
                            'eng' => 'Good',
                            'chn' => '良好',
                            'hin' => 'अच्छा',
                            'arb' => 'جيد'
                        ],
                        'caution' => [
                            'kor' => '주의',
                            'eng' => 'Caution',
                            'chn' => '注意',
                            'hin' => 'सावधानी',
                            'arb' => 'تنبيه'
                        ],
                        'risk' => [
                            'kor' => '위험',
                            'eng' => 'Risk',
                            'chn' => '危险',
                            'hin' => 'जोखिम',
                            'arb' => 'خطر'
                        ],
                        'collapse' => [
                            'kor' => '붕괴',
                            'eng' => 'Collapse',
                            'chn' => '崩溃',
                            'hin' => 'पतन',
                            'arb' => 'انهيار'
                        ]
                    ],
                    'category_analysis' => [
                        'kor' => '카테고리별 분석',
                        'eng' => 'Category Analysis',
                        'chn' => '分类分析',
                        'hin' => 'श्रेणी विश्लेषण',
                        'arb' => 'تحليل الفئات'
                    ],
                    'excellent_areas' => [
                        'kor' => '우수 영역 TOP 3',
                        'eng' => 'Top 3 Excellent Areas',
                        'chn' => '优秀领域 TOP 3',
                        'hin' => 'शीर्ष 3 उत्कृष्ट क्षेत्र',
                        'arb' => 'أفضل 3 مجالات ممتازة'
                    ],
                    'improvement_areas' => [
                        'kor' => '개선 필요 영역',
                        'eng' => 'Areas Needing Improvement',
                        'chn' => '需要改进的领域',
                        'hin' => 'सुधार की आवश्यकता वाले क्षेत्र',
                        'arb' => 'المجالات التي تحتاج إلى تحسين'
                    ],
                    'no_response' => [
                        'kor' => '미응답',
                        'eng' => 'No Response',
                        'chn' => '未回答',
                        'hin' => 'कोई प्रतिक्रिया नहीं',
                        'arb' => 'لا يوجد رد'
                    ],
                    'no_response_data' => [
                        'kor' => '응답 데이터 없음',
                        'eng' => 'No Response Data',
                        'chn' => '无响应数据',
                        'hin' => 'कोई प्रतिक्रिया डेटा नहीं',
                        'arb' => 'لا توجد بيانات استجابة'
                    ],
                    'buttons' => [
                        'survey_list' => [
                            'kor' => '설문 목록으로',
                            'eng' => 'Back to Survey List',
                            'chn' => '返回调查列表',
                            'hin' => 'सर्वेक्षण सूची पर वापस जाएं',
                            'arb' => 'العودة إلى قائمة الاستطلاع'
                        ],
                        'recovery_dashboard' => [
                            'kor' => '회복 대시보드',
                            'eng' => 'Recovery Dashboard',
                            'chn' => '恢复仪表板',
                            'hin' => 'रिकवरी डैशबोर्ड',
                            'arb' => 'لوحة التعافي'
                        ],
                        'retry_test' => [
                            'kor' => '다시 테스트하기',
                            'eng' => 'Retry Test',
                            'chn' => '重新测试',
                            'hin' => 'फिर से परीक्षण करें',
                            'arb' => 'إعادة الاختبار'
                        ],
                        'print_result' => [
                            'kor' => '결과 인쇄',
                            'eng' => 'Print Results',
                            'chn' => '打印结果',
                            'hin' => 'परिणाम प्रिंट करें',
                            'arb' => 'طباعة النتائج'
                        ]
                    ],
                    'disclaimer_title' => [
                        'kor' => '면책 고지',
                        'eng' => 'Disclaimer',
                        'chn' => '免责声明',
                        'hin' => 'अस्वीकरण',
                        'arb' => 'إخلاء المسؤولية'
                    ]
                ];
            @endphp
            
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">{{ $survey->getTitle($currentLang) }}</h1>
                <p class="text-xl text-gray-600">{{ $translations['aging_index_desc'][$currentLang] ?? $translations['aging_index_desc']['kor'] }}</p>
                
                @php
                    // 노화지수 계산
                    $actualScore = $response->total_score;
                    $maxPossibleScore = count($survey->questions) * 4;
                    $agingIndex = round(($actualScore / $maxPossibleScore) * 100);
                    
                    // 노화지수에 따른 결과 해설 가져오기
                    $resultCommentary = $survey->getResultCommentary($currentLang, $agingIndex);
                @endphp
                
                @if($resultCommentary)
                    <div class="mt-6 p-6 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="prose prose-blue mx-auto">
                            {!! $resultCommentary !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- CTA 버튼 섹션 -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 border border-blue-200">
                <div class="grid grid-cols-2 gap-4 max-w-2xl mx-auto">
                    <!-- 간편체크 시작 버튼 -->
                    <a href="{{ route('surveys.index') }}?survey_id={{ $survey->parent_id ?? $survey->id }}&analysis_type=simple#survey-{{ $survey->parent_id ?? $survey->id }}" 
                       onclick="trackEvent('simple_check_start', { survey_id: {{ $survey->parent_id ?? $survey->id }} })"
                       class="flex items-center justify-center px-4 sm:px-6 py-3 bg-white border-2 border-blue-500 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>{{ $translations['start_simple_check'][$currentLang] ?? $translations['start_simple_check']['kor'] }}</span>
                    </a>
                    
                    <!-- 심층체크(12주) 시작 버튼 -->
                    <a href="{{ route('surveys.index') }}?survey_id={{ $survey->parent_id ?? $survey->id }}&analysis_type=detailed#survey-{{ $survey->parent_id ?? $survey->id }}" 
                       onclick="trackEvent('deep_check_start', { survey_id: {{ $survey->parent_id ?? $survey->id }}, event_name: 'deep_start' }); trackEvent('deep_offer_shown', { survey_id: {{ $survey->parent_id ?? $survey->id }} })"
                       class="flex items-center justify-center px-4 sm:px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ $translations['start_deep_check'][$currentLang] ?? $translations['start_deep_check']['kor'] }}</span>
                    </a>
                </div>
            </div>

            <!-- 면책 고지 (상단) -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6" @if($currentLang == 'arb') dir="rtl" @endif>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 {{ $currentLang == 'arb' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-amber-800 {{ $currentLang == 'arb' ? 'text-right' : '' }}">
                        <p class="font-medium mb-1">{{ $translations['important_notice'][$currentLang] ?? $translations['important_notice']['kor'] }}</p>
                        <p>{{ $translations['disclaimer_1'][$currentLang] ?? $translations['disclaimer_1']['kor'] }}</p>
                        <p class="mt-2">{{ $translations['disclaimer_2'][$currentLang] ?? $translations['disclaimer_2']['kor'] }}</p>
                    </div>
                </div>
            </div>

            <!-- 계기판 스타일 점수 표시 -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="max-w-lg mx-auto">
                    @php
                        // 노화지수 기준 6개 구간 정의 (역순으로 표시하기 위해)
                        $segments = [
                            ['name' => $translations['segments']['optimal'][$currentLang] ?? $translations['segments']['optimal']['kor'], 'color' => '#047857', 'range' => [0, 15]],      // 0~15%
                            ['name' => $translations['segments']['excellent'][$currentLang] ?? $translations['segments']['excellent']['kor'], 'color' => '#059669', 'range' => [16, 30]],     // 16~30%
                            ['name' => $translations['segments']['good'][$currentLang] ?? $translations['segments']['good']['kor'], 'color' => '#10b981', 'range' => [31, 50]],     // 31~50%
                            ['name' => $translations['segments']['caution'][$currentLang] ?? $translations['segments']['caution']['kor'], 'color' => '#f59e0b', 'range' => [51, 70]],     // 51~70%
                            ['name' => $translations['segments']['risk'][$currentLang] ?? $translations['segments']['risk']['kor'], 'color' => '#dc2626', 'range' => [71, 85]],     // 71~85%
                            ['name' => $translations['segments']['collapse'][$currentLang] ?? $translations['segments']['collapse']['kor'], 'color' => '#991b1b', 'range' => [86, 100]]     // 86~100%
                        ];
                        
                        // 현재 구간 찾기 (노화지수 기준)
                        $currentSegmentIndex = 5; // 기본값: 붕괴
                        foreach ($segments as $index => $segment) {
                            if ($agingIndex >= $segment['range'][0] && $agingIndex <= $segment['range'][1]) {
                                $currentSegmentIndex = $index;
                                break;
                            }
                        }
                        $currentSegment = $segments[$currentSegmentIndex];
                        
                        // 게이지 표시를 위한 역전된 백분율 (시각적 표현용)
                        $gaugePercentage = 100 - $agingIndex;
                    @endphp
                    
                    <!-- Chart.js 게이지 차트 컨테이너 -->
                    <div class="relative mx-auto" style="width: 100%; max-width: 400px; height: 250px;">
                        <canvas id="gaugeChart"></canvas>
                        
                        <!-- 중앙 텍스트 (하단 위치) -->
                        <div class="absolute bottom-0 left-0 right-0 flex justify-center pb-12">
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800">{{ $agingIndex }}%</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $translations['aging_index'][$currentLang] ?? $translations['aging_index']['kor'] }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 구간 라벨 -->
                    <div class="flex justify-between px-4 mt-2">
                        @foreach($segments as $index => $segment)
                            <div class="text-xs {{ $currentSegmentIndex === $index ? 'font-bold text-gray-800' : 'text-gray-400' }}">
                                {{ $segment['name'] }}
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- 점수 정보 -->
                    <div class="text-center mt-8">
                        <div class="mb-4">
                            <p class="text-2xl font-bold text-gray-800">{{ $translations['digital_aging_index'][$currentLang] ?? $translations['digital_aging_index']['kor'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $translations['actual_score'][$currentLang] ?? $translations['actual_score']['kor'] }}: {{ $actualScore }} / {{ $maxPossibleScore }} ({{ $agingIndex }}%)
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $translations['lower_better'][$currentLang] ?? $translations['lower_better']['kor'] }}
                            </p>
                        </div>
                        
                        <!-- 현재 상태 표시 -->
                        <div class="mb-6">
                            <span class="inline-flex items-center px-8 py-4 rounded-full text-xl font-bold shadow-lg bg-gradient-to-r from-gray-50 to-gray-100 border-2" 
                                  style="border-color: {{ $currentSegment['color'] }}; color: {{ $currentSegment['color'] }}">
                                {{ $currentSegment['name'] }} {{ $translations['status'][$currentLang] ?? $translations['status']['kor'] }}
                            </span>
                        </div>
                        
                        <!-- 상태별 설명 -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <p class="text-gray-700 leading-relaxed">
                                @if($agingIndex <= 15)
                                    @if($currentLang == 'eng')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">Optimal condition!</span> Your digital aging has barely progressed and you are in excellent health.
                                    @elseif($currentLang == 'chn')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">最佳状态！</span> 您的数字老化几乎没有进展，处于非常好的状态。
                                    @elseif($currentLang == 'hin')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">सर्वोत्तम स्थिति!</span> आपकी डिजिटल एजिंग बहुत कम हुई है और आप बहुत अच्छी स्थिति में हैं।
                                    @elseif($currentLang == 'arb')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">حالة مثالية!</span> الشيخوخة الرقمية لديك لم تتقدم تقريبًا وأنت في حالة ممتازة.
                                    @else
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">최적의 상태입니다!</span> 디지털 노화가 거의 진행되지 않은 매우 좋은 상태입니다.
                                    @endif
                                @elseif($agingIndex <= 30)
                                    @if($currentLang == 'eng')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">Excellent condition.</span> Digital aging is mild, and with a little more care, you can maintain optimal condition.
                                    @elseif($currentLang == 'chn')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">优秀状态。</span> 数字老化程度轻微，稍加管理即可保持最佳状态。
                                    @elseif($currentLang == 'hin')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">उत्कृष्ट स्थिति।</span> डिजिटल एजिंग हल्की है, और थोड़ी और देखभाल से आप इष्टतम स्थिति बनाए रख सकते हैं।
                                    @elseif($currentLang == 'arb')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">حالة ممتازة.</span> الشيخوخة الرقمية خفيفة، ومع المزيد من الرعاية يمكنك الحفاظ على الحالة المثلى.
                                    @else
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">우수한 상태입니다.</span> 디지털 노화가 경미한 수준이며, 조금만 더 관리하면 최적 상태를 유지할 수 있습니다.
                                    @endif
                                @elseif($agingIndex <= 50)
                                    @if($currentLang == 'eng')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">Good condition.</span> Digital aging is progressing but at a manageable level.
                                    @elseif($currentLang == 'chn')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">良好状态。</span> 数字老化正在进行中，但处于可控水平。
                                    @elseif($currentLang == 'hin')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">अच्छी स्थिति।</span> डिजिटल एजिंग प्रगति पर है लेकिन प्रबंधनीय स्तर पर है।
                                    @elseif($currentLang == 'arb')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">حالة جيدة.</span> الشيخوخة الرقمية تتقدم ولكن بمستوى يمكن التحكم فيه.
                                    @else
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">양호한 상태입니다.</span> 디지털 노화가 진행되고 있지만 관리 가능한 수준입니다.
                                    @endif
                                @elseif($agingIndex <= 70)
                                    @if($currentLang == 'eng')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">Requires attention.</span> Digital aging has progressed significantly and requires active management.
                                    @elseif($currentLang == 'chn')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">需要注意的状态。</span> 数字老化已经显著进展，需要积极管理。
                                    @elseif($currentLang == 'hin')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">ध्यान देने की आवश्यकता है।</span> डिजिटल एजिंग काफी आगे बढ़ चुकी है और सक्रिय प्रबंधन की आवश्यकता है।
                                    @elseif($currentLang == 'arb')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">يتطلب الانتباه.</span> تقدمت الشيخوخة الرقمية بشكل كبير وتتطلب إدارة فعالة.
                                    @else
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">주의가 필요한 상태입니다.</span> 디지털 노화가 상당히 진행되어 적극적인 관리가 필요합니다.
                                    @endif
                                @elseif($agingIndex <= 85)
                                    @if($currentLang == 'eng')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">Risk condition.</span> Digital aging is at a serious level and requires immediate professional help.
                                    @elseif($currentLang == 'chn')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">危险状态。</span> 数字老化已达到严重水平，需要立即寻求专业帮助。
                                    @elseif($currentLang == 'hin')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">जोखिम की स्थिति।</span> डिजिटल एजिंग गंभीर स्तर पर है और तुरंत पेशेवर मदद की आवश्यकता है।
                                    @elseif($currentLang == 'arb')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">حالة خطرة.</span> الشيخوخة الرقمية في مستوى خطير وتتطلب مساعدة مهنية فورية.
                                    @else
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">위험한 상태입니다.</span> 디지털 노화가 심각한 수준으로 즉시 전문가의 도움이 필요합니다.
                                    @endif
                                @else
                                    @if($currentLang == 'eng')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">Very serious condition.</span> Digital aging is at an extreme level. Please consult a specialist for active treatment.
                                    @elseif($currentLang == 'chn')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">非常严重的状态。</span> 数字老化达到极端水平，请务必咨询专家进行积极治疗。
                                    @elseif($currentLang == 'hin')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">बहुत गंभीर स्थिति।</span> डिजिटल एजिंग चरम स्तर पर है। कृपया सक्रिय उपचार के लिए विशेषज्ञ से परामर्श करें।
                                    @elseif($currentLang == 'arb')
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">حالة خطيرة جدا.</span> الشيخوخة الرقمية في مستوى متطرف. يرجى استشارة أخصائي للعلاج الفعال.
                                    @else
                                        <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">매우 심각한 상태입니다.</span> 디지털 노화가 극심한 수준으로 반드시 전문의와 상담하여 적극적인 치료를 받으세요.
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 카테고리별 분석 -->
            @if(!empty($categoryAnalysis))
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">{{ $translations['category_analysis'][$currentLang] ?? $translations['category_analysis']['kor'] }}</h2>
                    
                    @php
                        $categoryAnalysisDescription = $survey->getCategoryAnalysisDescription($currentLang);
                    @endphp
                    
                    @if($categoryAnalysisDescription)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="prose prose-sm prose-gray max-w-none">
                                {!! $categoryAnalysisDescription !!}
                            </div>
                        </div>
                    @endif
                    
                    @php
                        // 카테고리를 점수 기준으로 정렬 (응답이 있는 카테고리만)
                        $scoredCategories = collect($categoryAnalysis)->filter(function($cat) {
                            return $cat['percentage'] !== null && $cat['answered_count'] > 0;
                        })->sortByDesc('percentage')->values();
                        
                        $top3Categories = $scoredCategories->take(3);
                        $bottom3Categories = $scoredCategories->slice(-3)->reverse()->values();
                    @endphp
                    
                    @if($scoredCategories->count() >= 3)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <!-- Top 3 카테고리 -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $translations['excellent_areas'][$currentLang] ?? $translations['excellent_areas']['kor'] }}
                                </h3>
                                <div class="space-y-2">
                                    @foreach($top3Categories as $index => $category)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <span class="text-green-600 font-bold mr-2">{{ $index + 1 }}.</span>
                                                <span class="text-gray-700">F{{ collect($categoryAnalysis)->search(function($item) use ($category) { 
                                                    return $item['name'] === $category['name']; 
                                                }) + 1 }}. {{ $category['name'] }}</span>
                                            </div>
                                            <span class="font-semibold text-green-700">{{ $category['percentage'] }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Bottom 3 카테고리 -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-red-800 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $translations['improvement_areas'][$currentLang] ?? $translations['improvement_areas']['kor'] }}
                                </h3>
                                <div class="space-y-2">
                                    @foreach($bottom3Categories as $index => $category)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <span class="text-red-600 font-bold mr-2">{{ $index + 1 }}.</span>
                                                <span class="text-gray-700">F{{ collect($categoryAnalysis)->search(function($item) use ($category) { 
                                                    return $item['name'] === $category['name']; 
                                                }) + 1 }}. {{ $category['name'] }}</span>
                                            </div>
                                            <span class="font-semibold text-red-700">{{ $category['percentage'] }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="space-y-8 pt-8">
                        @foreach($categoryAnalysis as $index => $category)
                            <div>
                                <div class="flex justify-between mb-2">
                                    <div class="flex items-center">
                                        @php
                                            // 상태 뱃지 결정 (100%에 가까울수록 최적, 0%에 가까울수록 붕괴)
                                            $statusBadge = '';
                                            $badgeColor = '';
                                            if ($category['percentage'] !== null && $category['answered_count'] > 0) {
                                                if ($category['percentage'] >= 85) {
                                                    $statusBadge = $translations['segments']['optimal'][$currentLang] ?? $translations['segments']['optimal']['kor'];
                                                    $badgeColor = 'bg-green-100 text-green-800 border-green-300';
                                                } elseif ($category['percentage'] >= 70) {
                                                    $statusBadge = $translations['segments']['excellent'][$currentLang] ?? $translations['segments']['excellent']['kor'];
                                                    $badgeColor = 'bg-blue-100 text-blue-800 border-blue-300';
                                                } elseif ($category['percentage'] >= 50) {
                                                    $statusBadge = $translations['segments']['good'][$currentLang] ?? $translations['segments']['good']['kor'];
                                                    $badgeColor = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                                } elseif ($category['percentage'] >= 30) {
                                                    $statusBadge = $translations['segments']['caution'][$currentLang] ?? $translations['segments']['caution']['kor'];
                                                    $badgeColor = 'bg-orange-100 text-orange-800 border-orange-300';
                                                } elseif ($category['percentage'] >= 15) {
                                                    $statusBadge = $translations['segments']['risk'][$currentLang] ?? $translations['segments']['risk']['kor'];
                                                    $badgeColor = 'bg-red-100 text-red-800 border-red-300';
                                                } else {
                                                    $statusBadge = $translations['segments']['collapse'][$currentLang] ?? $translations['segments']['collapse']['kor'];
                                                    $badgeColor = 'bg-red-200 text-red-900 border-red-400';
                                                }
                                            }
                                        @endphp
                                        
                                        @if($statusBadge)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }} mr-2">
                                                {{ $statusBadge }}
                                            </span>
                                        @endif
                                        <span class="text-gray-500 font-semibold mr-2">F{{ $index + 1 }}.</span>
                                        <span class="text-gray-700 font-medium">{{ $category['name'] }}</span>
                                    </div>
                                    @if($category['percentage'] !== null)
                                        <span class="text-gray-600 font-semibold">{{ $category['percentage'] }}%</span>
                                    @else
                                        <span class="text-gray-400 text-sm">{{ $translations['no_response'][$currentLang] ?? $translations['no_response']['kor'] }}</span>
                                    @endif
                                </div>
                                
                                @if(!empty($category['description']))
                                    <p class="text-sm text-gray-600 mb-2">{{ $category['description'] }}</p>
                                @endif
                                <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                    @php
                                        // 카테고리별 노화지수에 따른 색상 결정 (역전된 백분율 사용)
                                        $categoryAgingIndex = 100 - $category['percentage'];
                                        if ($categoryAgingIndex <= 15) {
                                            $barColor = 'bg-gradient-to-r from-green-500 to-green-600'; // 최적
                                        } elseif ($categoryAgingIndex <= 30) {
                                            $barColor = 'bg-gradient-to-r from-blue-500 to-blue-600'; // 우수
                                        } elseif ($categoryAgingIndex <= 50) {
                                            $barColor = 'bg-gradient-to-r from-yellow-500 to-yellow-600'; // 양호
                                        } elseif ($categoryAgingIndex <= 70) {
                                            $barColor = 'bg-gradient-to-r from-orange-500 to-orange-600'; // 주의
                                        } elseif ($categoryAgingIndex <= 85) {
                                            $barColor = 'bg-gradient-to-r from-red-500 to-red-600'; // 위험
                                        } else {
                                            $barColor = 'bg-gradient-to-r from-red-800 to-red-900'; // 붕괴
                                        }
                                    @endphp
                                    @if($category['percentage'] !== null && $category['answered_count'] > 0)
                                        <div class="h-full rounded-full transition-all duration-1000 ease-out relative {{ $barColor }}" 
                                             style="width: {{ $category['percentage'] }}%">
                                            <div class="absolute inset-0 bg-white bg-opacity-20"></div>
                                        </div>
                                    @else
                                        <div class="h-full flex items-center justify-center text-xs text-gray-500">
                                            {{ $translations['no_response_data'][$currentLang] ?? $translations['no_response_data']['kor'] }}
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!empty($category['result_description']))
                                    <div class="mt-2 p-3 bg-gray-50 rounded text-sm text-gray-700">
                                        {{ $category['result_description'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                </div>
            @endif

            <!-- 액션 버튼 -->
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-center no-print">
                <a href="{{ route('surveys.index') }}" 
                   class="flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    {{ $translations['buttons']['survey_list'][$currentLang] ?? $translations['buttons']['survey_list']['kor'] }}
                </a>
                
                @auth
                    <a href="{{ route('recovery.dashboard') }}" 
                       class="flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        {{ $translations['buttons']['recovery_dashboard'][$currentLang] ?? $translations['buttons']['recovery_dashboard']['kor'] }}
                    </a>
                @endauth
                
                <button onclick="window.print()" 
                        class="flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    {{ $translations['buttons']['print_result'][$currentLang] ?? $translations['buttons']['print_result']['kor'] }}
                </button>
            </div>
            
            <!-- 면책 고지 (하단) -->
            <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4" @if($currentLang == 'arb') dir="rtl" @endif>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-gray-600 mt-0.5 {{ $currentLang == 'arb' ? 'ml-3' : 'mr-3' }} flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-gray-700 {{ $currentLang == 'arb' ? 'text-right' : '' }}">
                        <p class="font-medium mb-1">{{ $translations['disclaimer_title'][$currentLang] ?? $translations['disclaimer_title']['kor'] }}</p>
                        <p>{{ $translations['disclaimer_1'][$currentLang] ?? $translations['disclaimer_1']['kor'] }}</p>
                        <p class="mt-2">{{ $translations['disclaimer_2'][$currentLang] ?? $translations['disclaimer_2']['kor'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 게이지 데이터
            const gaugePercentage = {{ $gaugePercentage }};
            const segments = @json($segments);
            const currentSegmentIndex = {{ $currentSegmentIndex }};
            
            // 각 세그먼트별 데이터 준비 (6개 세그먼트만)
            const segmentData = [1, 1, 1, 1, 1, 1]; // 모두 동일한 크기
            const segmentColors = [];
            const segmentBorderColors = [];
            
            segments.forEach((segment, index) => {
                // 현재 값이 포함된 구간만 실제 색상, 나머지는 매우 옅은 색
                if (index === currentSegmentIndex) {
                    // 현재 구간만 진한 색상
                    segmentColors.push(segment.color);
                    segmentBorderColors.push(segment.color);
                } else {
                    // 나머지는 매우 옅은 색으로 (15% 불투명도)
                    segmentColors.push(segment.color + '26'); // 약 15% 불투명도 (hex)
                    segmentBorderColors.push(segment.color + '40'); // 약 25% 불투명도 (hex)
                }
            });
            
            // Chart.js 설정
            const ctx = document.getElementById('gaugeChart').getContext('2d');
            const gaugeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: segmentData,
                        backgroundColor: segmentColors,
                        borderColor: segmentBorderColors,
                        borderWidth: 1,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%', // 도넛 두께 조정
                    aspectRatio: 2,
                    layout: {
                        padding: {
                            bottom: 50 // 하단 여백 증가
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
            
            // 카테고리별 진행도 바 애니메이션
            const progressBars = document.querySelectorAll('.space-y-4 [style*="width"]');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });
    </script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    
    <script>
    // Add version stamp to the page
    document.addEventListener('DOMContentLoaded', function() {
        const versionStamp = document.createElement('div');
        versionStamp.className = 'mt-12 pt-6 border-t border-gray-200 text-center text-xs text-gray-500';
        versionStamp.innerHTML = `
            <div class="space-y-1">
                <div>Form ID: {{ $survey->id }} • Model Version: {{ config('app.version', '1.0.0') }} • Generated: {{ now()->format('Y-m-d H:i:s T') }}</div>
                <div>Response ID: {{ $response->id }} • Privacy: noindex, nofollow</div>
            </div>
        `;
        
        // Find the main container and append the stamp
        const mainContainer = document.querySelector('.max-w-4xl.mx-auto');
        if (mainContainer) {
            mainContainer.appendChild(versionStamp);
        }
    });
    
    // 이벤트 추적 함수
    function trackEvent(eventName, eventParams) {
        // Google Analytics 이벤트 추적
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, eventParams);
        }
        
        // 콘솔 로그 (디버깅용)
        console.log('Event tracked:', eventName, eventParams);
        
        // 페이지 로드 시 deep_offer_shown 이벤트 자동 발생
        if (eventName === 'deep_check_start') {
            // deep_start 이벤트는 클릭 시에만 발생
            return true;
        }
    }
    
    // 페이지 로드 시 deep_offer_shown 이벤트 발생
    document.addEventListener('DOMContentLoaded', function() {
        trackEvent('deep_offer_shown', { 
            survey_id: {{ $survey->parent_id ?? $survey->id }},
            page: 'results'
        });
    });
    </script>
</x-app-layout>
