<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $survey->title }} - {{ config('app.name', 'AI-MED Korea') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Mobile App Styles -->
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        input, textarea, select {
            -webkit-user-select: text;
            user-select: text;
        }
        
        body {
            overscroll-behavior: contain;
            -webkit-font-smoothing: antialiased;
            background: #f8fafc;
        }
        
        .mobile-container {
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .question-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .option-button {
            transition: all 0.2s ease;
            border: 2px solid #e5e7eb;
            background: white;
        }
        
        .option-button:active {
            transform: scale(0.98);
        }
        
        .option-button.selected {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }
        
        .progress-bar {
            transition: width 0.3s ease;
        }
        
        .scale-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .scale-item:hover {
            transform: translateY(-2px);
        }
        
        .scale-item.selected {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        
        .floating-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 12px 16px;
            z-index: 40;
        }
        
        .question-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d1d5db;
            transition: all 0.3s ease;
        }
        
        .question-indicator.active {
            width: 24px;
            border-radius: 4px;
            background: #3b82f6;
        }
        
        .question-indicator.completed {
            background: #10b981;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="mobile-container">
        {{-- Mobile App Header --}}
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-gray-100">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <a href="/surveys" class="p-2 -ml-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-base font-semibold text-gray-900 flex-1 text-center mx-4 truncate">
                        {{ $survey->title }}
                    </h1>
                    <button onclick="exitSurvey()" class="p-2 -mr-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                {{-- Progress Bar --}}
                <div class="mt-3">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="progress-bar" class="h-full bg-gradient-to-r from-blue-500 to-purple-600 progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs text-gray-500">
                            <span id="current-question">0</span> / <span id="total-questions">0</span>
                        </span>
                        <span class="text-xs text-gray-500">
                            <span id="time-remaining">{{ $survey->questions ? count($survey->questions) * 2 : 5 }}</span> {{ __('min_remaining') }}
                        </span>
                    </div>
                </div>
            </div>
        </header>
        
        {{-- Survey Form --}}
        <form id="survey-form" action="{{ route('surveys.store', $survey) }}" method="POST">
            @csrf
            <input type="hidden" name="version" id="survey-version" value="simple">
            
            {{-- Survey Info Card --}}
            <div id="survey-intro" class="px-4 py-6">
                <div class="question-card bg-white p-6">
                    @if($survey->survey_image)
                        <img src="{{ Storage::url($survey->survey_image) }}" 
                             alt="{{ $survey->title }}"
                             class="w-full h-48 object-cover rounded-xl mb-4">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl mb-4 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $survey->title }}</h2>
                    <p class="text-gray-600 mb-6">{{ $survey->description }}</p>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-gray-500">
                            <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">{{ __('estimated_time') }}: {{ $survey->questions ? count($survey->questions) * 2 : 5 }} {{ __('minutes') }}</span>
                        </div>
                        <div class="flex items-center text-gray-500">
                            <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">{{ __('total_questions') }}: <span id="intro-total-questions">{{ $survey->questions ? count($survey->questions) : 0 }}</span></span>
                        </div>
                        <div class="flex items-center text-gray-500">
                            <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm">{{ __('instant_results') }}</span>
                        </div>
                    </div>
                    
                    @if($survey->has_detailed_version)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-900 font-medium">{{ __('detailed_version_available') }}</p>
                                    <p class="text-xs text-blue-700 mt-1">{{ __('choose_version_after_basic') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <button type="button" onclick="startSurvey()" class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-2xl">
                        {{ __('start_assessment') }}
                    </button>
                </div>
            </div>
            
            {{-- Questions Container --}}
            <div id="questions-container" class="hidden">
                @if($survey->questions && count($survey->questions) > 0)
                    @foreach($survey->questions as $index => $question)
                        <div class="question-slide hidden px-4 py-6" data-question="{{ $index }}">
                            <div class="question-card bg-white p-6">
                                <div class="mb-4">
                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                        {{ __('question') }} {{ $index + 1 }}
                                    </span>
                                </div>
                                
                                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                                    @php
                                        $questionText = '';
                                        if (is_array($question)) {
                                            // Check for various possible keys
                                            $questionText = $question['label'] ?? 
                                                          $question['text'] ?? 
                                                          $question['question'] ?? 
                                                          (isset($question[0]) ? $question[0] : '');
                                        } else {
                                            $questionText = $question;
                                        }
                                    @endphp
                                    {{ $questionText }}
                                </h3>
                                
                                @if(is_array($question) && isset($question['type']))
                                    @if($question['type'] === 'scale')
                                        {{-- Scale Question (1-5 or 1-10) --}}
                                        <div class="space-y-4">
                                            <div class="flex justify-between text-xs text-gray-500 mb-2">
                                                <span>{{ isset($question['min_label']) ? $question['min_label'] : __('strongly_disagree') }}</span>
                                                <span>{{ isset($question['max_label']) ? $question['max_label'] : __('strongly_agree') }}</span>
                                            </div>
                                            <div class="grid grid-cols-5 gap-2">
                                                @for($i = 1; $i <= (isset($question['max']) ? $question['max'] : 5); $i++)
                                                    <button type="button" 
                                                            onclick="selectScale({{ $index }}, {{ $i }})"
                                                            class="scale-item h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center font-semibold text-gray-700"
                                                            data-question="{{ $index }}"
                                                            data-value="{{ $i }}">
                                                        {{ $i }}
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="answers[{{ $index }}]" id="answer-{{ $index }}">
                                        </div>
                                    @elseif($question['type'] === 'multiple_choice')
                                        {{-- Multiple Choice --}}
                                        <div class="space-y-3">
                                            @if(isset($question['options']) && is_array($question['options']))
                                                @foreach($question['options'] as $optionIndex => $option)
                                                <button type="button"
                                                        onclick="selectOption({{ $index }}, '{{ $option }}')"
                                                        class="option-button w-full p-4 rounded-xl text-left"
                                                        data-question="{{ $index }}"
                                                        data-value="{{ $option }}">
                                                    <div class="flex items-center">
                                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 mr-3 flex-shrink-0 option-radio"></div>
                                                        <span class="text-gray-700">{{ $option }}</span>
                                                    </div>
                                                </button>
                                                @endforeach
                                            @endif
                                            <input type="hidden" name="answers[{{ $index }}]" id="answer-{{ $index }}">
                                        </div>
                                    @elseif($question['type'] === 'text')
                                        {{-- Text Input --}}
                                        <div>
                                            <textarea name="answers[{{ $index }}]" 
                                                      id="answer-{{ $index }}"
                                                      rows="4"
                                                      class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none resize-none"
                                                      placeholder="{{ __('type_your_answer') }}"></textarea>
                                        </div>
                                    @endif
                                @else
                                    {{-- Default Checklist Question --}}
                                    @if($survey->checklist_items && count($survey->checklist_items) > 0)
                                        <div class="space-y-3">
                                            @foreach($survey->checklist_items as $itemIndex => $item)
                                                @php
                                                    $itemLabel = is_array($item) ? ($item['label'] ?? $item['text'] ?? $item) : $item;
                                                @endphp
                                                <button type="button"
                                                        onclick="selectChecklistItem({{ $index }}, {{ $itemIndex }})"
                                                        class="option-button w-full p-4 rounded-xl text-left"
                                                        data-question="{{ $index }}"
                                                        data-value="{{ $itemIndex }}">
                                                    <div class="flex items-center">
                                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 mr-3 flex-shrink-0 option-radio"></div>
                                                        <span class="text-gray-700">{{ $itemLabel }}</span>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- Fallback to Yes/No if no checklist items --}}
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button"
                                                    onclick="selectYesNo({{ $index }}, 'yes')"
                                                    class="option-button p-6 rounded-xl"
                                                    data-question="{{ $index }}"
                                                    data-value="yes">
                                                <svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="block text-center font-semibold">{{ __('yes') }}</span>
                                            </button>
                                            <button type="button"
                                                    onclick="selectYesNo({{ $index }}, 'no')"
                                                    class="option-button p-6 rounded-xl"
                                                    data-question="{{ $index }}"
                                                    data-value="no">
                                                <svg class="w-8 h-8 mx-auto mb-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="block text-center font-semibold">{{ __('no') }}</span>
                                            </button>
                                        </div>
                                    @endif
                                    <input type="hidden" name="answers[{{ $index }}]" id="answer-{{ $index }}">
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            {{-- Summary Screen --}}
            <div id="summary-screen" class="hidden px-4 py-6">
                <div class="question-card bg-white p-6">
                    <div class="text-center mb-6">
                        <svg class="w-20 h-20 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('all_done') }}</h2>
                        <p class="text-gray-600">{{ __('ready_to_submit') }}</p>
                    </div>
                    
                    @if($survey->has_detailed_version)
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-4 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">{{ __('want_detailed_analysis') }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ __('detailed_analysis_description') }}</p>
                            
                            <div class="space-y-2">
                                <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer">
                                    <input type="radio" name="analysis_type" value="simple" checked class="mr-3">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ __('basic_analysis') }}</div>
                                        <div class="text-xs text-gray-500">{{ __('quick_results') }}</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer">
                                    <input type="radio" name="analysis_type" value="detailed" class="mr-3">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ __('detailed_analysis') }}</div>
                                        <div class="text-xs text-gray-500">{{ __('comprehensive_results') }}</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endif
                    
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-2xl">
                        {{ __('submit_and_view_results') }}
                    </button>
                </div>
            </div>
        </form>
        
        {{-- Floating Navigation --}}
        <div id="floating-nav" class="floating-nav hidden">
            <div class="flex items-center justify-between">
                <button type="button" 
                        onclick="previousQuestion()"
                        id="prev-btn"
                        class="px-6 py-3 text-gray-600 font-medium">
                    {{ __('previous') }}
                </button>
                
                <div class="flex space-x-1" id="question-indicators">
                    {{-- Will be populated by JavaScript --}}
                </div>
                
                <button type="button" 
                        onclick="nextQuestion()"
                        id="next-btn"
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-xl disabled:opacity-50"
                        disabled>
                    {{ __('next') }}
                </button>
            </div>
        </div>
    </div>
    
    <script>
        let currentQuestion = -1;
        let totalQuestions = {{ $survey->questions ? count($survey->questions) : 0 }};
        let answers = {};
        
        function startSurvey() {
            document.getElementById('survey-intro').classList.add('hidden');
            document.getElementById('questions-container').classList.remove('hidden');
            document.getElementById('floating-nav').classList.remove('hidden');
            
            // Initialize indicators
            let indicatorsHtml = '';
            for(let i = 0; i < totalQuestions; i++) {
                indicatorsHtml += `<div class="question-indicator" data-indicator="${i}"></div>`;
            }
            document.getElementById('question-indicators').innerHTML = indicatorsHtml;
            
            // Update totals
            document.getElementById('total-questions').textContent = totalQuestions;
            document.getElementById('intro-total-questions').textContent = totalQuestions;
            
            // Show first question
            showQuestion(0);
        }
        
        function showQuestion(index) {
            // Hide all questions
            document.querySelectorAll('.question-slide').forEach(slide => {
                slide.classList.add('hidden');
            });
            
            // Show current question
            const currentSlide = document.querySelector(`[data-question="${index}"]`);
            if (currentSlide) {
                currentSlide.classList.remove('hidden');
                currentSlide.classList.add('slide-in');
            }
            
            currentQuestion = index;
            
            // Update progress
            const progress = ((index + 1) / totalQuestions) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
            document.getElementById('current-question').textContent = index + 1;
            
            // Update indicators
            document.querySelectorAll('.question-indicator').forEach((indicator, i) => {
                indicator.classList.remove('active');
                if (i === index) {
                    indicator.classList.add('active');
                }
                if (answers[i] !== undefined) {
                    indicator.classList.add('completed');
                }
            });
            
            // Update navigation buttons
            document.getElementById('prev-btn').style.display = index === 0 ? 'none' : 'block';
            
            // Check if answer exists for current question
            if (answers[index] !== undefined) {
                document.getElementById('next-btn').disabled = false;
            } else {
                document.getElementById('next-btn').disabled = true;
            }
            
            // Check if last question
            if (index === totalQuestions - 1) {
                document.getElementById('next-btn').textContent = '{{ __("finish") }}';
            } else {
                document.getElementById('next-btn').textContent = '{{ __("next") }}';
            }
        }
        
        function nextQuestion() {
            if (currentQuestion < totalQuestions - 1) {
                showQuestion(currentQuestion + 1);
            } else {
                showSummary();
            }
        }
        
        function previousQuestion() {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        }
        
        function showSummary() {
            document.getElementById('questions-container').classList.add('hidden');
            document.getElementById('floating-nav').classList.add('hidden');
            document.getElementById('summary-screen').classList.remove('hidden');
            
            // Update progress to 100%
            document.getElementById('progress-bar').style.width = '100%';
        }
        
        function selectYesNo(questionIndex, value) {
            // Update UI
            document.querySelectorAll(`[data-question="${questionIndex}"]`).forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.closest('button').classList.add('selected');
            
            // Store answer
            answers[questionIndex] = value;
            document.getElementById(`answer-${questionIndex}`).value = value;
            
            // Enable next button
            document.getElementById('next-btn').disabled = false;
            
            // Auto advance after short delay
            setTimeout(() => {
                if (currentQuestion < totalQuestions - 1) {
                    nextQuestion();
                }
            }, 300);
        }
        
        function selectOption(questionIndex, value) {
            // Update UI
            document.querySelectorAll(`[data-question="${questionIndex}"]`).forEach(btn => {
                btn.classList.remove('selected');
                btn.querySelector('.option-radio').style.background = 'white';
            });
            event.target.closest('button').classList.add('selected');
            event.target.closest('button').querySelector('.option-radio').style.background = '#3b82f6';
            
            // Store answer
            answers[questionIndex] = value;
            document.getElementById(`answer-${questionIndex}`).value = value;
            
            // Enable next button
            document.getElementById('next-btn').disabled = false;
        }
        
        function selectScale(questionIndex, value) {
            // Update UI
            document.querySelectorAll(`[data-question="${questionIndex}"]`).forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.classList.add('selected');
            
            // Store answer
            answers[questionIndex] = value;
            document.getElementById(`answer-${questionIndex}`).value = value;
            
            // Enable next button
            document.getElementById('next-btn').disabled = false;
        }
        
        function selectChecklistItem(questionIndex, value) {
            // Update UI
            document.querySelectorAll(`[data-question="${questionIndex}"]`).forEach(btn => {
                btn.classList.remove('selected');
                const radio = btn.querySelector('.option-radio');
                if (radio) radio.style.background = 'white';
            });
            event.target.closest('button').classList.add('selected');
            const selectedRadio = event.target.closest('button').querySelector('.option-radio');
            if (selectedRadio) selectedRadio.style.background = '#3b82f6';
            
            // Store answer
            answers[questionIndex] = value;
            document.getElementById(`answer-${questionIndex}`).value = value;
            
            // Enable next button
            document.getElementById('next-btn').disabled = false;
        }
        
        function exitSurvey() {
            if (confirm('{{ __("exit_survey_confirm") }}')) {
                window.location.href = '/surveys';
            }
        }
        
        // Handle text inputs
        document.addEventListener('input', function(e) {
            if (e.target.tagName === 'TEXTAREA') {
                const questionIndex = parseInt(e.target.id.replace('answer-', ''));
                answers[questionIndex] = e.target.value;
                
                if (e.target.value.trim() !== '') {
                    document.getElementById('next-btn').disabled = false;
                } else {
                    document.getElementById('next-btn').disabled = true;
                }
            }
        });
    </script>
</body>
</html>