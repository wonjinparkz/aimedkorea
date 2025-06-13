<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroResource\Pages;
use App\Models\Hero;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\HtmlString;

class HeroResource extends Resource
{
    protected static ?string $model = Hero::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Hero 슬라이드';
    
    protected static ?string $modelLabel = 'Hero 슬라이드';
    
    protected static ?string $pluralModelLabel = 'Hero 슬라이드';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 실시간 프리뷰 섹션
                Forms\Components\Section::make('프리뷰')
                    ->schema([
                        Forms\Components\ViewField::make('preview')
                            ->label('')
                            ->view('filament.forms.hero-preview'),
                    ])
                    ->collapsible(),
                
                // 기본 정보 섹션
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('제목')
                                    ->required()
                                    ->maxLength(255)
                                    ->reactive()
                                    ->debounce(500),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('부제목')
                                    ->maxLength(255)
                                    ->reactive()
                                    ->debounce(500),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(3)
                            ->reactive()
                            ->debounce(500),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('button_text')
                                    ->label('버튼 텍스트')
                                    ->maxLength(255)
                                    ->reactive()
                                    ->debounce(500),
                                Forms\Components\TextInput::make('button_url')
                                    ->label('버튼 URL')
                                    ->maxLength(255),
                            ]),
                    ]),
                
                // 배경 설정 섹션
                Forms\Components\Section::make('배경 설정')
                    ->schema([
                        Forms\Components\Radio::make('background_type')
                            ->label('배경 타입')
                            ->options([
                                'image' => '이미지',
                                'video' => '영상',
                            ])
                            ->default('image')
                            ->reactive(),
                        Forms\Components\FileUpload::make('background_image')
                            ->label('배경 이미지')
                            ->image()
                            ->directory('heroes')
                            ->imageEditor()
                            ->visible(fn (Get $get) => $get('background_type') === 'image'),
                        Forms\Components\FileUpload::make('background_video')
                            ->label('배경 영상')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->directory('heroes/videos')
                            ->visible(fn (Get $get) => $get('background_type') === 'video'),
                    ]),
                
                // 스타일 설정 섹션
                Forms\Components\Section::make('스타일 설정')
                    ->schema([
                        // 콘텐츠 정렬
                        Forms\Components\Select::make('hero_settings.content_alignment')
                            ->label('콘텐츠 정렬')
                            ->options([
                                'top-left' => '상단 왼쪽',
                                'center-left' => '중앙 왼쪽',
                                'bottom-left' => '하단 왼쪽',
                                'top-center' => '상단 중앙',
                                'center' => '중앙',
                                'bottom-center' => '하단 중앙',
                                'top-right' => '상단 오른쪽',
                                'center-right' => '중앙 오른쪽',
                                'bottom-right' => '하단 오른쪽',
                            ])
                            ->default('center-left')
                            ->reactive(),
                        
                        // 제목 스타일
                        Forms\Components\Fieldset::make('제목 스타일')
                            ->schema([
                                Forms\Components\ColorPicker::make('hero_settings.title.color')
                                    ->label('색상')
                                    ->default('#FFFFFF')
                                    ->reactive(),
                            ]),
                        
                        // 부제목 스타일
                        Forms\Components\Fieldset::make('부제목 스타일')
                            ->schema([
                                Forms\Components\ColorPicker::make('hero_settings.subtitle.color')
                                    ->label('색상')
                                    ->default('#E5E7EB')
                                    ->reactive(),
                            ]),
                        
                        // 설명 스타일
                        Forms\Components\Fieldset::make('설명 스타일')
                            ->schema([
                                Forms\Components\ColorPicker::make('hero_settings.description.color')
                                    ->label('색상')
                                    ->default('#D1D5DB')
                                    ->reactive(),
                            ]),
                        
                        // 버튼 스타일
                        Forms\Components\Fieldset::make('버튼 스타일')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\ColorPicker::make('hero_settings.button.text_color')
                                            ->label('텍스트 색상')
                                            ->default('#FFFFFF')
                                            ->reactive(),
                                        Forms\Components\ColorPicker::make('hero_settings.button.border_color')
                                            ->label('테두리 색상')
                                            ->default('#FFFFFF')
                                            ->reactive(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\ColorPicker::make('hero_settings.button.bg_color')
                                            ->label('배경 색상')
                                            ->default('transparent')
                                            ->reactive(),
                                        Forms\Components\ColorPicker::make('hero_settings.button.hover_bg_color')
                                            ->label('호버 배경 색상')
                                            ->default('#FFFFFF')
                                            ->reactive(),
                                    ]),
                                Forms\Components\ColorPicker::make('hero_settings.button.hover_text_color')
                                    ->label('호버 텍스트 색상')
                                    ->default('#000000')
                                    ->reactive(),
                            ]),
                    ])
                    ->collapsed(),
                
                // 기타 설정
                Forms\Components\Section::make('기타 설정')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('활성화')
                                    ->default(true),
                                Forms\Components\TextInput::make('order')
                                    ->label('순서')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('background_image')
                    ->label('배경')
                    ->square()
                    ->defaultImageUrl(fn ($record) => $record->background_type === 'video' ? asset('images/video-placeholder.png') : null),
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('부제목')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('background_type')
                    ->label('배경 타입')
                    ->colors([
                        'primary' => 'image',
                        'success' => 'video',
                    ]),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성화')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('순서')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화 상태'),
                Tables\Filters\SelectFilter::make('background_type')
                    ->label('배경 타입')
                    ->options([
                        'image' => '이미지',
                        'video' => '영상',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order');
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
            'index' => Pages\ListHeroes::route('/'),
            'create' => Pages\CreateHero::route('/create'),
            'edit' => Pages\EditHero::route('/{record}/edit'),
        ];
    }
}
