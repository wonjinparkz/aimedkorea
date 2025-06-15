<?php

namespace App\Filament\Resources\SurveyResponseResource\Pages;

use App\Filament\Resources\SurveyResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Colors\Color;

class ViewSurveyResponse extends ViewRecord
{
    protected static string $resource = SurveyResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('응답 정보')
                    ->schema([
                        Components\TextEntry::make('survey.title')
                            ->label('설문'),
                        Components\TextEntry::make('user.name')
                            ->label('사용자')
                            ->default('익명'),
                        Components\TextEntry::make('total_score')
                            ->label('총점')
                            ->badge()
                            ->color(fn (int $state): string => match (true) {
                                $state <= 30 => 'success',
                                $state <= 60 => 'warning',
                                default => 'danger',
                            }),
                        Components\TextEntry::make('created_at')
                            ->label('응답일시')
                            ->dateTime('Y-m-d H:i:s'),
                        Components\TextEntry::make('ip_address')
                            ->label('IP 주소'),
                    ])
                    ->columns(3),
                    
                Components\Section::make('카테고리별 분석')
                    ->schema([
                        Components\ViewEntry::make('category_analysis')
                            ->label('')
                            ->view('filament.resources.survey-response.category-analysis')
                            ->viewData(fn ($record) => [
                                'analysisData' => $record->getAnalysisData(),
                            ]),
                    ])
                    ->visible(fn ($record) => count($record->survey->getCategories()) > 0),
                    
                Components\Section::make('상세 응답 내용')
                    ->schema([
                        Components\ViewEntry::make('responses_data')
                            ->label('')
                            ->view('filament.resources.survey-response.detailed-responses')
                            ->viewData(fn ($record) => [
                                'responses' => $record->responses_data ?? [],
                                'survey' => $record->survey,
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }
}
