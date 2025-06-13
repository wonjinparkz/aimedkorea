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
                Forms\Components\Section::make('미리보기')
                    ->description('아래에서 수정한 내용이 실시간으로 표시됩니다')
                    ->schema([
                        Forms\Components\Placeholder::make('preview')
                            ->label('')
                            ->content(new HtmlString(self::getPreviewHtml())),
                    ])
                    ->collapsible(),
                
                // 제목 섹션
                Forms\Components\Section::make('제목 설정')
                    ->description('슬라이드의 메인 제목을 입력하고 스타일을 설정하세요')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('제목 텍스트')
                                    ->required()
                                    ->maxLength(255)
                                    ->reactive()
                                    ->columnSpan(2),
                                Forms\Components\ColorPicker::make('hero_settings.title.color')
                                    ->label('글자 색상')
                                    ->default('#FFFFFF')
                                    ->reactive(),
                                Forms\Components\Select::make('hero_settings.title.size')
                                    ->label('글자 크기')
                                    ->options([
                                        'text-3xl' => '작게',
                                        'text-4xl' => '보통',
                                        'text-5xl' => '크게',
                                        'text-6xl' => '매우 크게',
                                    ])
                                    ->default('text-5xl')
                                    ->reactive(),
                            ]),
                    ]),
                
                // 부제목 섹션
                Forms\Components\Section::make('부제목 설정')
                    ->description('선택사항: 제목 위나 아래에 표시될 작은 텍스트')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('부제목 텍스트')
                                    ->maxLength(255)
                                    ->reactive()
                                    ->columnSpan(2),
                                Forms\Components\ColorPicker::make('hero_settings.subtitle.color')
                                    ->label('글자 색상')
                                    ->default('#E5E7EB')
                                    ->reactive(),
                                Forms\Components\Select::make('hero_settings.subtitle.size')
                                    ->label('글자 크기')
                                    ->options([
                                        'text-xs' => '매우 작게',
                                        'text-sm' => '작게',
                                        'text-base' => '보통',
                                        'text-lg' => '크게',
                                    ])
                                    ->default('text-sm')
                                    ->reactive(),
                            ]),
                    ])
                    ->collapsed(),
                
                // 설명 섹션
                Forms\Components\Section::make('설명 설정')
                    ->description('선택사항: 제목 아래에 표시될 상세 설명')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('설명 텍스트')
                            ->rows(3)
                            ->reactive(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\ColorPicker::make('hero_settings.description.color')
                                    ->label('글자 색상')
                                    ->default('#D1D5DB')
                                    ->reactive(),
                                Forms\Components\Select::make('hero_settings.description.size')
                                    ->label('글자 크기')
                                    ->options([
                                        'text-sm' => '작게',
                                        'text-base' => '보통',
                                        'text-lg' => '크게',
                                        'text-xl' => '매우 크게',
                                    ])
                                    ->default('text-lg')
                                    ->reactive(),
                            ]),
                    ])
                    ->collapsed(),
                
                // 버튼 섹션
                Forms\Components\Section::make('버튼 설정')
                    ->description('선택사항: 클릭 가능한 버튼 추가')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('button_text')
                                    ->label('버튼 텍스트')
                                    ->placeholder('예: 자세히 보기')
                                    ->maxLength(255)
                                    ->reactive(),
                                Forms\Components\TextInput::make('button_url')
                                    ->label('버튼 링크 (URL)')
                                    ->placeholder('예: /about')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\ColorPicker::make('hero_settings.button.text_color')
                                    ->label('글자 색상')
                                    ->default('#FFFFFF')
                                    ->reactive(),
                                Forms\Components\ColorPicker::make('hero_settings.button.bg_color')
                                    ->label('배경 색상')
                                    ->default('#3B82F6')
                                    ->reactive(),
                                Forms\Components\Select::make('hero_settings.button.style')
                                    ->label('버튼 스타일')
                                    ->options([
                                        'filled' => '색 채우기',
                                        'outline' => '테두리만',
                                    ])
                                    ->default('filled')
                                    ->reactive(),
                            ]),
                    ])
                    ->collapsed(),
                
                // 전체 레이아웃
                Forms\Components\Section::make('전체 레이아웃')
                    ->description('텍스트가 표시될 위치를 선택하세요')
                    ->schema([
                        Forms\Components\Radio::make('hero_settings.content_alignment')
                            ->label('콘텐츠 위치')
                            ->options([
                                'left' => '왼쪽',
                                'center' => '가운데',
                                'right' => '오른쪽 (텍스트는 왼쪽 정렬)',
                            ])
                            ->default('left')
                            ->reactive()
                            ->inline(),
                    ]),
                
                // 배경 설정
                Forms\Components\Section::make('배경 설정')
                    ->description('슬라이드의 배경 이미지, 영상 및 오버레이 설정')
                    ->schema([
                        Forms\Components\Radio::make('background_type')
                            ->label('배경 타입')
                            ->options([
                                'image' => '이미지',
                                'video' => '영상',
                            ])
                            ->default('image')
                            ->reactive()
                            ->inline(),
                        Forms\Components\FileUpload::make('background_image')
                            ->label('배경 이미지 업로드')
                            ->helperText('권장 크기: 1920x500 픽셀')
                            ->image()
                            ->directory('heroes')
                            ->imageEditor()
                            ->visible(fn (Get $get) => $get('background_type') === 'image'),
                        Forms\Components\FileUpload::make('background_video')
                            ->label('배경 영상 업로드')
                            ->helperText('MP4 형식 권장, 최대 50MB')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->directory('heroes/videos')
                            ->maxSize(51200) // 50MB
                            ->visible(fn (Get $get) => $get('background_type') === 'video'),
                        
                        // 오버레이 설정
                        Forms\Components\Fieldset::make('오버레이 설정')
                            ->schema([
                                Forms\Components\Toggle::make('hero_settings.overlay.enabled')
                                    ->label('오버레이 사용')
                                    ->helperText('배경을 어둡게 하여 텍스트를 더 잘 보이게 합니다')
                                    ->default(true)
                                    ->reactive(),
                                Forms\Components\ColorPicker::make('hero_settings.overlay.color')
                                    ->label('오버레이 색상')
                                    ->default('#000000')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('hero_settings.overlay.enabled')),
                                Forms\Components\TextInput::make('hero_settings.overlay.opacity')
                                    ->label('오버레이 투명도')
                                    ->helperText('0은 투명, 100은 완전 불투명')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->default(60)
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('hero_settings.overlay.enabled')),
                            ]),
                    ]),
                
                // 기타 설정
                Forms\Components\Section::make('기타 설정')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('슬라이드 활성화')
                                    ->helperText('비활성화하면 웹사이트에 표시되지 않습니다')
                                    ->default(true),
                                Forms\Components\TextInput::make('order')
                                    ->label('표시 순서')
                                    ->helperText('숫자가 작을수록 먼저 표시됩니다')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    protected static function getPreviewHtml(): string
    {
        $cssPath = resource_path('views/filament/resources/hero-resource/hero-preview-styles.css');
        $jsPath = resource_path('views/filament/resources/hero-resource/hero-preview-script.js');
        
        $css = file_exists($cssPath) ? file_get_contents($cssPath) : '';
        $js = file_exists($jsPath) ? file_get_contents($jsPath) : '';
        
        return <<<HTML
<div class="hero-preview-wrapper" x-data="heroPreview" x-init="initPreview">
    <style>{$css}</style>
    <div id="hero-preview-container" class="hero-preview-scope" style="width: 100%; height: 320px; border-radius: 8px; overflow: hidden; position: relative; background: #000;">
        <!-- 프리뷰가 JavaScript로 렌더링됩니다 -->
    </div>
    <div style="margin-top: 8px; padding: 8px; background-color: #f3f4f6; border-radius: 4px; font-size: 12px; color: #6b7280;">
        💡 팁: 각 섹션의 설정을 변경하면 위 미리보기에 실시간으로 반영됩니다
    </div>
    <script>{$js}</script>
</div>
HTML;
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
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('부제목')
                    ->searchable()
                    ->limit(30),
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
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성화 상태'),
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
