<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    회복 점수 비교 결과
                </h1>
                <p class="text-xl text-gray-600">선택한 설문 응답들의 비교 분석</p>
            </div>

            <!-- 비교 차트 -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">회복 점수 추이</h2>
                <canvas id="comparisonChart" class="w-full" style="max-height: 400px;"></canvas>
            </div>

            <!-- 비교 테이블 -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                설문/날짜
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                총점
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                회복 점수
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                변화
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($comparisonData as $index => $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $data['survey_name'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $data['date'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-lg font-semibold">{{ $data['total_score'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-lg font-semibold text-green-600">{{ $data['recovery_score'] }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($index > 0)
                                        @php
                                            $change = $data['recovery_score'] - $comparisonData[$index - 1]['recovery_score'];
                                        @endphp
                                        @if($change > 0)
                                            <span class="text-green-600 font-semibold">
                                                ↑ {{ abs($change) }}%
                                            </span>
                                        @elseif($change < 0)
                                            <span class="text-red-600 font-semibold">
                                                ↓ {{ abs($change) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500">기준점</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 카테고리별 비교 -->
            @if(isset($comparisonData[0]['category_scores']) && count($comparisonData[0]['category_scores']) > 0)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">카테고리별 점수 비교</h2>
                    <canvas id="categoryChart" class="w-full" style="max-height: 400px;"></canvas>
                </div>
            @endif

            <!-- 액션 버튼 -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('recovery.compare') }}" 
                   class="px-6 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                    다른 응답 비교
                </a>
                <a href="{{ route('recovery.dashboard') }}" 
                   class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    대시보드로 돌아가기
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 비교 차트
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const comparisonData = @json($comparisonData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: comparisonData.map(d => d.date),
                datasets: [{
                    label: '회복 점수',
                    data: comparisonData.map(d => d.recovery_score),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }, {
                    label: '총점 (역전)',
                    data: comparisonData.map(d => 100 - d.total_score),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
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

        // 카테고리별 차트
        @if(isset($comparisonData[0]['category_scores']) && count($comparisonData[0]['category_scores']) > 0)
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categories = [...new Set(comparisonData.flatMap(d => 
                d.category_scores.map(c => c.name || c[0] || '카테고리')
            ))];
            
            const datasets = comparisonData.map((data, index) => ({
                label: data.date,
                data: categories.map(cat => {
                    const categoryData = data.category_scores.find(c => 
                        (c.name || c[0] || '카테고리') === cat
                    );
                    return categoryData ? (100 - (categoryData.percentage || 0)) : 0;
                }),
                backgroundColor: `hsla(${index * 60}, 70%, 50%, 0.5)`,
                borderColor: `hsl(${index * 60}, 70%, 50%)`,
                borderWidth: 2
            }));

            new Chart(categoryCtx, {
                type: 'radar',
                data: {
                    labels: categories,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            }
                        }
                    }
                }
            });
        @endif
    </script>
</x-app-layout>