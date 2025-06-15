<?php

namespace App\Filament\Widgets;

use App\Models\Survey;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SurveyResponseChart extends ChartWidget
{
    protected static ?string $heading = '설문별 응답 통계';
    
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
        $surveys = Survey::withCount('responses')
            ->having('responses_count', '>', 0)
            ->get();
        
        $labels = $surveys->pluck('title')->map(fn($title) => 
            mb_strlen($title) > 20 ? mb_substr($title, 0, 20) . '...' : $title
        )->toArray();
        
        $data = $surveys->pluck('responses_count')->toArray();
        
        // 설문별 평균 점수
        $avgScores = [];
        foreach ($surveys as $survey) {
            $avgScore = $survey->responses()->avg('total_score') ?? 0;
            $avgScores[] = round($avgScore, 1);
        }
        
        return [
            'datasets' => [
                [
                    'label' => '응답 수',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => '평균 점수',
                    'data' => $avgScores,
                    'backgroundColor' => '#10b981',
                    'yAxisID' => 'y1',
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
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => '응답 수'
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => '평균 점수'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
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
