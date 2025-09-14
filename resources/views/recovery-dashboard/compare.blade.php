<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    체크 결과 비교
                </h1>
                <p class="text-xl text-gray-600">선택한 체크 결과를 비교하여 웰니스 추이를 확인하세요</p>
            </div>

            <!-- 비교 테이블 -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    항목
                                </th>
                                @foreach($comparisonData as $data)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $data['date'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- 종합 회복 점수 -->
                            <tr class="bg-blue-50">
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                    종합 웰니스 점수
                                </td>
                                @foreach($comparisonData as $index => $data)
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $score = $data['response']->recovery_score ?? 0;
                                            $prevScore = $index > 0 ? ($comparisonData[$index - 1]['response']->recovery_score ?? 0) : null;
                                            $diff = $prevScore !== null ? $score - $prevScore : null;
                                        @endphp
                                        <div class="text-2xl font-bold text-blue-600">
                                            {{ $score }}%
                                        </div>
                                        @if($diff !== null)
                                            <div class="text-sm {{ $diff > 0 ? 'text-green-600' : ($diff < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                @if($diff > 0)
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                                    </svg>
                                                    +{{ $diff }}%
                                                @elseif($diff < 0)
                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                    </svg>
                                                    {{ $diff }}%
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- 설문별 점수 -->
                            @php
                                $allSurveys = [];
                                foreach($comparisonData as $data) {
                                    if (!in_array($data['survey_name'], $allSurveys)) {
                                        $allSurveys[] = $data['survey_name'];
                                    }
                                }
                            @endphp

                            @foreach($allSurveys as $surveyName)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $surveyName }}
                                    </td>
                                    @foreach($comparisonData as $index => $data)
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($data['survey_name'] == $surveyName)
                                                @php
                                                    $score = $data['response']->recovery_score ?? 0;
                                                    $prevScore = null;
                                                    if ($index > 0 && $comparisonData[$index - 1]['survey_name'] == $surveyName) {
                                                        $prevScore = $comparisonData[$index - 1]['response']->recovery_score ?? 0;
                                                    }
                                                    $diff = $prevScore !== null ? $score - $prevScore : null;
                                                @endphp
                                                <div class="text-lg font-semibold {{ $score >= 70 ? 'text-green-600' : ($score >= 50 ? 'text-yellow-600' : ($score >= 30 ? 'text-orange-600' : 'text-red-600')) }}">
                                                    {{ round($score) }}%
                                                </div>
                                                @if($diff !== null)
                                                    <div class="text-xs {{ $diff > 0 ? 'text-green-600' : ($diff < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                        {{ $diff > 0 ? '+' : '' }}{{ round($diff) }}%
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach

                            <!-- 원점수 -->
                            <tr class="bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    원점수
                                </td>
                                @foreach($comparisonData as $data)
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ $data['response']->total_score }}점
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 시각화 차트 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- 라인 차트 -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">웰니스 점수 추이</h3>
                    <div style="height: 300px;">
                        <canvas id="comparisonLineChart"></canvas>
                    </div>
                </div>

                <!-- 레이더 차트 -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">설문별 비교</h3>
                    <div style="height: 300px;">
                        <canvas id="comparisonRadarChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 액션 버튼 -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('recovery.history') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    이력으로 돌아가기
                </a>
                
                <button onclick="window.print()" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 no-print">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    비교 결과 인쇄
                </button>
            </div>
        </div>
    </div>

    <!-- Chart.js 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comparisonData = @json($comparisonData);
            
            // 라인 차트 데이터 준비
            const labels = comparisonData.map(d => d.date);
            const recoveryScores = comparisonData.map(d => d.response.recovery_score || 0);
            
            // 설문별 데이터 준비
            const allSurveys = @json($allSurveys);
            const surveyDatasets = {};
            
            allSurveys.forEach(surveyName => {
                surveyDatasets[surveyName] = comparisonData.map(data => {
                    return data.survey_name === surveyName ? (data.response.recovery_score || 0) : null;
                });
            });
            
            // 라인 차트
            const lineCtx = document.getElementById('comparisonLineChart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '종합 웰니스 점수',
                            data: recoveryScores,
                            borderColor: '#3b82f6',
                            backgroundColor: '#3b82f640',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
            const radarCtx = document.getElementById('comparisonRadarChart').getContext('2d');
            const radarDatasets = comparisonData.map((data, index) => ({
                label: data.survey_name + ' (' + data.date + ')',
                data: allSurveys.map(surveyName => {
                    return data.survey_name === surveyName ? (data.response.recovery_score || 0) : 0;
                }),
                borderColor: ['#3b82f6', '#10b981', '#f59e0b', '#ec4899'][index % 4],
                backgroundColor: ['#3b82f640', '#10b98140', '#f59e0b40', '#ec489940'][index % 4],
                borderWidth: 2
            }));
            
            new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: allSurveys,
                    datasets: radarDatasets
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
                            position: 'bottom'
                        }
                    }
                }
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