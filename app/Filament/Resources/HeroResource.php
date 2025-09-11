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
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class HeroResource extends Resource
{
    protected static ?string $model = Hero::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Hero 슬라이드';
    
    protected static ?string $modelLabel = 'Hero 슬라이드';
    
    protected static ?string $pluralModelLabel = 'Hero 슬라이드';
    
    protected static ?string $navigationGroup = '홈 구성';
    
    protected static ?int $navigationSort = 21;

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
                            ->content(function ($record) {
                                $existingImageUrl = $record && $record->background_image ? Storage::url($record->background_image) : '';
                                $existingVideoUrl = $record && $record->background_video ? Storage::url($record->background_video) : '';
                                
                                return new HtmlString('
                                <div id="hero-preview-wrapper" 
                                     data-existing-image-url="' . $existingImageUrl . '"
                                     data-existing-video-url="' . $existingVideoUrl . '"
                                     style="overflow: hidden;">
                                    <iframe 
                                        id="hero-preview-iframe"
                                        src="' . route('filament.hero-preview') . '"
                                        style="width: 100%; height: 500px; border: none; border-radius: 8px; display: block;"
                                        scrolling="no"
                                    ></iframe>
                                </div>
                                <script>
                                    let previewIframe = null;
                                    let previousData = {};
                                    let updateInterval = null;
                                    
                                    document.addEventListener("DOMContentLoaded", function() {
                                        previewIframe = document.getElementById("hero-preview-iframe");
                                        
                                        // iframe 로드 완료 시 초기 데이터 전송
                                        previewIframe.onload = function() {
                                            console.log("Preview iframe loaded");
                                            setTimeout(function() {
                                                updatePreview();
                                                // 초기 로드 후 다시 한번 업데이트 (안전을 위해)
                                                setTimeout(updatePreview, 1000);
                                            }, 500);
                                        };
                                        
                                        // 주기적으로 업데이트 체크 (500ms마다)
                                        updateInterval = setInterval(updatePreview, 500);
                                    });
                                    
                                    // 페이지 떠날 때 interval 정리
                                    window.addEventListener(\'beforeunload\', function() {
                                        if (updateInterval) {
                                            clearInterval(updateInterval);
                                        }
                                    });
                                    
                                    function updatePreview() {
                                        if (!previewIframe || !previewIframe.contentWindow) return;
                                        
                                        // 폼 데이터 수집 - Filament의 실제 ID 속성 사용
                                        const getFieldValue = (fieldName) => {
                                            // Filament은 ID를 사용하므로 ID로 찾기
                                            const fieldId = `data.${fieldName}`;
                                            const field = document.getElementById(fieldId);
                                            
                                            if (field) {
                                                // ColorPicker의 경우 다른 위치에 값이 있을 수 있음
                                                if (field.type === \'text\' && field.value === \'\') {
                                                    // 형제 요소 중 color input 찾기
                                                    const colorInput = field.parentElement.querySelector(\'input[type="color"]\');
                                                    if (colorInput) {
                                                        return colorInput.value;
                                                    }
                                                }
                                                return field.value;
                                            }
                                            
                                            // 못 찾으면 fieldName 그대로 시도
                                            const directField = document.getElementById(fieldName);
                                            if (directField) {
                                                return directField.value;
                                            }
                                            
                                            return \'\';
                                        };
                                        
                                        const getRadioValue = (fieldName) => {
                                            // Radio 버튼은 name 속성을 사용
                                            const fieldId = `data.${fieldName}`;
                                            const checkedRadio = document.querySelector(`input[name="${fieldId}"]:checked`);
                                            if (checkedRadio) return checkedRadio.value;
                                            
                                            // name으로 못 찾으면 fieldName 그대로 시도
                                            const directRadio = document.querySelector(`input[name="${fieldName}"]:checked`);
                                            if (directRadio) return directRadio.value;
                                            
                                            return \'\';
                                        };
                                        
                                        const getCheckboxValue = (fieldName) => {
                                            const fieldId = `data.${fieldName}`;
                                            const checkbox = document.getElementById(fieldId);
                                            if (checkbox && checkbox.type === \'checkbox\') {
                                                return checkbox.checked;
                                            }
                                            return false;
                                        };
                                        
                                        // 각 필드 값 가져오기
                                        const title = getFieldValue(\'title\');
                                        const subtitle = getFieldValue(\'subtitle\');
                                        const description = getFieldValue(\'description\');
                                        const buttonText = getFieldValue(\'button_text\');
                                        
                                        const titleColor = getFieldValue(\'hero_settings.title.color\');
                                        const titleSize = getFieldValue(\'hero_settings.title.size\');
                                        const subtitleColor = getFieldValue(\'hero_settings.subtitle.color\');
                                        const subtitleSize = getFieldValue(\'hero_settings.subtitle.size\');
                                        const subtitlePosition = getRadioValue(\'hero_settings.subtitle.position\');
                                        const descriptionColor = getFieldValue(\'hero_settings.description.color\');
                                        const descriptionSize = getFieldValue(\'hero_settings.description.size\');
                                        const buttonTextColor = getFieldValue(\'hero_settings.button.text_color\');
                                        const buttonBgColor = getFieldValue(\'hero_settings.button.bg_color\');
                                        const buttonStyle = getFieldValue(\'hero_settings.button.style\');
                                        const contentAlignment = getRadioValue(\'hero_settings.content_alignment\');
                                        const overlayEnabled = getCheckboxValue(\'hero_settings.overlay.enabled\');
                                        const overlayColor = getFieldValue(\'hero_settings.overlay.color\');
                                        const overlayOpacity = getFieldValue(\'hero_settings.overlay.opacity\');
                                        const backgroundType = getRadioValue(\'background_type\');
                                        
                                        const data = {
                                            title: title,
                                            subtitle: subtitle,
                                            description: description,
                                            buttonText: buttonText,
                                            titleColor: titleColor || "#FFFFFF",
                                            titleSize: titleSize || "text-5xl",
                                            subtitleColor: subtitleColor || "#E5E7EB",
                                            subtitleSize: subtitleSize || "text-sm",
                                            subtitlePosition: subtitlePosition || "above",
                                            descriptionColor: descriptionColor || "#D1D5DB",
                                            descriptionSize: descriptionSize || "text-lg",
                                            buttonTextColor: buttonTextColor || "#FFFFFF",
                                            buttonBgColor: buttonBgColor || "#3B82F6",
                                            buttonStyle: buttonStyle || "filled",
                                            contentAlignment: contentAlignment || "left",
                                            overlayEnabled: overlayEnabled,
                                            overlayColor: overlayColor || "#000000",
                                            overlayOpacity: overlayOpacity ? parseInt(overlayOpacity) : 60,
                                            backgroundType: backgroundType || "image"
                                        };
                                        
                                        // 데이터 변경 확인 (파일 제외)
                                        const dataWithoutFiles = {...data};
                                        const previousWithoutFiles = {...previousData};
                                        delete previousWithoutFiles.backgroundImageUrl;
                                        delete previousWithoutFiles.backgroundVideoUrl;
                                        
                                        if (JSON.stringify(dataWithoutFiles) === JSON.stringify(previousWithoutFiles)) {
                                            // 변경사항이 없으면 리턴
                                            return;
                                        }
                                        
                                        console.log("Preview data changed:", data);
                                        previousData = {...data};
                                        
                                        // 배경 이미지/비디오 입력 처리
                                        const backgroundImageInput = document.querySelector(\'input[type="file"][accept*="image"]\');
                                        const backgroundVideoInput = document.querySelector(\'input[type="file"][accept*="video"]\');
                                        
                                        // 배경 이미지 처리
                                        if (backgroundImageInput && backgroundImageInput.files && backgroundImageInput.files[0]) {
                                            const file = backgroundImageInput.files[0];
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                data.backgroundImageUrl = e.target.result;
                                                previewIframe.contentWindow.postMessage({
                                                    type: "hero-preview-update",
                                                    data: data
                                                }, "*");
                                            };
                                            reader.readAsDataURL(file);
                                        } else if (backgroundVideoInput && backgroundVideoInput.files && backgroundVideoInput.files[0]) {
                                            const file = backgroundVideoInput.files[0];
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                data.backgroundVideoUrl = e.target.result;
                                                previewIframe.contentWindow.postMessage({
                                                    type: "hero-preview-update",
                                                    data: data
                                                }, "*");
                                            };
                                            reader.readAsDataURL(file);
                                        } else {
                                            // 기존 이미지 URL 확인 (수정 모드)
                                            const previewWrapper = document.getElementById(\'hero-preview-wrapper\');
                                            const existingImageUrl = previewWrapper ? previewWrapper.getAttribute(\'data-existing-image-url\') : \'\';  
                                            const existingVideoUrl = previewWrapper ? previewWrapper.getAttribute(\'data-existing-video-url\') : \'\';
                                            
                                            if (existingImageUrl && backgroundType === \'image\') {
                                                data.backgroundImageUrl = existingImageUrl;
                                            } else if (existingVideoUrl && backgroundType === \'video\') {
                                                data.backgroundVideoUrl = existingVideoUrl;
                                            }
                                            
                                            // iframe으로 데이터 전송
                                            previewIframe.contentWindow.postMessage({
                                                type: "hero-preview-update",
                                                data: data
                                            }, "*");
                                        }
                                    }
                                </script>
                            ');
                            }),
                    ])
                    ->collapsible(),
                
                // 제목 섹션
                Forms\Components\Section::make('제목 설정')
                    ->description('슬라이드의 메인 제목을 다국어로 입력하고 스타일을 설정하세요')
                    ->schema([
                        // 한국어 제목
                        Forms\Components\TextInput::make('title_translations.kor')
                            ->label('제목 (한국어)')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 영어 제목
                        Forms\Components\TextInput::make('title_translations.eng')
                            ->label('Title (English)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 중국어 제목
                        Forms\Components\TextInput::make('title_translations.chn')
                            ->label('标题 (中文)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 힌디어 제목
                        Forms\Components\TextInput::make('title_translations.hin')
                            ->label('शीर्षक (हिन्दी)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 아랍어 제목
                        Forms\Components\TextInput::make('title_translations.arb')
                            ->label('العنوان (العربية)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
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
                    ->description('선택사항: 제목 위나 아래에 표시될 작은 텍스트를 다국어로 입력하세요')
                    ->schema([
                        // 한국어 부제목
                        Forms\Components\TextInput::make('subtitle_translations.kor')
                            ->label('부제목 (한국어)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 영어 부제목
                        Forms\Components\TextInput::make('subtitle_translations.eng')
                            ->label('Subtitle (English)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 중국어 부제목
                        Forms\Components\TextInput::make('subtitle_translations.chn')
                            ->label('副标题 (中文)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 힌디어 부제목
                        Forms\Components\TextInput::make('subtitle_translations.hin')
                            ->label('उपशीर्षक (हिन्दी)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 아랍어 부제목
                        Forms\Components\TextInput::make('subtitle_translations.arb')
                            ->label('العنوان الفرعي (العربية)')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
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
                        Forms\Components\Radio::make('hero_settings.subtitle.position')
                            ->label('부제목 위치')
                            ->options([
                                'above' => '제목 위',
                                'below' => '제목 아래',
                            ])
                            ->default('above')
                            ->reactive()
                            ->inline(),
                    ])
                    ->collapsed(),
                
                // 설명 섹션
                Forms\Components\Section::make('설명 설정')
                    ->description('선택사항: 제목 아래에 표시될 상세 설명을 다국어로 입력하세요')
                    ->schema([
                        // 한국어 설명
                        Forms\Components\Textarea::make('description_translations.kor')
                            ->label('설명 (한국어)')
                            ->rows(3)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 영어 설명
                        Forms\Components\Textarea::make('description_translations.eng')
                            ->label('Description (English)')
                            ->rows(3)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 중국어 설명
                        Forms\Components\Textarea::make('description_translations.chn')
                            ->label('描述 (中文)')
                            ->rows(3)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 힌디어 설명
                        Forms\Components\Textarea::make('description_translations.hin')
                            ->label('विवरण (हिन्दी)')
                            ->rows(3)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 아랍어 설명
                        Forms\Components\Textarea::make('description_translations.arb')
                            ->label('الوصف (العربية)')
                            ->rows(3)
                            ->reactive()
                            ->columnSpanFull(),
                            
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
                    ->description('선택사항: 클릭 가능한 버튼 텍스트를 다국어로 입력하세요')
                    ->schema([
                        // 한국어 버튼 텍스트
                        Forms\Components\TextInput::make('button_text_translations.kor')
                            ->label('버튼 텍스트 (한국어)')
                            ->placeholder('예: 자세히 보기')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 영어 버튼 텍스트
                        Forms\Components\TextInput::make('button_text_translations.eng')
                            ->label('Button Text (English)')
                            ->placeholder('e.g. Learn More')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 중국어 버튼 텍스트
                        Forms\Components\TextInput::make('button_text_translations.chn')
                            ->label('按钮文本 (中文)')
                            ->placeholder('例如：了解更多')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 힌디어 버튼 텍스트
                        Forms\Components\TextInput::make('button_text_translations.hin')
                            ->label('बटन टेक्स्ट (हिन्दी)')
                            ->placeholder('जैसे: और पढ़ें')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        // 아랍어 버튼 텍스트
                        Forms\Components\TextInput::make('button_text_translations.arb')
                            ->label('نص الزر (العربية)')
                            ->placeholder('مثال: اقرأ المزيد')
                            ->maxLength(255)
                            ->reactive()
                            ->columnSpanFull(),
                            
                        Forms\Components\Select::make('button_post_id')
                            ->label('버튼 링크 (배너 포스트 선택)')
                            ->relationship('buttonPost', 'title')
                            ->options(function () {
                                return Post::where('type', Post::TYPE_BANNER)
                                    ->where('is_published', true)
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->placeholder('배너 포스트를 선택하세요')
                            ->columnSpanFull(),
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
                    
                // 기본 필드들 (숨김 처리)
                Forms\Components\Hidden::make('title')
                    ->default('Hero Title'),
                Forms\Components\Hidden::make('subtitle'),
                Forms\Components\Hidden::make('description'),
                Forms\Components\Hidden::make('button_text'),
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
                    ->getStateUsing(fn ($record) => $record->getTitle('kor'))
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('부제목')
                    ->getStateUsing(fn ($record) => $record->getSubtitle('kor'))
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
