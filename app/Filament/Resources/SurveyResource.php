<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = '설문 관리';
    
    protected static ?string $navigationLabel = '설문 문항 관리';
    
    protected static ?string $modelLabel = '설문';
    
    protected static ?string $pluralModelLabel = '설문 목록';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('설문 제목')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('설문 설명')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('체크리스트 항목')
                    ->schema([
                        Forms\Components\Repeater::make('checklist_items')
                            ->label('선택 항목')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('항목명')
                                    ->required(),
                                Forms\Components\TextInput::make('score')
                                    ->label('점수')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(5)
                            ->collapsible()
                            ->cloneable(),
                    ]),
                
                Forms\Components\Section::make('설문 문항')
                    ->schema([
                        Forms\Components\Repeater::make('questions')
                            ->label('문항 목록')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('문항')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('has_specific_checklist')
                                    ->label('개별 체크리스트 사용')
                                    ->reactive(),
                                Forms\Components\Repeater::make('specific_checklist_items')
                                    ->label('개별 체크리스트 항목')
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label('항목명')
                                            ->required(),
                                        Forms\Components\TextInput::make('score')
                                            ->label('점수')
                                            ->numeric()
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->visible(fn (Forms\Get $get) => $get('has_specific_checklist'))
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                    ]),
                
                Forms\Components\Section::make('문항 카테고리')
                    ->schema([
                        Forms\Components\Repeater::make('question_categories')
                            ->label('카테고리 목록')
                            ->schema([
                                Forms\Components\TextInput::make('category_name')
                                    ->label('카테고리명')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\Select::make('question_indices')
                                    ->label('문항 선택')
                                    ->multiple()
                                    ->required()
                                    ->options(function (Forms\Get $get, $state) {
                                        $questions = $get('../../questions') ?? [];
                                        $allCategories = $get('../') ?? [];
                                        
                                        // 현재 선택된 값들
                                        $currentQuestionIndices = is_array($state) ? $state : [];
                                        
                                        // 다른 카테고리에서 선택된 문항들 수집
                                        $selectedInOtherCategories = [];
                                        foreach ($allCategories as $category) {
                                            if (isset($category['question_indices']) && is_array($category['question_indices'])) {
                                                $categoryIndices = $category['question_indices'];
                                                $categoryName = $category['category_name'] ?? '';
                                                
                                                // 현재 편집 중인 카테고리인지 확인
                                                $isCurrentCategory = ($categoryName === $get('category_name') && 
                                                                    count(array_intersect($categoryIndices, $currentQuestionIndices)) > 0);
                                                
                                                if (!$isCurrentCategory) {
                                                    foreach ($categoryIndices as $idx) {
                                                        $selectedInOtherCategories[] = (string)$idx;
                                                    }
                                                }
                                            }
                                        }
                                        
                                        // 중복 제거
                                        $selectedInOtherCategories = array_unique($selectedInOtherCategories);
                                        
                                        // 문항 배열을 순서대로 처리하여 옵션 생성
                                        $options = [];
                                        $questionIndex = 0;
                                        
                                        foreach ($questions as $key => $question) {
                                            if (!empty($question['label'])) {
                                                $indexStr = (string)$questionIndex;
                                                
                                                // 현재 선택된 항목이거나 다른 카테고리에서 선택되지 않은 항목만 표시
                                                if (in_array($indexStr, array_map('strval', $currentQuestionIndices)) || 
                                                    !in_array($indexStr, $selectedInOtherCategories)) {
                                                    $displayNumber = $questionIndex + 1;
                                                    $options[$indexStr] = $displayNumber . '. ' . $question['label'];
                                                }
                                                
                                                $questionIndex++;
                                            }
                                        }
                                        
                                        return $options;
                                    })
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => 
                                !empty($state['category_name']) 
                                    ? $state['category_name'] . ' (' . count($state['question_indices'] ?? []) . '개 문항)'
                                    : '새 카테고리'
                            )
                            ->defaultItems(0)
                            ->helperText('문항들을 카테고리별로 그룹화할 수 있습니다. 각 문항은 하나의 카테고리에만 속할 수 있습니다.')
                    ])
                    ->description('설문 문항을 먼저 생성한 후, 카테고리를 설정할 수 있습니다.')
                    ->visible(fn (Forms\Get $get) => count($get('questions') ?? []) > 0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('설문 제목')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('설명')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('questions')
                    ->label('문항 수')
                    ->getStateUsing(fn ($record) => count($record->questions ?? [])),
                Tables\Columns\TextColumn::make('categories')
                    ->label('카테고리 수')
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->id) return 0;
                        $savedData = get_option('survey_categories_' . $record->id);
                        return isset($savedData['categories']) ? count($savedData['categories']) : 0;
                    })
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('responses_count')
                    ->label('응답 수')
                    ->counts('responses'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewCategories')
                    ->label('카테고리 보기')
                    ->icon('heroicon-o-tag')
                    ->modalHeading(fn ($record) => $record->title . ' - 카테고리 정보')
                    ->modalContent(function ($record) {
                        $savedData = get_option('survey_categories_' . $record->id);
                        
                        if (!$savedData || !isset($savedData['categories']) || count($savedData['categories']) === 0) {
                            return view('filament.components.empty-state', [
                                'message' => '설정된 카테고리가 없습니다.'
                            ]);
                        }
                        
                        $categories = $savedData['categories'];
                        $questions = $record->questions ?? [];
                        
                        // 문항을 순서대로 정리
                        $orderedQuestions = [];
                        $index = 0;
                        foreach ($questions as $question) {
                            if (!empty($question['label'])) {
                                $orderedQuestions[$index] = $question;
                                $index++;
                            }
                        }
                        
                        return view('filament.resources.survey.categories-modal', [
                            'categories' => $categories,
                            'questions' => $orderedQuestions
                        ]);
                    })
                    ->visible(fn ($record) => 
                        $record && 
                        get_option('survey_categories_' . $record->id) && 
                        count(get_option('survey_categories_' . $record->id)['categories'] ?? []) > 0
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'view' => Pages\ViewSurvey::route('/{record}'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
