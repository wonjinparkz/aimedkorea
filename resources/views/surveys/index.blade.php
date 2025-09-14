<x-app-layout>
    <div class="min-h-screen bg-white" x-data="{ 
        selectedSurvey: {{ $surveys->search(function($survey) { return $survey->id == 1; }) !== false ? $surveys->search(function($survey) { return $survey->id == 1; }) : 0 }}, 
        surveys: {{ $surveys->toJson() }},
        showQuestions: false,
        analysisType: 'simple',
        responses: {},
        frequencyResponses: {},
        currentPage: 0,
        maxReachedPage: 0,
        questionsPerPage: 3,
        savedResponses: {},
        savedFrequencyResponses: {},
        pageStartTime: null,
        pageTimestamps: {},
        showTimeWarning: false,
        minimumTimePerQuestion: 1500,
        
        get currentQuestions() {
            if (!this.surveys[this.selectedSurvey]) return [];
            if (this.analysisType === 'detailed' && this.surveys[this.selectedSurvey].detailed_questions) {
                return this.surveys[this.selectedSurvey].detailed_questions;
            }
            return this.surveys[this.selectedSurvey].questions || [];
        },
        
        get currentChecklistItems() {
            if (!this.surveys[this.selectedSurvey]) return [];
            if (this.analysisType === 'detailed' && this.surveys[this.selectedSurvey].detailed_checklist_items) {
                return this.surveys[this.selectedSurvey].detailed_checklist_items;
            }
            return this.surveys[this.selectedSurvey].checklist_items || [];
        },
        
        init() {
            // URL 파라미터 확인
            const urlParams = new URLSearchParams(window.location.search);
            const surveyId = urlParams.get('survey_id');
            const analysisTypeParam = urlParams.get('analysis_type');
            
            // survey_id가 있으면 해당 설문 선택
            if (surveyId) {
                let surveyIndex = this.surveys.findIndex(survey => survey.id == surveyId);
                if (surveyIndex !== -1) {
                    this.selectedSurvey = surveyIndex;
                    // analysis_type 파라미터가 있으면 설정
                    if (analysisTypeParam) {
                        this.analysisType = analysisTypeParam;
                        // 자동으로 질문 표시
                        setTimeout(() => {
                            this.showQuestions = true;
                            this.loadSavedData();
                            this.startPageTimer();
                            // 해당 설문으로 스크롤
                            const surveyElement = document.getElementById(`survey-${surveyId}`);
                            if (surveyElement) {
                                surveyElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }, 500);
                    }
                } else {
                    // ID가 1인 설문을 찾아서 선택 (기본값)
                    let defaultIndex = this.surveys.findIndex(survey => survey.id === 1);
                    this.selectedSurvey = defaultIndex !== -1 ? defaultIndex : 0;
                }
            } else {
                // ID가 1인 설문을 찾아서 선택 (기본값)
                let surveyIndex = this.surveys.findIndex(survey => survey.id === 1);
                this.selectedSurvey = surveyIndex !== -1 ? surveyIndex : 0;
            }
            
            // showQuestions 초기값을 false로 설정 (URL 파라미터가 없는 경우)
            if (!surveyId || !analysisTypeParam) {
                this.showQuestions = false;
            }
            
            // 페이지 시작 시간 기록 함수
            this.startPageTimer = () => {
                this.pageStartTime = Date.now();
                console.log(`[Timer] Page ${this.currentPage + 1} started at:`, new Date(this.pageStartTime).toISOString());
            };
            
            // 페이지 응답 시간 검증 함수
            this.validateResponseTime = () => {
                if (!this.pageStartTime) return true;
                
                const timeSpent = Date.now() - this.pageStartTime;
                const questionsOnPage = Math.min(this.questionsPerPage, 
                    this.currentQuestions.length - (this.currentPage * this.questionsPerPage));
                const minimumRequired = questionsOnPage * this.minimumTimePerQuestion;
                
                console.log(`[Timer] Page ${this.currentPage + 1} - Time spent: ${timeSpent}ms, Minimum required: ${minimumRequired}ms`);
                
                // 타임스탬프 기록
                if (!this.pageTimestamps[this.currentPage]) {
                    this.pageTimestamps[this.currentPage] = {};
                }
                this.pageTimestamps[this.currentPage].startTime = this.pageStartTime;
                this.pageTimestamps[this.currentPage].endTime = Date.now();
                this.pageTimestamps[this.currentPage].duration = timeSpent;
                this.pageTimestamps[this.currentPage].questions = questionsOnPage;
                
                // localStorage에 타임스탬프 저장
                if (this.selectedSurvey !== null) {
                    const timestampKey = `survey_${this.surveys[this.selectedSurvey].id}_timestamps`;
                    localStorage.setItem(timestampKey, JSON.stringify(this.pageTimestamps));
                }
                
                if (timeSpent < minimumRequired) {
                    this.showTimeWarning = true;
                    const remainingTime = Math.ceil((minimumRequired - timeSpent) / 1000);
                    console.warn(`[Timer] Too fast! Need ${remainingTime} more seconds`);
                    return false;
                }
                
                this.showTimeWarning = false;
                return true;
            };
            
            // 저장된 응답 불러오기 함수
            this.loadSavedData = () => {
                if (this.selectedSurvey !== null && this.surveys[this.selectedSurvey]) {
                    const storageKey = `survey_${this.surveys[this.selectedSurvey].id}_responses`;
                    const freqStorageKey = `survey_${this.surveys[this.selectedSurvey].id}_freq_responses`;
                    const pageKey = `survey_${this.surveys[this.selectedSurvey].id}_page`;
                    const maxPageKey = `survey_${this.surveys[this.selectedSurvey].id}_max_page`;
                    const timestampKey = `survey_${this.surveys[this.selectedSurvey].id}_timestamps`;
                    
                    const saved = localStorage.getItem(storageKey);
                    const savedFreq = localStorage.getItem(freqStorageKey);
                    const savedPage = localStorage.getItem(pageKey);
                    const savedMaxPage = localStorage.getItem(maxPageKey);
                    
                    if (saved) {
                        this.responses = JSON.parse(saved);
                        this.savedResponses = JSON.parse(saved);
                    }
                    if (savedFreq) {
                        this.frequencyResponses = JSON.parse(savedFreq);
                        this.savedFrequencyResponses = JSON.parse(savedFreq);
                    }
                    if (savedPage !== null) {
                        this.currentPage = parseInt(savedPage);
                    } else {
                        this.currentPage = 0;
                    }
                    if (savedMaxPage !== null) {
                        this.maxReachedPage = parseInt(savedMaxPage);
                    } else {
                        this.maxReachedPage = this.currentPage;
                    }
                    
                    // 타임스탬프 불러오기
                    const savedTimestamps = localStorage.getItem(timestampKey);
                    if (savedTimestamps) {
                        this.pageTimestamps = JSON.parse(savedTimestamps);
                    }
                }
            };
            
            // 초기 로드
            this.loadSavedData();
            
            // 선택된 설문 변경 시
            this.$watch('selectedSurvey', (value) => {
                if (value !== null) {
                    this.loadSavedData();
                }
            });
            
            // showQuestions 변경 시 currentPage 초기화
            this.$watch('showQuestions', (value) => {
                if (value && this.selectedSurvey !== null) {
                    const pageKey = `survey_${this.surveys[this.selectedSurvey].id}_page`;
                    const savedPage = localStorage.getItem(pageKey);
                    if (savedPage !== null) {
                        this.currentPage = parseInt(savedPage);
                    } else {
                        this.currentPage = 0;
                    }
                    // 페이지 타이머 시작
                    this.startPageTimer();
                }
            });
            
            // 응답 자동 저장
            this.$watch('responses', () => {
                if (this.selectedSurvey !== null && this.showQuestions) {
                    const storageKey = `survey_${this.surveys[this.selectedSurvey].id}_responses`;
                    localStorage.setItem(storageKey, JSON.stringify(this.responses));
                }
            });
            
            this.$watch('frequencyResponses', () => {
                if (this.selectedSurvey !== null && this.showQuestions) {
                    const freqStorageKey = `survey_${this.surveys[this.selectedSurvey].id}_freq_responses`;
                    localStorage.setItem(freqStorageKey, JSON.stringify(this.frequencyResponses));
                }
            });
            
            this.$watch('currentPage', () => {
                if (this.selectedSurvey !== null && this.showQuestions) {
                    const pageKey = `survey_${this.surveys[this.selectedSurvey].id}_page`;
                    const maxPageKey = `survey_${this.surveys[this.selectedSurvey].id}_max_page`;
                    localStorage.setItem(pageKey, this.currentPage.toString());
                    
                    // maxReachedPage 업데이트
                    if (this.currentPage > this.maxReachedPage) {
                        this.maxReachedPage = this.currentPage;
                        localStorage.setItem(maxPageKey, this.maxReachedPage.toString());
                    }
                    
                    // 페이지 변경 시 타이머 재시작
                    this.startPageTimer();
                    this.showTimeWarning = false;
                }
            });
        }
    }">
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
            
            <!-- 모바일용 상단 가로 스크롤 영역 -->
            <div class="block md:hidden mb-6">
                <div class="overflow-x-auto pb-2">
                    <div class="flex space-x-3" style="min-width: max-content;">
                        @foreach($surveys as $index => $survey)
                        <div class="cursor-pointer group flex-shrink-0" 
                             @click="selectedSurvey = {{ $index }}">
                            <div class="w-20 h-16 rounded-lg overflow-hidden bg-gray-200 border-2 border-transparent group-hover:border-indigo-300 transition-all duration-200"
                                 :class="{ 'border-indigo-500 ring-2 ring-indigo-500': selectedSurvey === {{ $index }} }">
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
                            <p class="text-xs text-center mt-1 text-gray-600" 
                               :class="{ 'text-indigo-600 font-semibold': selectedSurvey === {{ $index }} }">
                                {{ Str::limit($survey->title, 10) }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="flex gap-6">
                <!-- 데스크톱용 좌측 사이드바 -->
                <div class="hidden md:block w-24 flex-shrink-0">
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

                <!-- 메인 컨텐츠 (모바일에서 전체 너비) -->
                <div class="flex-1 min-w-0 w-full md:w-auto">
                    <!-- 설문 버튼 섹션 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($surveys as $index => $survey)
                        <button id="survey-{{ $survey->id }}"
                                @click="selectedSurvey = {{ $index }}" 
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
                                        $loginRequiredMsg = [
                                            'kor' => '로그인이 필요한 서비스입니다',
                                            'eng' => 'Login required for this service',
                                            'chn' => '此服务需要登录',
                                            'hin' => 'इस सेवा के लिए लॉगिन आवश्यक है',
                                            'arb' => 'تسجيل الدخول مطلوب لهذه الخدمة'
                                        ];
                                    @endphp
                                    <button @click="showQuestions = true; analysisType = 'simple'; loadSavedData();" 
                                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
                                        {{ $buttonLabels[$currentLang]['simple'] ?? $buttonLabels['kor']['simple'] }}
                                    </button>
                                    
                                    @guest
                                        <button @click="alert('{{ $loginRequiredMsg[$currentLang] ?? $loginRequiredMsg['kor'] }}'); window.location.href='{{ route('login') }}';" 
                                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                                            {{ $buttonLabels[$currentLang]['detailed'] ?? $buttonLabels['kor']['detailed'] }}
                                        </button>
                                    @else
                                        <button @click="
                                            if (surveys[selectedSurvey].has_detailed_version) {
                                                showQuestions = true; 
                                                analysisType = 'detailed'; 
                                                loadSavedData();
                                            } else {
                                                alert('심층 분석 문항이 아직 준비되지 않았습니다.');
                                            }
                                        " 
                                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                                            {{ $buttonLabels[$currentLang]['detailed'] ?? $buttonLabels['kor']['detailed'] }}
                                        </button>
                                    @endguest
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
                                <h2 class="text-xl font-bold text-gray-900 mb-4" x-text="surveys[selectedSurvey].title"></h2>
                                
                                <!-- 진행률 표시 -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">진행 상황</span>
                                        <span class="text-sm font-medium text-indigo-600">
                                            F<span x-text="currentPage + 1"></span> / F<span x-text="Math.ceil(currentQuestions.length / questionsPerPage)"></span>
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-500" 
                                             :style="`width: ${((maxReachedPage + 1) / Math.ceil(currentQuestions.length / questionsPerPage)) * 100}%`"></div>
                                    </div>
                                    
                                    <!-- 시간 경고 메시지 -->
                                    <div x-show="showTimeWarning" x-transition
                                         class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-yellow-800">
                                                    너무 빨리 응답하고 있습니다
                                                </p>
                                                <p class="text-xs text-yellow-600 mt-1">
                                                    정확한 분석을 위해 각 문항을 신중히 읽고 답변해주세요.
                                                    최소 응답 시간: 문항당 1.5초
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 페이지 인디케이터 -->
                                    <div class="flex items-center justify-center mt-4 space-x-2">
                                        <template x-for="pageNum in Math.ceil(currentQuestions.length / questionsPerPage)" :key="pageNum">
                                            <button type="button" 
                                                    @click="() => {
                                                        const targetPage = pageNum - 1;
                                                        // maxReachedPage까지는 자유롭게 이동 가능
                                                        if (targetPage <= maxReachedPage) {
                                                            currentPage = targetPage;
                                                        } else {
                                                            // maxReachedPage를 넘어서는 페이지로는 이동 불가
                                                            alert('이전 페이지의 모든 문항에 답변해주세요.');
                                                        }
                                                    }"
                                                    :disabled="(() => {
                                                        const targetPage = pageNum - 1;
                                                        // maxReachedPage까지는 활성화, 그 이후는 비활성화
                                                        return targetPage > maxReachedPage;
                                                    })()"
                                                    :class="(() => {
                                                        const targetPage = pageNum - 1;
                                                        if (currentPage === targetPage) {
                                                            return 'bg-indigo-600 border-indigo-600 text-white';
                                                        }
                                                        
                                                        // maxReachedPage를 넘는 페이지는 비활성화 스타일
                                                        if (targetPage > maxReachedPage) {
                                                            return 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed opacity-50';
                                                        }
                                                        
                                                        return 'bg-white border-gray-300 text-gray-600 hover:border-indigo-400';
                                                    })()"
                                                    class="px-2 py-1 rounded-full border-2 text-sm font-medium transition-all duration-300">
                                                    F<span x-text="pageNum"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-6">
                                    @php
                                        $currentLang = session('locale', 'kor');
                                        $analysisLabels = [
                                            'kor' => ['simple' => '간편 분석', 'detailed' => '심층 분석'],
                                            'eng' => ['simple' => 'Simple Analysis', 'detailed' => 'Detailed Analysis'],
                                            'chn' => ['simple' => '简单分析', 'detailed' => '详细分析'],
                                            'hin' => ['simple' => 'सरल विश्लेषण', 'detailed' => 'विस्तृत विश्लेषण'],
                                            'arb' => ['simple' => 'تحليل بسيط', 'detailed' => 'تحليل مفصل']
                                        ];
                                    @endphp
                                    <span x-text="analysisType === 'simple' ? '{{ $analysisLabels[$currentLang]['simple'] ?? $analysisLabels['kor']['simple'] }}' : '{{ $analysisLabels[$currentLang]['detailed'] ?? $analysisLabels['kor']['detailed'] }}'"></span>
                                </p>
                                
                                <form method="POST" :action="`/surveys/${analysisType === 'detailed' && surveys[selectedSurvey].detailed_survey_id ? surveys[selectedSurvey].detailed_survey_id : surveys[selectedSurvey].id}/responses`">
                                    @csrf
                                    <input type="hidden" name="analysis_type" :value="analysisType">
                                    
                                    <!-- 현재 페이지에 표시되지 않은 모든 응답을 hidden input으로 저장 -->
                                    <template x-for="(response, index) in responses" :key="`hidden-response-${index}`">
                                        <template x-if="currentPage === null || index < currentPage * questionsPerPage || index >= (currentPage + 1) * questionsPerPage">
                                            <input type="hidden" :name="`responses[${index}]`" :value="response">
                                        </template>
                                    </template>
                                    
                                    
                                    <!-- 간편 분석 - 페이지네이션 적용 -->
                                    <template x-if="analysisType === 'simple' && surveys[selectedSurvey] && currentQuestions">
                                        <div class="space-y-6">
                                            <template x-for="(question, qIndex) in currentQuestions.slice(currentPage * questionsPerPage, Math.min((currentPage + 1) * questionsPerPage, currentQuestions.length))" :key="`simple-${currentQuestions.indexOf(question)}`">
                                                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                                    <div class="inline-block min-w-full align-middle">
                                                        <table class="w-full min-w-[320px] sm:min-w-[500px]">
                                                        <tbody>
                                                            <!-- 질문 행 -->
                                                            <tr class="bg-gray-50">
                                                                <td class="p-4">
                                                                    <div class="flex items-start">
                                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full text-xs font-semibold mr-3 flex-shrink-0" x-text="currentQuestions.indexOf(question) + 1"></span>
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
                                                                                           :name="`responses[${currentQuestions.indexOf(question)}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[currentQuestions.indexOf(question)]"
                                                                                           :checked="responses[currentQuestions.indexOf(question)] == oIndex"
                                                                                           @change="$nextTick(() => { responses = {...responses} })"
                                                                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 mr-2">
                                                                                    <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                                </label>
                                                                            </template>
                                                                        </template>
                                                                        <template x-if="!question.has_specific_checklist">
                                                                            <template x-for="(option, oIndex) in currentChecklistItems" :key="oIndex">
                                                                                <label class="inline-flex items-center cursor-pointer">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${currentQuestions.indexOf(question)}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[currentQuestions.indexOf(question)]"
                                                                                           :checked="responses[currentQuestions.indexOf(question)] == oIndex"
                                                                                           @change="$nextTick(() => { responses = {...responses} })"
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
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- 심층 분석 - 페이지네이션 적용 (빈도 평가 제거) -->
                                    <template x-if="analysisType === 'detailed' && surveys[selectedSurvey] && currentQuestions">
                                        <div class="space-y-6">
                                            <template x-for="(question, qIndex) in currentQuestions.slice(currentPage * questionsPerPage, Math.min((currentPage + 1) * questionsPerPage, currentQuestions.length))" :key="`detailed-${currentQuestions.indexOf(question)}`">
                                                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                                    <div class="inline-block min-w-full align-middle">
                                                        <table class="w-full min-w-[320px] sm:min-w-[500px]">
                                                        <tbody>
                                                            <!-- 질문 행 -->
                                                            <tr class="bg-gradient-to-r from-green-50 to-green-100">
                                                                <td class="p-4">
                                                                    <div class="flex items-start">
                                                                        <span class="inline-flex items-center justify-center w-6 h-6 bg-green-600 text-white rounded-full text-xs font-semibold mr-3 flex-shrink-0" x-text="currentQuestions.indexOf(question) + 1"></span>
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
                                                                                <label class="inline-flex items-center cursor-pointer hover:bg-green-50 px-3 py-2 rounded-lg transition-colors">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${currentQuestions.indexOf(question)}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[currentQuestions.indexOf(question)]"
                                                                                           :checked="responses[currentQuestions.indexOf(question)] == oIndex"
                                                                                           @change="$nextTick(() => { responses = {...responses} })"
                                                                                           class="w-4 h-4 text-green-600 focus:ring-green-500 mr-2">
                                                                                    <span class="text-sm text-gray-600" x-text="option.label"></span>
                                                                                </label>
                                                                            </template>
                                                                        </template>
                                                                        <template x-if="!question.has_specific_checklist">
                                                                            <template x-for="(option, oIndex) in currentChecklistItems" :key="oIndex">
                                                                                <label class="inline-flex items-center cursor-pointer hover:bg-green-50 px-3 py-2 rounded-lg transition-colors">
                                                                                    <input type="radio" 
                                                                                           :name="`responses[${currentQuestions.indexOf(question)}]`" 
                                                                                           :value="oIndex"
                                                                                           x-model="responses[currentQuestions.indexOf(question)]"
                                                                                           :checked="responses[currentQuestions.indexOf(question)] == oIndex"
                                                                                           @change="$nextTick(() => { responses = {...responses} })"
                                                                                           class="w-4 h-4 text-green-600 focus:ring-green-500 mr-2">
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
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- 네비게이션 및 제출 버튼 -->
                                    <div class="mt-8">
                                        <div class="flex justify-between items-center">
                                            <!-- 이전 버튼 -->
                                            <button type="button" 
                                                    @click="currentPage = Math.max(0, currentPage - 1)"
                                                    :disabled="currentPage === 0"
                                                    :class="currentPage === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                                    class="flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                                이전
                                            </button>
                                            
                                            <!-- 취소 버튼 -->
                                            <button type="button" 
                                                    @click="showQuestions = false; currentPage = 0; maxReachedPage = 0; pageTimestamps = {}; showTimeWarning = false; localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_responses`); localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_freq_responses`); localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_page`); localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_max_page`); localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_timestamps`); responses = {}; frequencyResponses = {};" 
                                                    class="px-6 py-3 text-gray-500 hover:text-gray-700 transition-all duration-200">
                                                취소
                                            </button>
                                            
                                            <!-- 다음/제출 버튼 -->
                                            <template x-if="currentPage < Math.ceil(currentQuestions.length / questionsPerPage) - 1">
                                                <button type="button" 
                                                        @click="() => {
                                                            const startIdx = currentPage * questionsPerPage;
                                                            const endIdx = Math.min((currentPage + 1) * questionsPerPage, currentQuestions.length);
                                                            let allAnswered = true;
                                                            for (let i = startIdx; i < endIdx; i++) {
                                                                if (!responses.hasOwnProperty(i)) {
                                                                    allAnswered = false;
                                                                    break;
                                                                }
                                                            }
                                                            if (!allAnswered) {
                                                                alert('현재 페이지의 모든 문항에 답변해주세요.');
                                                                return;
                                                            }
                                                            
                                                            // 시간 검증
                                                            if (!validateResponseTime()) {
                                                                const questionsOnPage = Math.min(questionsPerPage, currentQuestions.length - (currentPage * questionsPerPage));
                                                                const timeSpent = Date.now() - pageStartTime;
                                                                const minimumRequired = questionsOnPage * minimumTimePerQuestion;
                                                                const remainingTime = Math.ceil((minimumRequired - timeSpent) / 1000);
                                                                
                                                                showTimeWarning = true;
                                                                alert(`너무 빨리 응답하고 있습니다.\n\n각 문항을 신중히 읽고 답변해주세요.\n최소 ${remainingTime}초 더 필요합니다.`);
                                                                return;
                                                            }
                                                            
                                                            currentPage = Math.min(Math.ceil(currentQuestions.length / questionsPerPage) - 1, currentPage + 1);
                                                        }"
                                                        class="flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500">
                                                    다음
                                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            </template>
                                            
                                            <template x-if="currentPage === Math.ceil(currentQuestions.length / questionsPerPage) - 1">
                                                <button type="submit" 
                                                        @click="(e) => {
                                                            const allAnswered = currentQuestions.every((q, idx) => responses.hasOwnProperty(idx));
                                                            
                                                            if (!allAnswered) {
                                                                e.preventDefault();
                                                                alert('모든 문항에 답변해주세요.');
                                                                return;
                                                            }
                                                            
                                                            // 마지막 페이지 시간 검증
                                                            if (!validateResponseTime()) {
                                                                e.preventDefault();
                                                                const questionsOnPage = Math.min(questionsPerPage, currentQuestions.length - (currentPage * questionsPerPage));
                                                                const timeSpent = Date.now() - pageStartTime;
                                                                const minimumRequired = questionsOnPage * minimumTimePerQuestion;
                                                                const remainingTime = Math.ceil((minimumRequired - timeSpent) / 1000);
                                                                
                                                                showTimeWarning = true;
                                                                alert(`너무 빨리 응답하고 있습니다.\n\n각 문항을 신중히 읽고 답변해주세요.\n최소 ${remainingTime}초 더 필요합니다.`);
                                                                return;
                                                            }
                                                            
                                                            // 전체 타임스탬프 로그 출력
                                                            console.log('[Survey Complete] Total timestamps:', pageTimestamps);
                                                            
                                                            // 제출 성공 시 로컬 스토리지 정리
                                                            localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_responses`);
                                                            localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_freq_responses`);
                                                            localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_page`);
                                                            localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_max_page`);
                                                            localStorage.removeItem(`survey_${surveys[selectedSurvey].id}_timestamps`);
                                                        }"
                                                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    설문 완료하기
                                                </button>
                                            </template>
                                        </div>
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
