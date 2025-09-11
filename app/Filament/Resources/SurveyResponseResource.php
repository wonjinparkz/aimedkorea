<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResponseResource\Pages;
use App\Filament\Resources\SurveyResponseResource\RelationManagers;
use App\Models\SurveyResponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class SurveyResponseResource extends Resource
{
    protected static ?string $model = SurveyResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationGroup = '설문';
    
    protected static ?int $navigationSort = 71;
    
    protected static ?string $navigationLabel = '설문 결과';
    
    protected static ?string $modelLabel = '설문 응답';
    
    protected static ?string $pluralModelLabel = '설문 응답 목록';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('응답 정보')
                    ->schema([
                        Forms\Components\Select::make('survey_id')
                            ->label('설문')
                            ->relationship('survey', 'title')
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->label('사용자')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_score')
                            ->label('총점')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP 주소')
                            ->disabled(),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('응답 데이터')
                    ->schema([
                        Forms\Components\ViewField::make('responses_data')
                            ->label('응답 내용')
                            ->view('filament.forms.components.survey-responses')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('survey.title')
                    ->label('설문')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('사용자')
                    ->searchable()
                    ->default('익명'),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('총점')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 30 => 'success',
                        $state <= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('category_performance')
                    ->label('카테고리 성취도')
                    ->getStateUsing(function ($record) {
                        $categoryScores = $record->getCategoryScores();
                        if (empty($categoryScores)) {
                            return '-';
                        }
                        
                        $highPerformance = 0;
                        $mediumPerformance = 0;
                        $lowPerformance = 0;
                        
                        foreach ($categoryScores as $category) {
                            if ($category['percentage'] >= 70) {
                                $highPerformance++;
                            } elseif ($category['percentage'] >= 40) {
                                $mediumPerformance++;
                            } else {
                                $lowPerformance++;
                            }
                        }
                        
                        $result = [];
                        if ($highPerformance > 0) {
                            $result[] = "우수: {$highPerformance}";
                        }
                        if ($mediumPerformance > 0) {
                            $result[] = "보통: {$mediumPerformance}";
                        }
                        if ($lowPerformance > 0) {
                            $result[] = "개선: {$lowPerformance}";
                        }
                        
                        return implode(', ', $result);
                    })
                    ->html()
                    ->wrap(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP 주소')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('응답일시')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('survey')
                    ->label('설문 선택')
                    ->relationship('survey', 'title'),
                Tables\Filters\Filter::make('high_score')
                    ->label('고득점 (70점 이상)')
                    ->query(fn (Builder $query): Builder => $query->where('total_score', '>=', 70)),
                Tables\Filters\Filter::make('low_score')
                    ->label('저득점 (30점 이하)')
                    ->query(fn (Builder $query): Builder => $query->where('total_score', '<=', 30)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalWidth('7xl'),
                Tables\Actions\Action::make('exportAnalysis')
                    ->label('분석 보고서')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->action(function ($record) {
                        // 추후 PDF 또는 Excel 내보내기 기능 구현
                        // 현재는 모달로 표시
                    })
                    ->modalContent(fn ($record) => view('filament.resources.survey-response.export-preview', [
                        'response' => $record,
                        'analysisData' => $record->getAnalysisData(),
                    ]))
                    ->modalHeading('분석 보고서 미리보기')
                    ->modalWidth('5xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveyResponses::route('/'),
            'view' => Pages\ViewSurveyResponse::route('/{record}'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // 응답은 직접 생성하지 않음
    }
}
