<x-app-layout>
    <div class="min-h-screen bg-white" x-data="{ 
        selectedSurvey: {{ $surveys->search(function($survey) { return $survey->id == 1; }) !== false ? $surveys->search(function($survey) { return $survey->id == 1; }) : 0 }}, 
        surveys: {{ $surveys->toJson() }},
        showQuestions: true,
        analysisType: 'simple',
        responses: {},
        frequencyResponses: {}
    }" x-init="
        // ID가 1인 설문을 찾아서 선택
        let surveyIndex = surveys.findIndex(survey => survey.id === 1);
        selectedSurvey = surveyIndex !== -1 ? surveyIndex : 0;
    ">
        <!-- 히어로 섹션 -->
        <div class="relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                        디지털 인체기능 노화 분석 시스템
                    </h1>
                    <p class="text-lg text-gray-600">
                        (Digital Functional Aging System, DFAS)
                    </p>
                </div>
            </div>
        </div>

        <!-- 메인 컨텐츠 영역 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($surveys->count() > 0)
            <div class="flex gap-6">
                <!-- 좌측 사이드바 -->
                <div class="w-24 flex-shrink-0">
                    <div class="sticky top-8 space-y-3">
                        @foreach($surveys as $index => $survey)
                        <div class="cursor-pointer group" 
                             @click="selectedSurvey = {{ $index }}">
                            <div class="w-20 h-16 rounded-lg overflow-hidden bg-gray-200 border-2 border-transparent group-hover:border-indigo-300 transition-all duration-200"
                                 :class="{ 'border-indigo-500': selectedSurvey === {{ $index }} }">
                                @if($survey->survey_image)
                                    <img src="{{ asset('storage/' . $survey->survey_image) }}" 
                                         alt="{{ $survey->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- 우측 메인 컨텐츠 -->
                <div class="flex-1 min-w-0">
                    <!-- 설문 버튼 섹션 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($surveys as $index => $survey)
                        <button @click="selectedSurvey = {{ $index }}" 
                                :class="{ 'ring-2 ring-indigo-500 bg-indigo-600 text-white': selectedSurvey === {{ $index }}, 
                                          'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200': selectedSurvey !== {{ $index }} }"
                                class="relative p-4 rounded-lg transition-all duration-300 focus:outline-none text-left">
                            <h3 class="text-sm font-medium">{{ $survey->title }}</h3>
                        </button>
                        @endforeach
                    </div>
                
                <!-- 안내 메시지 -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                        @php
                            $currentLang = session('locale', 'kor');
                            $disclaimerTexts = [
                                'kor' => '본 자가 진단은 질병 진단이나 의학적 처방이 아닌, 디지털 노화에 대한 사용자의 뇌·시각·청각 등 기능 회복 루틴 설계를 위한 웰니스 기능 점검 도구입니다.',
                                'eng' => 'This self-assessment is not a disease diagnosis or medical prescription, but a wellness function check tool for designing functional recovery routines for brain, vision, hearing, etc. related to digital aging.',
                                'chn' => '本自我诊断不是疾病诊断或医学处方，而是为设计用户大脑、视觉、听觉等功能恢复程序而开发的数字老化健康功能检查工具。',
                                'hin' => 'यह स्व-मूल्यांकन रोग निदान या चिकित्सा नुस्खा नहीं है, बल्कि डिजिटल बुढ़ापे से संबंधित मस्तिष्क, दृष्टि, श्रवण आदि के लिए कार्यात्मक वसूली दिनचर्या डिजाइन करने के लिए एक वेलनेस फ़ंक्शन जांच उपकरण है।',
                                'arb' => 'هذا التقييم الذاتي ليس تشخيصًا للمرض أو وصفة طبية، بل أداة فحص وظيفي للعافية لتصميم روتينات استعادة الوظائف للدماغ والرؤية والسمع وما إلى ذلك المتعلقة بالشيخوخة الرقمية.'
                            ];
                            echo $disclaimerTexts[$currentLang] ?? $disclaimerTexts['kor'];
                        @endphp
                    </p>
                </div>
                
                <!-- 선택된 설문 설명 섹션 -->
                <div class="mt-8" x-show="selectedSurvey !== null" x-transition>
                    <div class="p-6">
                        <template x-if="selectedSurvey !== null">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-4" x-text="surveys[selectedSurvey].title"></h2>
                                <p class="text-gray-600 mb-6 whitespace-pre-line" x-text="surveys[selectedSurvey].description"></p>
                                
                                <div class="flex items-center justify-center space-x-4">
                                    @php
                                        $currentLang = session('locale', 'kor');
                                        $buttonLabels = [
                                            'kor' => ['simple' => '간편 분석', 'detailed' => '심층 분석'],
                                            'eng' => ['simple' => 'Simple Analysis', 'detailed' => 'Detailed Analysis'],
                                            'chn' => ['simple' => '简单分析', 'detailed' => '详细分析'],
                                            'hin' => ['simple' => 'सरल विश्लेषण', 'detailed' => 'विस्तृत विश्लेषण'],
                                            'arb' => ['simple' => 'تحليل بسيط', 'detailed' => 'تحليل مفصل']
                                        ];
                                    @endphp
                                    <button @click="showQuestions = true; analysisType = 'simple'" 
                                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
                                        {{ $buttonLabels[$currentLang]['simple'] ?? $buttonLabels['kor']['simple'] }}
                                    </button>
                                    
                                    <button @click="showQuestions = true; analysisType = 'detailed'" 
                                           class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                                        {{ $buttonLabels[$currentLang]['detailed'] ?? $buttonLabels['kor']['detailed'] }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- 설문 문항 섹션 -->
                <div id="survey-questions" class="mt-8" x-show="showQuestions && selectedSurvey !== null" x-transition x-cloak>
                    <div class="p-6">
                        <template x-if="selectedSurvey !== null && showQuestions">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-6" x-text="surveys[selectedSurvey].title"></h2>
                                <p class="text-sm text-gray-600 mb-6">
                                    @php
                                        $currentLang = session('locale', 'kor');
                                        $analysisLabels = [
                                            'kor' => ['simple' => '간편 분석', 'detailed' => '심층 분석 (최근 1주 기준)'],
                                            'eng' => ['simple' => 'Simple Analysis', 'detailed' => 'Detailed Analysis (Past 1 Week)'],
                                            'chn' => ['simple' => '简单分析', 'detailed' => '详细分析（近1周）'],
                                            'hin' => ['simple' => 'सरल विश्लेषण', 'detailed' => 'विस्तृत विश्लेषण (पिछले 1 सप्ताह)'],
                                            'arb' => ['simple' => 'تحليل بسيط', 'detailed' => 'تحليل مفصل (الأسبوع الماضي)']
                                        ];
                                    @endphp
                                    <span x-text="analysisType === 'simple' ? '{{ $analysisLabels[$currentLang]['simple'] ?? $analysisLabels['kor']['simple'] }}' : '{{ $analysisLabels[$currentLang]['detailed'] ?? $analysisLabels['kor']['detailed'] }}'"></span>
                                </p>
                                
                                <form method="POST" :action="`/surveys/${surveys[selectedSurvey].id}/responses`">
                                    @csrf
                                    <input type="hidden" name="analysis_type" :value="analysisType">
                                    
                                    <!-- 간편 분석 - 기존 형태 -->
                                    <template x-if="analysisType === 'simple'">
                                        <div class="space-y-6">
                                            <template x-for="(question, qIndex) in surveys[selectedSurvey].questions" :key="qIndex">
                                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                                                    <table class="w-full">
                                                        <tbody>
                                                            <!-- 질문 행 -->
                                                            <tr class="bg-gray-50">
                                                                <td class="p-4">
                                                                    <div class="flex items-start">
                                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-xs font-semibold mr-3 flex-shrink-0" x-text="qIndex + 1"></span>
                                                                        <span class="text-sm font-medium text-gray-700" x-text="question.label"></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <!-- 선택지 행 -->
                                                            <tr>
                                                                <td class="p-4">
                                                                    <div class="flex flex-wrap gap-4">
                                                                        <template x-if="question.has_specific_checklist && question.specific_checklist_items">
                                                                            <template x-for="(option, oIndex) in question.specific_checklist_items" :key="oIndex">
                                                                                <label class="inline-flex items-center cursor-pointer">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${qIndex}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[qIndex]"
                                                                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                                                    <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                                </label>
                                                                            </template>
                                                                        </template>
                                                                        <template x-if="!question.has_specific_checklist">
                                                                            <template x-for="(option, oIndex) in surveys[selectedSurvey].checklist_items" :key="oIndex">
                                                                                <label class="inline-flex items-center cursor-pointer">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${qIndex}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[qIndex]"
                                                                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                                                    <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                                </label>
                                                                            </template>
                                                                        </template>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- 심층 분석 - 기존 형태 + 빈도 평가 추가 -->
                                    <template x-if="analysisType === 'detailed'">
                                        <div class="space-y-6">
                                            <template x-for="(question, qIndex) in surveys[selectedSurvey].questions" :key="qIndex">
                                                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                                                    <table class="w-full">
                                                        <tbody>
                                                            <!-- 질문 행 -->
                                                            <tr class="bg-gray-50">
                                                                <td class="p-4">
                                                                    <div class="flex items-start">
                                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-xs font-semibold mr-3 flex-shrink-0" x-text="qIndex + 1"></span>
                                                                        <span class="text-sm font-medium text-gray-700" x-text="question.label"></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <!-- 선택지 행 -->
                                                            <tr>
                                                                <td class="p-4">
                                                                    <div class="flex flex-wrap gap-4">
                                                                        <template x-if="question.has_specific_checklist && question.specific_checklist_items">
                                                                            <template x-for="(option, oIndex) in question.specific_checklist_items" :key="oIndex">
                                                                                <label class="inline-flex items-center cursor-pointer">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${qIndex}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[qIndex]"
                                                                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                                                    <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                                </label>
                                                                            </template>
                                                                        </template>
                                                                        <template x-if="!question.has_specific_checklist">
                                                                            <template x-for="(option, oIndex) in surveys[selectedSurvey].checklist_items" :key="oIndex">
                                                                                <label class="inline-flex items-center cursor-pointer">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${qIndex}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[qIndex]"
                                                                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                                                    <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                                </label>
                                                                            </template>
                                                                        </template>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <!-- 빈도 평가 질문 행 -->
                                                            <tr class="bg-gray-50 border-t border-gray-200">
                                                                <td class="p-4">
                                                                    <div class="flex items-start">
                                                                        <span class="text-sm font-medium text-gray-700">
                                                                            @php
                                                                                $currentLang = session('locale', 'kor');
                                                                                $frequencyLabels = [
                                                                                    'kor' => '빈도 평가 (최근 1주 기준)',
                                                                                    'eng' => 'Frequency Assessment (Past 1 Week)',
                                                                                    'chn' => '频率评估（近1周）',
                                                                                    'hin' => 'आवृत्ति मूल्यांकन (पिछले 1 सप्ताह)',
                                                                                    'arb' => 'تقييم التكرار (الأسبوع الماضي)'
                                                                                ];
                                                                                echo $frequencyLabels[$currentLang] ?? $frequencyLabels['kor'];
                                                                            @endphp
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <!-- 빈도 평가 선택지 행 -->
                                                            <tr>
                                                                <td class="p-4">
                                                                    <div class="flex flex-wrap gap-4">
                                                                        <template x-for="(option, oIndex) in surveys[selectedSurvey].frequency_items" :key="oIndex">
                                                                            <label class="inline-flex items-center cursor-pointer">
                                                                                <input type="radio" 
                                                                                       :name="`frequency_responses[${qIndex}]`" 
                                                                                       :value="oIndex"
                                                                                       x-model="frequencyResponses[qIndex]"
                                                                                       class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                                                <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                            </label>
                                                                        </template>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- 제출 버튼 -->
                                    <div class="mt-8 flex justify-between items-center">
                                        <button type="button" 
                                                @click="showQuestions = false" 
                                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
                                            취소
                                        </button>
                                        
                                        <button type="submit" 
                                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            설문 완료하기
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
                </div> <!-- 우측 메인 컨텐츠 끝 -->
            </div> <!-- flex container 끝 -->
            @else
                <!-- 설문이 없을 때 -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('no_surveys_yet') }}</h3>
                    <p class="mt-2 text-gray-500">관리자가 설문을 등록하면 여기서 확인할 수 있습니다.</p>
                </div>
            @endif
        </div>

        <!-- 안내 섹션 -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-gray-50 rounded-2xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">이런 분들께 추천합니다</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">건강 상태가 궁금하신 분</h4>
                        <p class="text-sm text-gray-600">간단한 자가진단으로 현재 상태를 확인하세요</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">예방이 중요하다고 생각하는 분</h4>
                        <p class="text-sm text-gray-600">조기 발견과 예방으로 건강을 지키세요</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">맞춤형 솔루션을 원하는 분</h4>
                        <p class="text-sm text-gray-600">개인별 맞춤 건강 관리 방법을 제안합니다</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
