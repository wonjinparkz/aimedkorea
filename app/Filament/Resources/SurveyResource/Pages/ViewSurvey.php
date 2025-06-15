<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('기본 정보')
                    ->schema([
                        Components\TextEntry::make('title')
                            ->label('설문 제목'),
                        Components\TextEntry::make('description')
                            ->label('설문 설명'),
                        Components\TextEntry::make('created_at')
                            ->label('생성일')
                            ->dateTime('Y-m-d H:i'),
                    ])
                    ->columns(2),
                    
                Components\Section::make('문항 정보')
                    ->schema([
                        Components\TextEntry::make('questions_count')
                            ->label('총 문항 수')
                            ->state(fn ($record) => count($record->questions ?? [])),
                        Components\TextEntry::make('categories_count')
                            ->label('카테고리 수')
                            ->state(fn ($record) => count($record->getCategories())),
                    ])
                    ->columns(2),
                    
                Components\Section::make('카테고리별 문항')
                    ->schema([
                        Components\ViewEntry::make('categories_view')
                            ->label('')
                            ->view('filament.resources.survey.categories-detail')
                            ->viewData(fn ($record) => [
                                'categorizedQuestions' => $record->getQuestionsByCategory(),
                                'allQuestions' => $record->getOrderedQuestions(),
                            ]),
                    ])
                    ->visible(fn ($record) => count($record->getCategories()) > 0),
            ]);
    }
}
