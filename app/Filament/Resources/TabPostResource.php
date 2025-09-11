<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TabPostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Str;

class TabPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = '탭';
    
    protected static ?string $modelLabel = '탭';
    
    protected static ?string $pluralModelLabel = '탭';
    
    protected static ?string $postType = Post::TYPE_TAB;
    
    protected static ?string $navigationGroup = '홈 구성';
    
    protected static ?int $navigationSort = 23;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\Hidden::make('type')
                            ->default(static::$postType),
                        
                        Forms\Components\Textarea::make('summary')
                            ->label('요약')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('read_more_text')
                            ->label('더보기 문구')
                            ->default('더 보기')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('컨텐츠 섹션')
                    ->description('탭 형식으로 표시될 4개의 섹션을 입력하세요.')
                    ->schema([
                        Forms\Components\Tabs::make('content_sections')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Overview')
                                    ->label('루틴 정의')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content_sections.overview')
                                            ->label('')
                                            ->required()
                                            ->default(''),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Our Vision')
                                    ->label('왜 루틴이 필요한가?')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content_sections.our_vision')
                                            ->label('')
                                            ->required()
                                            ->default(''),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Research Topics')
                                    ->label('루틴 실천 방법 (How to)')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content_sections.research_topics')
                                            ->label('')
                                            ->required()
                                            ->default(''),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Principles for AI Ethics')
                                    ->label('AI 윤리 원칙')
                                    ->schema([
                                        Forms\Components\RichEditor::make('content_sections.principles_for_ai_ethics')
                                            ->label('AI 윤리 원칙 내용')
                                            ->required()
                                            ->default(''),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('관련 기사')
                    ->schema([
                        Forms\Components\Select::make('related_articles')
                            ->label('관련 기사 선택')
                            ->multiple()
                            ->options(function () {
                                return Post::where('type', 'news')
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->default([])
                            ->columnSpanFull(),
                    ]),
                
                // content 필드는 숨김 처리하고 기본값 설정
                Forms\Components\Hidden::make('content')
                    ->default('탭 게시물은 컨텐츠 섹션을 사용합니다.'),
                
                // author_id 기본값 설정
                Forms\Components\Hidden::make('author_id')
                    ->default(auth()->id() ?? 1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTabPosts::route('/'),
            'create' => Pages\CreateTabPost::route('/create'),
            'edit' => Pages\EditTabPost::route('/{record}/edit'),
        ];
    }
}
