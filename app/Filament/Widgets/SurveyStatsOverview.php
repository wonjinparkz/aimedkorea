<?php

namespace App\Filament\Widgets;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SurveyStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        $totalSurveys = Survey::count();
        $totalResponses = SurveyResponse::count();
        $todayResponses = SurveyResponse::whereDate('created_at', today())->count();
        $averageScore = SurveyResponse::avg('total_score') ?? 0;
        
        return [
            Stat::make('전체 설문 수', $totalSurveys)
                ->description('등록된 설문 개수')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),
                
            Stat::make('전체 응답 수', $totalResponses)
                ->description('누적 응답 수')
                ->icon('heroicon-o-chart-bar')
                ->color('success'),
                
            Stat::make('오늘 응답 수', $todayResponses)
                ->description('오늘 받은 응답')
                ->icon('heroicon-o-calendar')
                ->color('warning'),
                
            Stat::make('평균 점수', number_format($averageScore, 1))
                ->description('전체 응답 평균')
                ->icon('heroicon-o-academic-cap')
                ->color('info'),
        ];
    }
}
