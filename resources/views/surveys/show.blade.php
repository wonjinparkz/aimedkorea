<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">{{ $survey->title }}</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $survey->description }}</p>
            </div>

            <!-- 진행도 표시 -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700">진행 상황</span>
                    <span class="text-sm font-medium text-indigo-600" id="progress-text">
                        <span id="answered-count">0</span> / {{ count($survey->questions) }} 문항 완료
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                    <div id="progress-bar" class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full transition-all duration-500 ease-out" style="width: 0%">
                        <div class="h-full bg-white bg-opacity-30 animate-pulse"></div>
                    </div>
                </div>
                <!-- 페이지 인디케이터 -->
                <div class="flex items-center justify-center mt-4 space-x-2" id="page-indicators">
                    @php
                        $totalPages = ceil(count($survey->questions) / 3);
                    @endphp
                    @for($i = 0; $i < $totalPages; $i++)
                        <button type="button" 
                                class="page-indicator w-10 h-10 rounded-full border-2 transition-all duration-300 {{ $i === 0 ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-300 text-gray-600 hover:border-indigo-400' }}"
                                data-page="{{ $i }}">
                            {{ $i + 1 }}
                        </button>
                    @endfor
                </div>
            </div>

            <!-- 설문 폼 -->
            <form id="survey-form" action="{{ route('surveys.store', $survey) }}" method="POST">
                @csrf
                
                <!-- 페이지별 문항 그룹 -->
                @php
                    $questionsPerPage = 3;
                    $totalPages = ceil(count($survey->questions) / $questionsPerPage);
                @endphp
                
                @for($page = 0; $page < $totalPages; $page++)
                    <div class="page-container {{ $page === 0 ? '' : 'hidden' }}" data-page="{{ $page }}">
                        <div class="space-y-6">
                            @php
                                $startIndex = $page * $questionsPerPage;
                                $endIndex = min($startIndex + $questionsPerPage, count($survey->questions));
                            @endphp
                            
                            @for($index = $startIndex; $index < $endIndex; $index++)
                                @php
                                    $question = $survey->questions[$index];
                                @endphp
                                <div class="question-item bg-white rounded-2xl shadow-lg p-6 transform transition-all duration-500" data-question-index="{{ $index }}">
                                    <!-- 문항 번호와 내용 -->
                                    <div class="mb-6">
                                        <div class="flex items-start">
                                            <span class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full font-bold text-lg shadow-md flex-shrink-0">
                                                {{ $index + 1 }}
                                            </span>
                                            <h2 class="ml-4 text-lg md:text-xl font-semibold text-gray-800 leading-relaxed">
                                                {{ $question['label'] }}
                                            </h2>
                                        </div>
                                    </div>

                                    <!-- 선택지 -->
                                    <div class="space-y-2 ml-14">
                                        @php
                                            if (isset($question['has_specific_checklist']) && $question['has_specific_checklist']) {
                                                $options = $question['specific_checklist_items'];
                                            } else {
                                                $options = $survey->frequency_items ?: $survey->checklist_items;
                                            }
                                        @endphp
                                        
                                        @foreach($options as $optionIndex => $option)
                                            <label class="block cursor-pointer group">
                                                <input type="radio" 
                                                       name="responses[{{ $index }}]" 
                                                       value="{{ $optionIndex }}" 
                                                       class="sr-only peer question-radio"
                                                       data-question="{{ $index }}"
                                                       required>
                                                <div class="relative border-2 border-gray-200 rounded-lg p-4 hover:border-indigo-400 hover:shadow-md peer-checked:border-indigo-600 peer-checked:bg-gradient-to-r peer-checked:from-indigo-50 peer-checked:to-purple-50 transition-all duration-300">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-5 h-5 border-2 border-gray-300 rounded-full relative transition-all duration-300 peer-checked:border-transparent peer-checked:bg-gradient-to-r peer-checked:from-indigo-600 peer-checked:to-purple-600 group-hover:border-gray-400">
                                                            <div class="absolute inset-1 bg-white rounded-full scale-0 transition-transform duration-300 peer-checked:scale-100"></div>
                                                        </div>
                                                        <span class="ml-3 text-gray-700 text-sm peer-checked:text-gray-900 peer-checked:font-semibold transition-colors duration-300">
                                                            {{ $option['label'] }}
                                                        </span>
                                                    </div>
                                                    <!-- 점수 배지 -->
                                                    <div class="absolute top-3 right-3 px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full peer-checked:bg-indigo-100 peer-checked:text-indigo-700 transition-all duration-300">
                                                        {{ $option['score'] }}점
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endfor

                <!-- 네비게이션 버튼 -->
                <div class="mt-8 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <button type="button" 
                            id="prev-btn" 
                            class="flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300"
                            disabled>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        이전 문항
                    </button>

                    <a href="{{ route('surveys.index') }}" 
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200 underline-offset-4 hover:underline">
                        설문 목록으로
                    </a>

                    <button type="button" 
                            id="next-btn" 
                            class="flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 shadow-lg">
                        다음 문항
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <button type="submit" 
                            id="submit-btn" 
                            class="hidden flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        설문 완료하기
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const surveyId = {{ $survey->id }};
            const totalQuestions = {{ count($survey->questions) }};
            const questionsPerPage = 3;
            const totalPages = Math.ceil(totalQuestions / questionsPerPage);
            
            const form = document.getElementById('survey-form');
            const pageContainers = document.querySelectorAll('.page-container');
            const pageIndicators = document.querySelectorAll('.page-indicator');
            const progressBar = document.getElementById('progress-bar');
            const answeredCount = document.getElementById('answered-count');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            
            let currentPage = 0;
            let responses = {};
            
            // 로컬 스토리지 키
            const storageKey = `survey_${surveyId}_responses`;
            const pageKey = `survey_${surveyId}_page`;
            
            // 저장된 응답 불러오기
            function loadSavedResponses() {
                const saved = localStorage.getItem(storageKey);
                const savedPage = localStorage.getItem(pageKey);
                
                if (saved) {
                    responses = JSON.parse(saved);
                    // 저장된 응답 복원
                    Object.entries(responses).forEach(([questionIndex, value]) => {
                        const radio = document.querySelector(`input[name="responses[${questionIndex}]"][value="${value}"]`);
                        if (radio) {
                            radio.checked = true;
                        }
                    });
                }
                
                if (savedPage !== null) {
                    currentPage = parseInt(savedPage);
                }
                
                updateProgress();
                showPage(currentPage);
            }
            
            // 응답 저장
            function saveResponses() {
                localStorage.setItem(storageKey, JSON.stringify(responses));
                localStorage.setItem(pageKey, currentPage.toString());
            }
            
            // 진행률 업데이트
            function updateProgress() {
                const answeredQuestionsCount = Object.keys(responses).length;
                const progressPercent = (answeredQuestionsCount / totalQuestions) * 100;
                
                progressBar.style.width = progressPercent + '%';
                answeredCount.textContent = answeredQuestionsCount;
            }
            
            // 페이지 표시
            function showPage(pageIndex) {
                // 모든 페이지 숨기기
                pageContainers.forEach(container => container.classList.add('hidden'));
                // 현재 페이지 표시
                if (pageContainers[pageIndex]) {
                    pageContainers[pageIndex].classList.remove('hidden');
                }
                
                // 페이지 인디케이터 업데이트
                pageIndicators.forEach((indicator, index) => {
                    if (index === pageIndex) {
                        indicator.classList.add('bg-indigo-600', 'border-indigo-600', 'text-white');
                        indicator.classList.remove('bg-white', 'border-gray-300', 'text-gray-600');
                    } else {
                        indicator.classList.remove('bg-indigo-600', 'border-indigo-600', 'text-white');
                        indicator.classList.add('bg-white', 'border-gray-300', 'text-gray-600');
                    }
                });
                
                // 버튼 상태 업데이트
                prevBtn.disabled = pageIndex === 0;
                prevBtn.classList.toggle('opacity-50', pageIndex === 0);
                prevBtn.classList.toggle('cursor-not-allowed', pageIndex === 0);
                
                // 다음/제출 버튼 표시 및 활성화 상태
                if (pageIndex === totalPages - 1) {
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                    checkPageCompletion();
                } else {
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                    checkPageCompletion();
                }
                
                currentPage = pageIndex;
                saveResponses();
            }
            
            // 현재 페이지의 모든 문항이 답변되었는지 확인
            function checkPageCompletion() {
                const startIndex = currentPage * questionsPerPage;
                const endIndex = Math.min(startIndex + questionsPerPage, totalQuestions);
                let allAnswered = true;
                
                for (let i = startIndex; i < endIndex; i++) {
                    if (!responses.hasOwnProperty(i)) {
                        allAnswered = false;
                        break;
                    }
                }
                
                if (currentPage === totalPages - 1) {
                    // 마지막 페이지
                    submitBtn.disabled = !allAnswered;
                    submitBtn.classList.toggle('opacity-50', !allAnswered);
                    submitBtn.classList.toggle('cursor-not-allowed', !allAnswered);
                } else {
                    // 중간 페이지
                    nextBtn.disabled = !allAnswered;
                    nextBtn.classList.toggle('opacity-50', !allAnswered);
                    nextBtn.classList.toggle('cursor-not-allowed', !allAnswered);
                }
            }
            
            // 라디오 버튼 변경 감지
            document.querySelectorAll('.question-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const questionIndex = this.dataset.question;
                    responses[questionIndex] = this.value;
                    
                    updateProgress();
                    saveResponses();
                    checkPageCompletion();
                });
            });
            
            // 페이지 인디케이터 클릭
            pageIndicators.forEach(indicator => {
                indicator.addEventListener('click', function() {
                    const targetPage = parseInt(this.dataset.page);
                    
                    // 이전 페이지로만 이동 가능 또는 현재 페이지까지 모든 문항이 답변된 경우
                    if (targetPage <= currentPage) {
                        showPage(targetPage);
                    } else {
                        // 현재 페이지까지 모든 문항이 답변되었는지 확인
                        let canNavigate = true;
                        for (let p = 0; p <= targetPage; p++) {
                            const startIdx = p * questionsPerPage;
                            const endIdx = Math.min(startIdx + questionsPerPage, totalQuestions);
                            
                            if (p < targetPage) {
                                for (let i = startIdx; i < endIdx; i++) {
                                    if (!responses.hasOwnProperty(i)) {
                                        canNavigate = false;
                                        break;
                                    }
                                }
                            }
                            
                            if (!canNavigate) break;
                        }
                        
                        if (canNavigate) {
                            showPage(targetPage);
                        }
                    }
                });
            });
            
            // 이전 버튼
            prevBtn.addEventListener('click', function() {
                if (currentPage > 0) {
                    showPage(currentPage - 1);
                }
            });
            
            // 다음 버튼
            nextBtn.addEventListener('click', function() {
                const startIndex = currentPage * questionsPerPage;
                const endIndex = Math.min(startIndex + questionsPerPage, totalQuestions);
                let allAnswered = true;
                
                for (let i = startIndex; i < endIndex; i++) {
                    if (!responses.hasOwnProperty(i)) {
                        allAnswered = false;
                        break;
                    }
                }
                
                if (allAnswered && currentPage < totalPages - 1) {
                    showPage(currentPage + 1);
                }
            });
            
            // 폼 제출 시 로컬 스토리지 정리
            form.addEventListener('submit', function(e) {
                // 모든 문항이 답변되었는지 최종 확인
                let allAnswered = true;
                for (let i = 0; i < totalQuestions; i++) {
                    if (!responses.hasOwnProperty(i)) {
                        allAnswered = false;
                        e.preventDefault();
                        alert('모든 문항에 답변해주세요.');
                        break;
                    }
                }
                
                if (allAnswered) {
                    // 제출 성공 시 로컬 스토리지 정리
                    localStorage.removeItem(storageKey);
                    localStorage.removeItem(pageKey);
                }
            });
            
            // 페이지 떠날 때 경고
            window.addEventListener('beforeunload', function(e) {
                if (Object.keys(responses).length > 0 && Object.keys(responses).length < totalQuestions) {
                    e.preventDefault();
                    e.returnValue = '아직 완료되지 않은 설문이 있습니다. 페이지를 떠나시면 진행 상황이 저장됩니다.';
                }
            });
            
            // 초기 로드
            loadSavedResponses();
        });
    </script>
</x-app-layout>
