<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    회복 점수 대시보드
                </h1>
                <p class="text-xl text-gray-600">{{ Auth::user()->name }}님의 건강 회복 상태를 한눈에 확인하세요</p>
            </div>

            <!-- 개선율 알림 -->
            @if($improvementRate)
                <div class="mb-6 p-4 rounded-lg {{ $improvementRate['direction'] === 'up' ? 'bg-green-100 border-green-400' : ($improvementRate['direction'] === 'down' ? 'bg-red-100 border-red-400' : 'bg-gray-100 border-gray-400') }} border">
                    <div class="flex items-center">
                        @if($improvementRate['direction'] === 'up')
                            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            <span class="text-green-800 font-semibold">
                                지난 검사 대비 {{ abs($improvementRate['rate']) }}% 개선되었습니다! ({{ $improvementRate['absolute'] > 0 ? '+' : '' }}{{ $improvementRate['absolute'] }}점)
                            </span>
                        @elseif($improvementRate['direction'] === 'down')
                            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                            <span class="text-red-800 font-semibold">
                                지난 검사 대비 {{ abs($improvementRate['rate']) }}% 하락했습니다. ({{ $improvementRate['absolute'] }}점)
                            </span>
                        @else
                            <span class="text-gray-800">지난 검사와 동일한 상태입니다.</span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- 메인 회복 점수 게이지 -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">종합 회복 점수</h2>
                        
                        <div class="relative mx-auto" style="width: 100%; max-width: 400px; height: 250px;">
                            <canvas id="recoveryGaugeChart"></canvas>
                            
                            <div class="absolute bottom-0 left-0 right-0 flex justify-center pb-12">
                                <div class="text-center">
                                    <div class="text-5xl font-bold text-gray-800" id="recoveryScoreDisplay">{{ $latestResponse->recovery_score ?? 0 }}%</div>
                                    <div class="text-sm text-gray-500 mt-1">회복 지수</div>
                                </div>
                            </div>
                        </div>
                        
                        @php
                            $score = $latestResponse->recovery_score ?? 0;
                            $level = $score >= 80 ? '최적' : ($score >= 65 ? '우수' : ($score >= 50 ? '양호' : ($score >= 35 ? '주의' : ($score >= 20 ? '위험' : '붕괴'))));
                            $levelColor = $score >= 80 ? '#047857' : ($score >= 65 ? '#059669' : ($score >= 50 ? '#10b981' : ($score >= 35 ? '#f59e0b' : ($score >= 20 ? '#dc2626' : '#991b1b'))));
                        @endphp
                        
                        <div class="text-center mt-8">
                            <span class="inline-flex items-center px-8 py-4 rounded-full text-xl font-bold shadow-lg bg-gradient-to-r from-gray-50 to-gray-100 border-2" 
                                  style="border-color: {{ $levelColor }}; color: {{ $levelColor }}">
                                {{ $level }} 상태
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 회복 팁 카드 -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl p-6 h-full">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path>
                            </svg>
                            오늘의 회복 팁
                        </h3>
                        
                        @if(isset($recoveryTips['overall']))
                            <div class="mb-4">
                                <div class="p-4 rounded-lg {{ $recoveryTips['overall']['level'] === 'critical' ? 'bg-red-50' : ($recoveryTips['overall']['level'] === 'warning' ? 'bg-yellow-50' : ($recoveryTips['overall']['level'] === 'moderate' ? 'bg-blue-50' : 'bg-green-50')) }}">
                                    <p class="font-semibold text-gray-800 mb-2">{{ $recoveryTips['overall']['message'] }}</p>
                                    <ul class="space-y-2">
                                        @foreach($recoveryTips['overall']['actions'] as $action)
                                            <li class="flex items-start">
                                                <svg class="w-5 h-5 text-gray-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-gray-700">{{ $action }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        
                    </div>
                </div>
            </div>

            <!-- 시간별 추이 그래프 -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">회복 점수 추이</h2>
                <div style="height: 300px;">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>

            <!-- 설문별 분석 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- 레이더 차트 -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">설문별 회복 상태</h3>
                    @if(count($surveyScores) > 0)
                        <div style="height: 300px;">
                            <canvas id="radarChart"></canvas>
                        </div>
                    @else
                        <div class="flex items-center justify-center" style="height: 300px;">
                            <div class="text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p>설문별 분석 데이터가 없습니다</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 설문별 상세 점수 -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">설문별 상세 점수</h3>
                    @if(count($surveyScores) > 0)
                        <div class="space-y-4">
                            @foreach($surveyScores as $survey)
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <div>
                                            <span class="text-gray-700 font-medium">{{ $survey['name'] }}</span>
                                            <span class="text-xs text-gray-500 ml-2">
                                                ({{ $survey['response_date']->format('m/d') }})
                                            </span>
                                        </div>
                                        <span class="text-gray-600 font-semibold">{{ round($survey['score']) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-1000 ease-out relative {{ $survey['score'] >= 70 ? 'bg-gradient-to-r from-green-500 to-green-600' : ($survey['score'] >= 50 ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : ($survey['score'] >= 30 ? 'bg-gradient-to-r from-orange-500 to-orange-600' : 'bg-gradient-to-r from-red-500 to-red-600')) }}" 
                                             style="width: {{ $survey['score'] }}%">
                                            <div class="absolute inset-0 bg-white bg-opacity-20"></div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 text-right">
                                        원점수: {{ $survey['total_score'] }}점
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>아직 완료한 설문이 없습니다.</p>
                            <p class="text-sm mt-2">자가 진단을 시작하여 회복 점수를 확인해보세요.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 개선이 필요한 영역 -->
            @if(isset($recoveryTips['surveys']) && count($recoveryTips['surveys']) > 0)
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">개선이 필요한 영역</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($recoveryTips['surveys'] as $surveyName => $surveyTip)
                            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border border-orange-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-bold text-lg text-orange-900">{{ $surveyName }}</h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                        {{ round($surveyTip['score']) }}%
                                    </span>
                                </div>
                                <p class="text-gray-700 leading-relaxed">{{ $surveyTip['tip'] }}</p>
                                <div class="mt-4 pt-4 border-t border-orange-200">
                                    <p class="text-xs text-gray-600">
                                        @if($surveyTip['score'] < 30)
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                즉각적인 개선 필요
                                            </span>
                                        @else
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 text-orange-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                지속적인 관리 필요
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800">
                                    <strong>전문가 상담 추천:</strong> 50% 미만의 점수를 받은 영역이 있다면, 전문가의 도움을 받는 것이 좋습니다.
                                    지속적인 개선이 이루어지지 않는다면 의료 전문가와 상담하세요.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 빠른 액션 -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('surveys.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    새로운 진단 시작
                </a>
                
                <a href="{{ route('recovery.history') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    진단 이력 보기
                </a>
                
                <button onclick="window.print()" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 no-print">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    리포트 인쇄
                </button>
            </div>
        </div>
    </div>

    <!-- Chart.js 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded');
            console.log('Recent Responses Count:', {{ $recentResponses->count() }});
            
            // 회복 점수 게이지 차트
            const recoveryScore = {{ $latestResponse->recovery_score ?? 0 }};
            console.log('Recovery Score:', recoveryScore);
            
            const gaugeCanvas = document.getElementById('recoveryGaugeChart');
            if (!gaugeCanvas) {
                console.error('Gauge canvas not found');
                return;
            }
            const gaugeCtx = gaugeCanvas.getContext('2d');
            
            const segments = [
                {name: '붕괴', color: '#991b1b', range: [0, 16.67]},
                {name: '위험', color: '#dc2626', range: [16.67, 33.33]},
                {name: '주의', color: '#f59e0b', range: [33.33, 50]},
                {name: '양호', color: '#10b981', range: [50, 66.67]},
                {name: '우수', color: '#059669', range: [66.67, 83.33]},
                {name: '최적', color: '#047857', range: [83.33, 100]}
            ];
            
            // 현재 세그먼트 찾기
            let currentSegmentIndex = 0;
            segments.forEach((segment, index) => {
                if (recoveryScore >= segment.range[0] && recoveryScore <= segment.range[1]) {
                    currentSegmentIndex = index;
                }
            });
            
            // 세그먼트 색상 설정
            const segmentColors = segments.map((segment, index) => 
                index === currentSegmentIndex ? segment.color : segment.color + '26'
            );
            
            new Chart(gaugeCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [1, 1, 1, 1, 1, 1],
                        backgroundColor: segmentColors,
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });

            // 시간별 추이 차트
            const timelineData = @json($timelineData);
            console.log('Timeline Data:', timelineData);
            
            const timelineCanvas = document.getElementById('timelineChart');
            if (!timelineCanvas) {
                console.error('Timeline canvas not found');
                return;
            }
            const timelineCtx = timelineCanvas.getContext('2d');
            
            // 데이터가 없는 경우 처리
            if (!timelineData.labels || timelineData.labels.length === 0) {
                timelineData.labels = ['데이터 없음'];
                timelineData.scores = [0];
            }
            
            const datasets = [{
                label: '종합 회복 점수',
                data: timelineData.scores,
                borderColor: '#3b82f6',
                backgroundColor: '#3b82f640',
                tension: 0.4,
                borderWidth: 3
            }];
            
            // 설문별 데이터 추가
            const surveyColors = ['#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#3b82f6', '#ef4444'];
            let colorIndex = 0;
            
            if (timelineData.surveys && Object.keys(timelineData.surveys).length > 0) {
                for (const [surveyName, scores] of Object.entries(timelineData.surveys)) {
                    datasets.push({
                        label: surveyName,
                        data: scores,
                        borderColor: surveyColors[colorIndex % surveyColors.length],
                        backgroundColor: surveyColors[colorIndex % surveyColors.length] + '20',
                        tension: 0.4,
                        borderWidth: 2,
                        borderDash: [5, 5]
                    });
                    colorIndex++;
                }
            }
            
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: timelineData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // 레이더 차트
            const surveyScores = @json($surveyScores);
            console.log('Survey Scores:', surveyScores);
            
            if (surveyScores.length > 0) {
                const radarCanvas = document.getElementById('radarChart');
                if (radarCanvas) {
                    const radarCtx = radarCanvas.getContext('2d');
                    
                    const radarLabels = surveyScores.map(s => s.name);
                    const radarData = surveyScores.map(s => s.score);
                    
                    new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: radarLabels,
                    datasets: [{
                        label: '회복 점수',
                        data: radarData,
                        borderColor: '#3b82f6',
                        backgroundColor: '#3b82f640',
                        borderWidth: 2,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20,
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
                }
            }

            // 애니메이션
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
</x-app-layout>