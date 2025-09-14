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
use League\Csv\Writer;
use Illuminate\Support\Facades\Response;
use App\Helpers\PermissionHelper;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = '설문';
    
    protected static ?int $navigationSort = 70;
    
    protected static ?string $navigationLabel = '설문 문항 관리';
    
    protected static ?string $modelLabel = '설문';
    
    protected static ?string $pluralModelLabel = '설문 목록';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 설문 유형 표시 (수정 시에만)
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('survey_type')
                            ->label('설문 유형')
                            ->content(function ($record) {
                                if (!$record) return '새 설문 (간편 분석)';
                                if ($record->is_detailed) {
                                    $parent = $record->parent;
                                    return '심층 분석 문항' . ($parent ? ' (원본: ' . $parent->getTitle('kor') . ')' : '');
                                }
                                $detailed = $record->detailedVersion;
                                return '간편 분석 문항' . ($detailed ? ' (심층 버전 있음)' : '');
                            })
                            ->helperText(function ($record) {
                                if (!$record) return '저장 후 심층 문항을 생성할 수 있습니다.';
                                if ($record->is_detailed) return '이 설문은 심층 분석 전용입니다.';
                                if (!$record->detailedVersion) return '액션 메뉴에서 심층 문항을 생성할 수 있습니다.';
                                return '이미 심층 버전이 존재합니다.';
                            }),
                    ])
                    ->collapsible(false)
                    ->visible(fn ($record) => $record !== null),
                    
                // 기본 정보 섹션 - 모든 언어 표시
                Forms\Components\Section::make('기본 정보')
                    ->description('모든 언어의 설문 제목과 설명을 입력하세요.')
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                // 한국어
                                Forms\Components\Fieldset::make('한국어')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_translations.kor')
                                            ->label('설문 제목')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_translations.kor')
                                            ->label('설문 설명')
                                            ->rows(2),
                                    ])
                                    ->columns(1),
                                    
                                // 영어
                                Forms\Components\Fieldset::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_translations.eng')
                                            ->label('Survey Title')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_translations.eng')
                                            ->label('Survey Description')
                                            ->rows(2),
                                    ])
                                    ->columns(1),
                                    
                                // 중국어
                                Forms\Components\Fieldset::make('中文')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_translations.chn')
                                            ->label('问卷标题')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_translations.chn')
                                            ->label('问卷说明')
                                            ->rows(2),
                                    ])
                                    ->columns(1),
                                    
                                // 힌디어
                                Forms\Components\Fieldset::make('हिन्दी')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_translations.hin')
                                            ->label('सर्वेक्षण शीर्षक')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_translations.hin')
                                            ->label('सर्वेक्षण विवरण')
                                            ->rows(2),
                                    ])
                                    ->columns(1),
                                    
                                // 아랍어
                                Forms\Components\Fieldset::make('العربية')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_translations.arb')
                                            ->label('عنوان الاستبيان')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_translations.arb')
                                            ->label('وصف الاستبيان')
                                            ->rows(2),
                                    ])
                                    ->columns(1),
                            ]),
                    ]),
                    
                // 체크리스트 항목 섹션
                Forms\Components\Section::make('체크리스트 항목')
                    ->description('설문 응답 선택 항목을 설정합니다. 모든 언어의 번역을 함께 입력할 수 있습니다.')
                    ->schema([
                        Forms\Components\Repeater::make('checklist_items_translations')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(6)
                                    ->schema([
                                        // 점수는 공통
                                        Forms\Components\TextInput::make('score')
                                            ->label('점수')
                                            ->numeric()
                                            ->required()
                                            ->columnSpan(1),
                                            
                                        // 각 언어별 레이블
                                        Forms\Components\TextInput::make('label_kor')
                                            ->label('한국어')
                                            ->required()
                                            ->placeholder('항목명')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_eng')
                                            ->label('English')
                                            ->placeholder('Item name')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_chn')
                                            ->label('中文')
                                            ->placeholder('项目名称')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_hin')
                                            ->label('हिन्दी')
                                            ->placeholder('आइटम नाम')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_arb')
                                            ->label('العربية')
                                            ->placeholder('اسم العنصر')
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->defaultItems(5)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(function (array $state): ?string {
                                if (!empty($state['label_kor'])) {
                                    return $state['label_kor'] . ' (점수: ' . ($state['score'] ?? '0') . ')';
                                }
                                return '새 항목';
                            })
                            ->mutateDehydratedStateUsing(function ($state) {
                                // 저장 시 언어별로 분리하여 저장
                                $languages = ['kor', 'eng', 'chn', 'hin', 'arb'];
                                $result = [];
                                
                                // state가 비어있으면 빈 배열 반환
                                if (empty($state)) {
                                    foreach ($languages as $lang) {
                                        $result[$lang] = [];
                                    }
                                    return $result;
                                }
                                
                                foreach ($languages as $lang) {
                                    $result[$lang] = [];
                                    foreach ($state as $item) {
                                        if (!empty($item['label_' . $lang]) || $lang === 'kor') {
                                            $result[$lang][] = [
                                                'label' => $item['label_' . $lang] ?? '',
                                                'score' => $item['score'] ?? 0
                                            ];
                                        }
                                    }
                                }
                                
                                return $result;
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;
                                
                                // 기존 데이터를 통합 형태로 변환
                                $unified = [];
                                $korItems = $record->checklist_items_translations['kor'] ?? $record->checklist_items ?? [];
                                
                                foreach ($korItems as $index => $korItem) {
                                    $unifiedItem = [
                                        'score' => $korItem['score'] ?? 0,
                                        'label_kor' => $korItem['label'] ?? ''
                                    ];
                                    
                                    // 다른 언어 데이터 추가
                                    $languages = ['eng', 'chn', 'hin', 'arb'];
                                    foreach ($languages as $lang) {
                                        $langItems = $record->checklist_items_translations[$lang] ?? [];
                                        if (isset($langItems[$index])) {
                                            $unifiedItem['label_' . $lang] = $langItems[$index]['label'] ?? '';
                                        }
                                    }
                                    
                                    $unified[] = $unifiedItem;
                                }
                                
                                $component->state($unified);
                            }),
                    ]),
                    
                // 빈도 평가 항목 섹션
                Forms\Components\Section::make('빈도 평가 항목')
                    ->description('설문 응답의 빈도 평가 항목을 설정합니다. 기본 5개 항목이 제공되며 수정 가능합니다.')
                    ->schema([
                        Forms\Components\Repeater::make('frequency_items_translations')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(6)
                                    ->schema([
                                        // 점수는 공통
                                        Forms\Components\TextInput::make('score')
                                            ->label('점수')
                                            ->numeric()
                                            ->required()
                                            ->columnSpan(1),
                                            
                                        // 각 언어별 레이블
                                        Forms\Components\TextInput::make('label_kor')
                                            ->label('한국어')
                                            ->required()
                                            ->placeholder('항목명')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_eng')
                                            ->label('English')
                                            ->placeholder('Item name')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_chn')
                                            ->label('中文')
                                            ->placeholder('项目名称')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_hin')
                                            ->label('हिन्दी')
                                            ->placeholder('आइटम नाम')
                                            ->columnSpan(1),
                                            
                                        Forms\Components\TextInput::make('label_arb')
                                            ->label('العربية')
                                            ->placeholder('اسم العنصر')
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->default([
                                ['score' => 0, 'label_kor' => '전혀 없었다', 'label_eng' => 'Never', 'label_chn' => '从未', 'label_hin' => 'कभी नहीं', 'label_arb' => 'أبداً'],
                                ['score' => 1, 'label_kor' => '1~2회 있었다', 'label_eng' => '1-2 times', 'label_chn' => '1-2次', 'label_hin' => '1-2 बार', 'label_arb' => '1-2 مرات'],
                                ['score' => 2, 'label_kor' => '3~4회 있었다', 'label_eng' => '3-4 times', 'label_chn' => '3-4次', 'label_hin' => '3-4 बार', 'label_arb' => '3-4 مرات'],
                                ['score' => 3, 'label_kor' => '거의 매일 있었다', 'label_eng' => 'Almost daily', 'label_chn' => '几乎每天', 'label_hin' => 'लगभग हर दिन', 'label_arb' => 'تقريباً كل يوم'],
                                ['score' => 4, 'label_kor' => '하루에 여러 번 있었다', 'label_eng' => 'Multiple times a day', 'label_chn' => '每天多次', 'label_hin' => 'दिन में कई बार', 'label_arb' => 'عدة مرات في اليوم'],
                            ])
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (!empty($state['label_kor'])) {
                                    return $state['label_kor'] . ' (점수: ' . ($state['score'] ?? '0') . ')';
                                }
                                return '새 항목';
                            })
                            ->mutateDehydratedStateUsing(function ($state) {
                                // 저장 시 언어별로 분리하여 저장
                                $languages = ['kor', 'eng', 'chn', 'hin', 'arb'];
                                $result = [];
                                
                                // state가 비어있으면 빈 배열 반환
                                if (empty($state)) {
                                    foreach ($languages as $lang) {
                                        $result[$lang] = [];
                                    }
                                    return $result;
                                }
                                
                                foreach ($languages as $lang) {
                                    $result[$lang] = [];
                                    foreach ($state as $item) {
                                        if (!empty($item['label_' . $lang]) || $lang === 'kor') {
                                            $result[$lang][] = [
                                                'label' => $item['label_' . $lang] ?? '',
                                                'score' => $item['score'] ?? 0
                                            ];
                                        }
                                    }
                                }
                                
                                return $result;
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;
                                
                                // 기존 데이터를 통합 형태로 변환
                                $unified = [];
                                $korItems = $record->frequency_items_translations['kor'] ?? $record->frequency_items ?? [];
                                
                                foreach ($korItems as $index => $korItem) {
                                    $unifiedItem = [
                                        'score' => $korItem['score'] ?? 0,
                                        'label_kor' => $korItem['label'] ?? ''
                                    ];
                                    
                                    // 다른 언어 데이터 추가
                                    $languages = ['eng', 'chn', 'hin', 'arb'];
                                    foreach ($languages as $lang) {
                                        $langItems = $record->frequency_items_translations[$lang] ?? [];
                                        if (isset($langItems[$index])) {
                                            $unifiedItem['label_' . $lang] = $langItems[$index]['label'] ?? '';
                                        }
                                    }
                                    
                                    $unified[] = $unifiedItem;
                                }
                                
                                $component->state($unified);
                            }),
                    ]),
                    
                // 설문 문항 섹션
                Forms\Components\Section::make('설문 문항')
                    ->description('설문 문항을 입력합니다. 각 문항에 대해 모든 언어의 번역을 함께 입력할 수 있습니다.')
                    ->schema([
                        Forms\Components\Repeater::make('questions_translations')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        // 각 언어별 문항
                                        Forms\Components\TextInput::make('label_kor')
                                            ->label('한국어 문항')
                                            ->required()
                                            ->placeholder('문항을 입력하세요')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\TextInput::make('label_eng')
                                            ->label('English Question')
                                            ->placeholder('Enter the question')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\TextInput::make('label_chn')
                                            ->label('中文题目')
                                            ->placeholder('请输入题目')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\TextInput::make('label_hin')
                                            ->label('हिन्दी प्रश्न')
                                            ->placeholder('प्रश्न दर्ज करें')
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\TextInput::make('label_arb')
                                            ->label('السؤال بالعربية')
                                            ->placeholder('أدخل السؤال')
                                            ->columnSpanFull(),
                                            
                                        // 개별 체크리스트 설정
                                        Forms\Components\Toggle::make('has_specific_checklist')
                                            ->label('개별 체크리스트 사용')
                                            ->helperText('이 문항에 대해 별도의 응답 항목을 사용합니다.')
                                            ->reactive(),
                                            
                                        Forms\Components\Repeater::make('specific_checklist_items')
                                            ->label('개별 체크리스트 항목')
                                            ->schema([
                                                Forms\Components\Grid::make(6)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('score')
                                                            ->label('점수')
                                                            ->numeric()
                                                            ->required()
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\TextInput::make('label_kor')
                                                            ->label('한국어')
                                                            ->required()
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\TextInput::make('label_eng')
                                                            ->label('English')
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\TextInput::make('label_chn')
                                                            ->label('中文')
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\TextInput::make('label_hin')
                                                            ->label('हिन्दी')
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\TextInput::make('label_arb')
                                                            ->label('العربية')
                                                            ->columnSpan(1),
                                                    ]),
                                            ])
                                            ->visible(fn (Forms\Get $get) => $get('has_specific_checklist'))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(function (array $state): ?string {
                                if (!empty($state['label_kor'])) {
                                    $label = \Str::limit($state['label_kor'], 50);
                                    if ($state['has_specific_checklist'] ?? false) {
                                        $label .= ' (개별 체크리스트)';
                                    }
                                    return $label;
                                }
                                return '새 문항';
                            })
                            ->mutateDehydratedStateUsing(function ($state) {
                                // 저장 시 언어별로 분리하여 저장
                                $languages = ['kor', 'eng', 'chn', 'hin', 'arb'];
                                $result = [];
                                
                                // state가 비어있으면 빈 배열 반환
                                if (empty($state)) {
                                    foreach ($languages as $lang) {
                                        $result[$lang] = [];
                                    }
                                    return $result;
                                }
                                
                                foreach ($languages as $lang) {
                                    $result[$lang] = [];
                                    foreach ($state as $item) {
                                        if (!empty($item['label_' . $lang]) || $lang === 'kor') {
                                            $questionData = [
                                                'label' => $item['label_' . $lang] ?? '',
                                                'has_specific_checklist' => $item['has_specific_checklist'] ?? false
                                            ];
                                            
                                            // 개별 체크리스트가 있는 경우
                                            if ($questionData['has_specific_checklist'] && !empty($item['specific_checklist_items'])) {
                                                $specificItems = [];
                                                foreach ($item['specific_checklist_items'] as $checkItem) {
                                                    if (!empty($checkItem['label_' . $lang]) || $lang === 'kor') {
                                                        $specificItems[] = [
                                                            'label' => $checkItem['label_' . $lang] ?? '',
                                                            'score' => $checkItem['score'] ?? 0
                                                        ];
                                                    }
                                                }
                                                $questionData['specific_checklist_items'] = $specificItems;
                                            }
                                            
                                            $result[$lang][] = $questionData;
                                        }
                                    }
                                }
                                
                                return $result;
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;
                                
                                // 기존 데이터를 통합 형태로 변환
                                $unified = [];
                                $korQuestions = $record->questions_translations['kor'] ?? $record->questions ?? [];
                                
                                foreach ($korQuestions as $index => $korQuestion) {
                                    $unifiedQuestion = [
                                        'label_kor' => $korQuestion['label'] ?? '',
                                        'has_specific_checklist' => $korQuestion['has_specific_checklist'] ?? false
                                    ];
                                    
                                    // 다른 언어 데이터 추가
                                    $languages = ['eng', 'chn', 'hin', 'arb'];
                                    foreach ($languages as $lang) {
                                        $langQuestions = $record->questions_translations[$lang] ?? [];
                                        if (isset($langQuestions[$index])) {
                                            $unifiedQuestion['label_' . $lang] = $langQuestions[$index]['label'] ?? '';
                                        }
                                    }
                                    
                                    // 개별 체크리스트 처리
                                    if ($unifiedQuestion['has_specific_checklist']) {
                                        $unifiedChecklist = [];
                                        $korChecklist = $korQuestion['specific_checklist_items'] ?? [];
                                        
                                        foreach ($korChecklist as $checkIndex => $korCheckItem) {
                                            $unifiedCheckItem = [
                                                'score' => $korCheckItem['score'] ?? 0,
                                                'label_kor' => $korCheckItem['label'] ?? ''
                                            ];
                                            
                                            foreach ($languages as $lang) {
                                                $langQuestions = $record->questions_translations[$lang] ?? [];
                                                if (isset($langQuestions[$index]['specific_checklist_items'][$checkIndex])) {
                                                    $unifiedCheckItem['label_' . $lang] = $langQuestions[$index]['specific_checklist_items'][$checkIndex]['label'] ?? '';
                                                }
                                            }
                                            
                                            $unifiedChecklist[] = $unifiedCheckItem;
                                        }
                                        
                                        $unifiedQuestion['specific_checklist_items'] = $unifiedChecklist;
                                    }
                                    
                                    $unified[] = $unifiedQuestion;
                                }
                                
                                $component->state($unified);
                            }),
                    ]),
                    
                // 기본 필드들 (숨김 처리)
                Forms\Components\Hidden::make('title')
                    ->default('Survey'),
                Forms\Components\Hidden::make('description'),
                Forms\Components\Hidden::make('checklist_items')
                    ->default([]),
                Forms\Components\Hidden::make('questions')
                    ->default([]),
                Forms\Components\Hidden::make('frequency_items')
                    ->default([]),
                    
                // 설문 이미지 섹션
                Forms\Components\Section::make('설문 이미지')
                    ->description('설문과 관련된 이미지를 업로드할 수 있습니다.')
                    ->schema([
                        Forms\Components\FileUpload::make('survey_image')
                            ->label('설문 이미지')
                            ->image()
                            ->disk('public')
                            ->directory('survey-images')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('업로드 가능한 파일: JPEG, PNG, WebP (최대 5MB)')
                            ->columnSpanFull(),
                    ]),
                    
                // 문항 카테고리 섹션
                Forms\Components\Section::make('문항 카테고리')
                    ->schema([
                        Forms\Components\Repeater::make('question_categories')
                            ->label('카테고리 목록')
                            ->schema([
                                Forms\Components\Tabs::make('Category Names')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('한국어')
                                            ->schema([
                                                Forms\Components\TextInput::make('category_name_kor')
                                                    ->label('카테고리명 (한국어)')
                                                    ->required(),
                                            ]),
                                        Forms\Components\Tabs\Tab::make('English')
                                            ->schema([
                                                Forms\Components\TextInput::make('category_name_eng')
                                                    ->label('Category Name (English)'),
                                            ]),
                                        Forms\Components\Tabs\Tab::make('中文')
                                            ->schema([
                                                Forms\Components\TextInput::make('category_name_chn')
                                                    ->label('类别名称 (中文)'),
                                            ]),
                                        Forms\Components\Tabs\Tab::make('हिन्दी')
                                            ->schema([
                                                Forms\Components\TextInput::make('category_name_hin')
                                                    ->label('श्रेणी का नाम (हिन्दी)'),
                                            ]),
                                        Forms\Components\Tabs\Tab::make('العربية')
                                            ->schema([
                                                Forms\Components\TextInput::make('category_name_arb')
                                                    ->label('اسم الفئة (العربية)'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('question_indices')
                                    ->label('문항 선택')
                                    ->multiple()
                                    ->required()
                                    ->options(function (Forms\Get $get) {
                                        // 통합된 문항 데이터에서 옵션 생성 (한국어만)
                                        $questions = $get('../../questions_translations') ?? $get('../../questions_unified') ?? [];
                                        $options = [];
                                        
                                        foreach ($questions as $index => $question) {
                                            if (!empty($question['label_kor'])) {
                                                $displayNumber = $index + 1;
                                                $options[$index] = $displayNumber . '. ' . $question['label_kor'];
                                            }
                                        }
                                        
                                        return $options;
                                    })
                                    ->reactive()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => 
                                !empty($state['category_name_kor']) 
                                    ? $state['category_name_kor'] . ' (' . count($state['question_indices'] ?? []) . '개 문항)'
                                    : '새 카테고리'
                            )
                            ->defaultItems(0)
                            ->helperText('문항들을 카테고리별로 그룹화할 수 있습니다. 각 문항은 하나의 카테고리에만 속할 수 있습니다.')
                            ->mutateDehydratedStateUsing(function ($state, Forms\Get $get) {
                                // 카테고리 데이터를 저장 형식으로 변환
                                if (!is_array($state)) return [];
                                
                                // 카테고리 설명 데이터 가져오기
                                $descriptions = $get('../category_descriptions') ?? [];
                                $descriptionMap = [];
                                
                                foreach ($descriptions as $desc) {
                                    if (isset($desc['category_index'])) {
                                        $descriptionMap[$desc['category_index']] = $desc;
                                    }
                                }
                                
                                $categories = [];
                                foreach ($state as $index => $category) {
                                    $desc = $descriptionMap[$index] ?? [];
                                    
                                    // 다국어 데이터 수집
                                    $categoryData = [
                                        'name' => $category['category_name_kor'] ?? '',
                                        'question_indices' => $category['question_indices'] ?? [],
                                        'translations' => [
                                            'kor' => [
                                                'name' => $category['category_name_kor'] ?? '',
                                                'description' => $desc['category_description_kor'] ?? '',
                                                'result_description' => $desc['result_description_kor'] ?? '',
                                            ],
                                            'eng' => [
                                                'name' => $category['category_name_eng'] ?? '',
                                                'description' => $desc['category_description_eng'] ?? '',
                                                'result_description' => $desc['result_description_eng'] ?? '',
                                            ],
                                            'chn' => [
                                                'name' => $category['category_name_chn'] ?? '',
                                                'description' => $desc['category_description_chn'] ?? '',
                                                'result_description' => $desc['result_description_chn'] ?? '',
                                            ],
                                            'hin' => [
                                                'name' => $category['category_name_hin'] ?? '',
                                                'description' => $desc['category_description_hin'] ?? '',
                                                'result_description' => $desc['result_description_hin'] ?? '',
                                            ],
                                            'arb' => [
                                                'name' => $category['category_name_arb'] ?? '',
                                                'description' => $desc['category_description_arb'] ?? '',
                                                'result_description' => $desc['result_description_arb'] ?? '',
                                            ],
                                        ]
                                    ];
                                    $categories[] = $categoryData;
                                }
                                
                                return ['categories' => $categories];
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record) return;
                                
                                // 저장된 카테고리 데이터 가져오기
                                $savedData = get_option('survey_categories_' . $record->id);
                                if (!$savedData || !isset($savedData['categories'])) return;
                                
                                // 저장된 데이터를 폼 형식으로 변환 (다국어 카테고리명과 문항)
                                $formData = [];
                                foreach ($savedData['categories'] as $category) {
                                    // 새로운 데이터 형식 처리 (name이 배열인 경우)
                                    if (isset($category['name']) && is_array($category['name'])) {
                                        $formCategory = [
                                            'category_name_kor' => $category['name']['kor'] ?? '',
                                            'category_name_eng' => $category['name']['eng'] ?? '',
                                            'category_name_chn' => $category['name']['chn'] ?? '',
                                            'category_name_hin' => $category['name']['hin'] ?? '',
                                            'category_name_arb' => $category['name']['arb'] ?? '',
                                            'question_indices' => $category['question_indices'] ?? [],
                                        ];
                                    } 
                                    // 기존 데이터 형식 처리 (translations 구조)
                                    else {
                                        $formCategory = [
                                            'category_name_kor' => $category['translations']['kor']['name'] ?? $category['name'] ?? '',
                                            'category_name_eng' => $category['translations']['eng']['name'] ?? '',
                                            'category_name_chn' => $category['translations']['chn']['name'] ?? '',
                                            'category_name_hin' => $category['translations']['hin']['name'] ?? '',
                                            'category_name_arb' => $category['translations']['arb']['name'] ?? '',
                                            'question_indices' => $category['question_indices'] ?? [],
                                        ];
                                    }
                                    $formData[] = $formCategory;
                                }
                                
                                $component->state($formData);
                            })
                    ])
                    ->description('설문 문항을 먼저 생성한 후, 카테고리를 설정할 수 있습니다.')
                    ->visible(fn (Forms\Get $get) => count($get('questions_translations') ?? $get('questions_unified') ?? []) > 0),
                    
                // 결과 해설 섹션
                Forms\Components\Section::make('노화지수별 결과 해설')
                    ->description('노화지수 구간에 따라 다른 해설을 입력합니다.')
                    ->schema([
                        Forms\Components\Tabs::make('Result Commentary by Aging Index')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('한국어')
                                    ->schema([
                                        Forms\Components\RichEditor::make('result_commentary.kor.excellent')
                                            ->label('최적 (0~15%)')
                                            ->helperText('노화지수 0~15%일 때 표시될 해설')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.kor.good')
                                            ->label('우수 (16~30%)')
                                            ->helperText('노화지수 16~30%일 때 표시될 해설')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.kor.fair')
                                            ->label('양호 (31~50%)')
                                            ->helperText('노화지수 31~50%일 때 표시될 해설')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.kor.caution')
                                            ->label('주의 (51~70%)')
                                            ->helperText('노화지수 51~70%일 때 표시될 해설')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.kor.danger')
                                            ->label('위험 (71~85%)')
                                            ->helperText('노화지수 71~85%일 때 표시될 해설')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.kor.critical')
                                            ->label('붕괴 (86~100%)')
                                            ->helperText('노화지수 86~100%일 때 표시될 해설')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\RichEditor::make('result_commentary.eng.excellent')
                                            ->label('Excellent (0~15%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.eng.good')
                                            ->label('Good (16~30%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.eng.fair')
                                            ->label('Fair (31~50%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.eng.caution')
                                            ->label('Caution (51~70%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.eng.danger')
                                            ->label('Danger (71~85%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.eng.critical')
                                            ->label('Critical (86~100%)')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('中文')
                                    ->schema([
                                        Forms\Components\RichEditor::make('result_commentary.chn.excellent')
                                            ->label('最佳 (0~15%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.chn.good')
                                            ->label('优秀 (16~30%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.chn.fair')
                                            ->label('良好 (31~50%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.chn.caution')
                                            ->label('注意 (51~70%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.chn.danger')
                                            ->label('危险 (71~85%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.chn.critical')
                                            ->label('严重 (86~100%)')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('हिन्दी')
                                    ->schema([
                                        Forms\Components\RichEditor::make('result_commentary.hin.excellent')
                                            ->label('उत्कृष्ट (0~15%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.hin.good')
                                            ->label('अच्छा (16~30%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.hin.fair')
                                            ->label('ठीक (31~50%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.hin.caution')
                                            ->label('सावधानी (51~70%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.hin.danger')
                                            ->label('खतरा (71~85%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.hin.critical')
                                            ->label('गंभीर (86~100%)')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('العربية')
                                    ->schema([
                                        Forms\Components\RichEditor::make('result_commentary.arb.excellent')
                                            ->label('ممتاز (0~15%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.arb.good')
                                            ->label('جيد (16~30%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.arb.fair')
                                            ->label('مقبول (31~50%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.arb.caution')
                                            ->label('تحذير (51~70%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.arb.danger')
                                            ->label('خطر (71~85%)')
                                            ->columnSpanFull(),
                                        Forms\Components\RichEditor::make('result_commentary.arb.critical')
                                            ->label('حرج (86~100%)')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                    ]),
                    
                // 카테고리 분석 설명 섹션
                Forms\Components\Section::make('카테고리 분석 설명')
                    ->description('카테고리별 분석에 대한 전체적인 설명을 입력합니다.')
                    ->schema([
                        Forms\Components\Tabs::make('Category Analysis Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('한국어')
                                    ->schema([
                                        Forms\Components\RichEditor::make('category_analysis_description.kor')
                                            ->label('카테고리 분석 설명')
                                            ->helperText('카테고리별 분석 결과에 대한 전체적인 설명을 입력하세요.')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\RichEditor::make('category_analysis_description.eng')
                                            ->label('Category Analysis Description')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('中文')
                                    ->schema([
                                        Forms\Components\RichEditor::make('category_analysis_description.chn')
                                            ->label('类别分析说明')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('हिन्दी')
                                    ->schema([
                                        Forms\Components\RichEditor::make('category_analysis_description.hin')
                                            ->label('श्रेणी विश्लेषण विवरण')
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('العربية')
                                    ->schema([
                                        Forms\Components\RichEditor::make('category_analysis_description.arb')
                                            ->label('وصف تحليل الفئة')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('유형')
                    ->getStateUsing(function ($record) {
                        if ($record->is_detailed) return '심층';
                        if ($record->detailedVersion) return '간편';
                        return '간편';
                    })
                    ->colors([
                        'success' => '간편',
                        'warning' => '심층',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => '간편',
                        'heroicon-o-academic-cap' => '심층',
                    ]),
                Tables\Columns\TextColumn::make('title')
                    ->label('설문 제목')
                    ->getStateUsing(function ($record) {
                        $title = $record->getTitle('kor');
                        if ($record->is_detailed && $record->parent_id) {
                            return '　└ ' . $title;  // 들여쓰기 표시
                        }
                        return $title;
                    })
                    ->searchable()
                    ->sortable()
                    ->html(),
                Tables\Columns\TextColumn::make('description')
                    ->label('설명')
                    ->getStateUsing(fn ($record) => $record->getDescription('kor'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('문항 수')
                    ->getStateUsing(function ($record) {
                        if (!$record) return 0;
                        $questions = $record->getQuestions('kor');
                        return $questions ? count($questions) : 0;
                    }),
                Tables\Columns\ImageColumn::make('survey_image')
                    ->label('이미지')
                    ->disk('public')
                    ->height(60)
                    ->width(80)
                    ->defaultImageUrl('/images/no-image.png')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('createDetailedVersion')
                    ->label('심층 문항 생성')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('success')
                    ->modalHeading('심층 분석 문항 생성')
                    ->modalDescription('이 설문의 심층 분석 버전을 생성합니다.')
                    ->form([
                        Forms\Components\Radio::make('copy_content')
                            ->label('문항 복사 옵션')
                            ->options([
                                'copy_all' => '모든 문항과 카테고리 정보 복사',
                                'empty' => '빈 심층 문항으로 생성 (직접 입력)'
                            ])
                            ->default('copy_all')
                            ->required()
                            ->helperText('복사된 문항은 심층 분석 전용으로 수정할 수 있습니다.')
                    ])
                    ->action(function (array $data, $record) {
                        // 이미 심층 버전이 있는지 확인
                        if ($record->detailedVersion()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('이미 심층 분석 버전이 존재합니다')
                                ->danger()
                                ->send();
                            return;
                        }

                        $detailedSurvey = new \App\Models\Survey();
                        
                        if ($data['copy_content'] === 'copy_all') {
                            // 모든 데이터 복사
                            $detailedSurvey->fill($record->toArray());
                            $detailedSurvey->title = $record->title . ' (심층 분석)';
                            
                            // 다국어 제목도 업데이트
                            $titleTranslations = $record->title_translations ?? [];
                            foreach ($titleTranslations as $lang => $title) {
                                $titleTranslations[$lang] = $title . ' (Detailed)';
                            }
                            $detailedSurvey->title_translations = $titleTranslations;
                        } else {
                            // 빈 설문 생성
                            $detailedSurvey->title = $record->title . ' (심층 분석)';
                            $detailedSurvey->description = '심층 분석을 위한 설문입니다.';
                            $detailedSurvey->questions = [];
                            $detailedSurvey->checklist_items = [];
                            $detailedSurvey->frequency_items = [];
                        }
                        
                        // 부모 ID와 심층 여부 설정
                        $detailedSurvey->parent_id = $record->id;
                        $detailedSurvey->is_detailed = true;
                        
                        $detailedSurvey->save();
                        
                        // 카테고리 데이터도 복사
                        if ($data['copy_content'] === 'copy_all') {
                            $originalCategories = get_option('survey_categories_' . $record->id);
                            if ($originalCategories) {
                                update_option('survey_categories_' . $detailedSurvey->id, $originalCategories);
                            }
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('심층 분석 문항이 생성되었습니다')
                            ->success()
                            ->send();
                            
                        // 생성된 심층 문항 편집 페이지로 이동
                        return redirect()->route('filament.admin.resources.surveys.edit', $detailedSurvey);
                    })
                    ->visible(function ($record) {
                        // 간편 분석 문항이고 심층 버전이 없는 경우에만 표시
                        return $record && !$record->is_detailed && !$record->parent_id && !$record->detailedVersion()->exists();
                    })
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('viewCategories')
                    ->label('카테고리 보기')
                    ->icon('heroicon-o-tag')
                    ->modalHeading(function ($record) {
                        if (!$record) return '카테고리 정보';
                        return $record->getTitle('kor') . ' - 카테고리 정보';
                    })
                    ->modalContent(function ($record) {
                        if (!$record) {
                            return view('filament.components.empty-state', [
                                'message' => '설문을 찾을 수 없습니다.'
                            ]);
                        }
                        
                        $savedData = get_option('survey_categories_' . $record->id);
                        
                        if (!$savedData || !isset($savedData['categories']) || count($savedData['categories']) === 0) {
                            return view('filament.components.empty-state', [
                                'message' => '설정된 카테고리가 없습니다.'
                            ]);
                        }
                        
                        $categories = $savedData['categories'];
                        $questions = $record->getQuestions('kor') ?? [];
                        
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
                    ->visible(function ($record) {
                        if (!$record || !$record->id) {
                            return false;
                        }
                        
                        try {
                            $savedData = get_option('survey_categories_' . $record->id);
                            return $savedData && isset($savedData['categories']) && count($savedData['categories']) > 0;
                        } catch (\Exception $e) {
                            return false;
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('export')
                    ->label('선택항목 CSV 내보내기')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        $csv = Writer::createFromString('');
                        $csv->setOutputBOM(Writer::BOM_UTF8);
                        
                        // CSV 헤더 정의
                        $headers = [
                            'ID',
                            '제목 (한국어)',
                            '제목 (영어)',
                            '제목 (중국어)',
                            '제목 (힌디어)',
                            '제목 (아랍어)',
                            '설명 (한국어)',
                            '설명 (영어)',
                            '설명 (중국어)',
                            '설명 (힌디어)',
                            '설명 (아랍어)',
                            '체크리스트1 점수',
                            '체크리스트1 한국어',
                            '체크리스트1 영어',
                            '체크리스트2 점수',
                            '체크리스트2 한국어',
                            '체크리스트2 영어',
                            '체크리스트3 점수',
                            '체크리스트3 한국어',
                            '체크리스트3 영어',
                            '체크리스트4 점수',
                            '체크리스트4 한국어',
                            '체크리스트4 영어',
                            '체크리스트5 점수',
                            '체크리스트5 한국어',
                            '체크리스트5 영어',
                            '질문1 (한국어)',
                            '질문1 (영어)',
                            '질문2 (한국어)',
                            '질문2 (영어)',
                            '질문3 (한국어)',
                            '질문3 (영어)',
                            '질문4 (한국어)',
                            '질문4 (영어)',
                            '질문5 (한국어)',
                            '질문5 (영어)',
                            '빈도항목1 (한국어)',
                            '빈도항목1 (영어)',
                            '빈도항목2 (한국어)',
                            '빈도항목2 (영어)',
                            '빈도항목3 (한국어)',
                            '빈도항목3 (영어)',
                            '이미지',
                            '생성일',
                            '수정일',
                        ];
                        
                        $csv->insertOne($headers);
                        
                        foreach ($records as $record) {
                            // Parse title translations
                            $titleTrans = is_string($record->title_translations) 
                                ? json_decode($record->title_translations, true) 
                                : $record->title_translations;
                            
                            // Parse description translations
                            $descTrans = is_string($record->description_translations) 
                                ? json_decode($record->description_translations, true) 
                                : $record->description_translations;
                            
                            // Parse checklist items translations
                            $checklistTrans = is_string($record->checklist_items_translations) 
                                ? json_decode($record->checklist_items_translations, true) 
                                : $record->checklist_items_translations;
                            
                            // Parse checklist items
                            $checklistItems = is_string($record->checklist_items) 
                                ? json_decode($record->checklist_items, true) 
                                : $record->checklist_items;
                            
                            // Parse questions translations
                            $questionsTrans = is_string($record->questions_translations) 
                                ? json_decode($record->questions_translations, true) 
                                : $record->questions_translations;
                            
                            // Parse frequency items translations
                            $freqTrans = is_string($record->frequency_items_translations) 
                                ? json_decode($record->frequency_items_translations, true) 
                                : $record->frequency_items_translations;
                            
                            $row = [
                                $record->id,
                                isset($titleTrans['kor']) ? $titleTrans['kor'] : '',
                                isset($titleTrans['eng']) ? $titleTrans['eng'] : '',
                                isset($titleTrans['chn']) ? $titleTrans['chn'] : '',
                                isset($titleTrans['hin']) ? $titleTrans['hin'] : '',
                                isset($titleTrans['arb']) ? $titleTrans['arb'] : '',
                                isset($descTrans['kor']) ? $descTrans['kor'] : '',
                                isset($descTrans['eng']) ? $descTrans['eng'] : '',
                                isset($descTrans['chn']) ? $descTrans['chn'] : '',
                                isset($descTrans['hin']) ? $descTrans['hin'] : '',
                                isset($descTrans['arb']) ? $descTrans['arb'] : '',
                            ];
                            
                            // Add checklist items (up to 5)
                            for ($i = 0; $i < 5; $i++) {
                                if (isset($checklistItems[$i])) {
                                    $row[] = isset($checklistItems[$i]['score']) ? $checklistItems[$i]['score'] : '';
                                    $row[] = isset($checklistTrans['kor'][$i]['label']) ? $checklistTrans['kor'][$i]['label'] : '';
                                    $row[] = isset($checklistTrans['eng'][$i]['label']) ? $checklistTrans['eng'][$i]['label'] : '';
                                } else {
                                    $row[] = '';
                                    $row[] = '';
                                    $row[] = '';
                                }
                            }
                            
                            // Add questions (up to 5)
                            for ($i = 0; $i < 5; $i++) {
                                $row[] = isset($questionsTrans['kor'][$i]['label']) ? $questionsTrans['kor'][$i]['label'] : '';
                                $row[] = isset($questionsTrans['eng'][$i]['label']) ? $questionsTrans['eng'][$i]['label'] : '';
                            }
                            
                            // Add frequency items (up to 3)
                            for ($i = 0; $i < 3; $i++) {
                                $row[] = isset($freqTrans['kor'][$i]['label']) ? $freqTrans['kor'][$i]['label'] : '';
                                $row[] = isset($freqTrans['eng'][$i]['label']) ? $freqTrans['eng'][$i]['label'] : '';
                            }
                            
                            $row[] = $record->survey_image;
                            $row[] = $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '';
                            $row[] = $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : '';
                            
                            $csv->insertOne($row);
                        }
                        
                        $content = $csv->toString();
                        
                        return Response::streamDownload(
                            function () use ($content) {
                                echo $content;
                            },
                            'surveys_export_' . date('Y-m-d_His') . '.csv',
                            [
                                'Content-Type' => 'text/csv; charset=UTF-8',
                                'Content-Encoding' => 'UTF-8',
                            ]
                        );
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all')
                    ->label('전체 CSV 내보내기')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $records = Survey::all();
                        
                        $csv = Writer::createFromString('');
                        $csv->setOutputBOM(Writer::BOM_UTF8);
                        
                        // CSV 헤더 정의
                        $headers = [
                            'ID',
                            '제목 (한국어)',
                            '제목 (영어)',
                            '제목 (중국어)',
                            '제목 (힌디어)',
                            '제목 (아랍어)',
                            '설명 (한국어)',
                            '설명 (영어)',
                            '설명 (중국어)',
                            '설명 (힌디어)',
                            '설명 (아랍어)',
                            '체크리스트1 점수',
                            '체크리스트1 한국어',
                            '체크리스트1 영어',
                            '체크리스트2 점수',
                            '체크리스트2 한국어',
                            '체크리스트2 영어',
                            '체크리스트3 점수',
                            '체크리스트3 한국어',
                            '체크리스트3 영어',
                            '체크리스트4 점수',
                            '체크리스트4 한국어',
                            '체크리스트4 영어',
                            '체크리스트5 점수',
                            '체크리스트5 한국어',
                            '체크리스트5 영어',
                            '질문1 (한국어)',
                            '질문1 (영어)',
                            '질문2 (한국어)',
                            '질문2 (영어)',
                            '질문3 (한국어)',
                            '질문3 (영어)',
                            '질문4 (한국어)',
                            '질문4 (영어)',
                            '질문5 (한국어)',
                            '질문5 (영어)',
                            '빈도항목1 (한국어)',
                            '빈도항목1 (영어)',
                            '빈도항목2 (한국어)',
                            '빈도항목2 (영어)',
                            '빈도항목3 (한국어)',
                            '빈도항목3 (영어)',
                            '이미지',
                            '생성일',
                            '수정일',
                        ];
                        
                        $csv->insertOne($headers);
                        
                        foreach ($records as $record) {
                            // Parse title translations
                            $titleTrans = is_string($record->title_translations) 
                                ? json_decode($record->title_translations, true) 
                                : $record->title_translations;
                            
                            // Parse description translations
                            $descTrans = is_string($record->description_translations) 
                                ? json_decode($record->description_translations, true) 
                                : $record->description_translations;
                            
                            // Parse checklist items translations
                            $checklistTrans = is_string($record->checklist_items_translations) 
                                ? json_decode($record->checklist_items_translations, true) 
                                : $record->checklist_items_translations;
                            
                            // Parse checklist items
                            $checklistItems = is_string($record->checklist_items) 
                                ? json_decode($record->checklist_items, true) 
                                : $record->checklist_items;
                            
                            // Parse questions translations
                            $questionsTrans = is_string($record->questions_translations) 
                                ? json_decode($record->questions_translations, true) 
                                : $record->questions_translations;
                            
                            // Parse frequency items translations
                            $freqTrans = is_string($record->frequency_items_translations) 
                                ? json_decode($record->frequency_items_translations, true) 
                                : $record->frequency_items_translations;
                            
                            $row = [
                                $record->id,
                                isset($titleTrans['kor']) ? $titleTrans['kor'] : '',
                                isset($titleTrans['eng']) ? $titleTrans['eng'] : '',
                                isset($titleTrans['chn']) ? $titleTrans['chn'] : '',
                                isset($titleTrans['hin']) ? $titleTrans['hin'] : '',
                                isset($titleTrans['arb']) ? $titleTrans['arb'] : '',
                                isset($descTrans['kor']) ? $descTrans['kor'] : '',
                                isset($descTrans['eng']) ? $descTrans['eng'] : '',
                                isset($descTrans['chn']) ? $descTrans['chn'] : '',
                                isset($descTrans['hin']) ? $descTrans['hin'] : '',
                                isset($descTrans['arb']) ? $descTrans['arb'] : '',
                            ];
                            
                            // Add checklist items (up to 5)
                            for ($i = 0; $i < 5; $i++) {
                                if (isset($checklistItems[$i])) {
                                    $row[] = isset($checklistItems[$i]['score']) ? $checklistItems[$i]['score'] : '';
                                    $row[] = isset($checklistTrans['kor'][$i]['label']) ? $checklistTrans['kor'][$i]['label'] : '';
                                    $row[] = isset($checklistTrans['eng'][$i]['label']) ? $checklistTrans['eng'][$i]['label'] : '';
                                } else {
                                    $row[] = '';
                                    $row[] = '';
                                    $row[] = '';
                                }
                            }
                            
                            // Add questions (up to 5)
                            for ($i = 0; $i < 5; $i++) {
                                $row[] = isset($questionsTrans['kor'][$i]['label']) ? $questionsTrans['kor'][$i]['label'] : '';
                                $row[] = isset($questionsTrans['eng'][$i]['label']) ? $questionsTrans['eng'][$i]['label'] : '';
                            }
                            
                            // Add frequency items (up to 3)
                            for ($i = 0; $i < 3; $i++) {
                                $row[] = isset($freqTrans['kor'][$i]['label']) ? $freqTrans['kor'][$i]['label'] : '';
                                $row[] = isset($freqTrans['eng'][$i]['label']) ? $freqTrans['eng'][$i]['label'] : '';
                            }
                            
                            $row[] = $record->survey_image;
                            $row[] = $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '';
                            $row[] = $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : '';
                            
                            $csv->insertOne($row);
                        }
                        
                        $content = $csv->toString();
                        
                        return Response::streamDownload(
                            function () use ($content) {
                                echo $content;
                            },
                            'surveys_all_export_' . date('Y-m-d_His') . '.csv',
                            [
                                'Content-Type' => 'text/csv; charset=UTF-8',
                                'Content-Encoding' => 'UTF-8',
                            ]
                        );
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['detailedVersion', 'parent'])
            ->orderByRaw('COALESCE(parent_id, id), is_detailed');
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
    
    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_survey-view') && PermissionHelper::hasPermission('surveys-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('surveys-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('surveys-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('surveys-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('surveys-delete');
    }
    
    public static function canView($record): bool
    {
        return PermissionHelper::hasPermission('surveys-view');
    }
}