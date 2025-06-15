<?php

namespace App\Filament\Widgets;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SurveyResponseCategoryChart extends ChartWidget
{
    protected static ?string $heading = '카테고리별 평균 성취도';
    
    protected static ?int $sort = 3;
    
    public ?string $filter = 'all';
    
    protected function getFilters(): ?array
    {
        $surveys = Survey::whereHas('responses')->pluck('title', 'id')->toArray();
        
        return array_merge(['all' => '전체 설문'], $surveys);
    }
    
    protected function getData(): array
    {
        $query = SurveyResponse::query();
        
        if ($this->filter !== 'all') {
            $query->where('survey_id', $this->filter);
        }
        
        $responses = $query->with('survey')->get();
        
        // 카테고리별 데이터 집계
        $categoryData = [];
        
        foreach ($responses as $response) {
            $categoryScores = $response->getCategoryScores();
            
            foreach ($categoryScores as $category) {
                if (!isset($categoryData[$category['name']])) {
                    $categoryData[$category['name']] = [
                        'total_percentage' => 0,
                        'count' => 0,
                        'scores' => []
                    ];
                }
                
                $categoryData[$category['name']]['total_percentage'] += $category['percentage'];
                $categoryData[$category['name']]['count']++;
                $categoryData[$category['name']]['scores'][] = $category['percentage'];
            }
        }
        
        // 평균 계산
        $labels = [];
        $averages = [];
        
        foreach ($categoryData as $categoryName => $data) {
            if ($data['count'] > 0) {
                $labels[] = $categoryName;
                $averages[] = round($data['total_percentage'] / $data['count'], 1);
            }
        }
        
        // 데이터가 없는 경우
        if (empty($labels)) {
            return [
                'datasets' => [
                    [
                        'label' => '평균 성취도 (%)',
                        'data' => [],
                        'backgroundColor' => '#3b82f6',
                    ],
                ],
                'labels' => [],
            ];
        }
        
        // 색상 설정 (성취도에 따라)
        $backgroundColors = array_map(function ($avg) {
            if ($avg >= 70) return '#10b981'; // green
            if ($avg >= 40) return '#f59e0b'; // yellow
            return '#ef4444'; // red
        }, $averages);
        
        return [
            'datasets' => [
                [
                    'label' => '평균 성취도 (%)',
                    'data' => $averages,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'title' => [
                        'display' => true,
                        'text' => '성취도 (%)'
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }",
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
