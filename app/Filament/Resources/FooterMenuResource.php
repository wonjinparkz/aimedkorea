<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FooterMenuResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class FooterMenuResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    
    protected static ?string $navigationLabel = '푸터 메뉴 관리';
    
    protected static ?string $navigationGroup = '사이트 관리';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $slug = 'footer-menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('푸터 기본 설정')
                    ->description('웹사이트 하단 푸터의 기본 정보를 설정합니다.')
                    ->schema([
                        Textarea::make('footer_description')
                            ->label('푸터 설명')
                            ->placeholder('회사 소개 또는 푸터에 표시할 설명을 입력하세요')
                            ->helperText('푸터 상단에 표시되는 설명 문구입니다')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('feature_image')
                                    ->label('특징 이미지')
                                    ->image()
                                    ->directory('footer')
                                    ->helperText('푸터에 표시될 대표 이미지를 업로드하세요'),
                                
                                TextInput::make('feature_image_url')
                                    ->label('이미지 클릭 시 이동 URL')
                                    ->placeholder('https://example.com 또는 /page')
                                    ->helperText('이미지 클릭 시 이동할 페이지 주소'),
                            ]),
                    ])
                    ->collapsible(false),
                
                Section::make('푸터 카드 설정')
                    ->description('푸터에 표시될 6개의 카드를 설정합니다. 각 카드에는 아이콘, 제목, 설명, 링크를 설정할 수 있습니다.')
                    ->schema([
                        Repeater::make('footer_cards')
                            ->label('푸터 카드')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('icon')
                                            ->label('아이콘 선택')
                                            ->options([
                                                'heroicon-o-heart' => '❤️ 하트',
                                                'heroicon-o-chart-bar' => '📊 차트',
                                                'heroicon-o-user-group' => '👥 사용자 그룹',
                                                'heroicon-o-document-text' => '📄 문서',
                                                'heroicon-o-bell' => '🔔 알림',
                                                'heroicon-o-shield-check' => '🛡️ 보안',
                                                'heroicon-o-home' => '🏠 홈',
                                                'heroicon-o-phone' => '📱 전화',
                                                'heroicon-o-envelope' => '✉️ 이메일',
                                                'heroicon-o-calendar' => '📅 캘린더',
                                                'heroicon-o-clock' => '⏰ 시계',
                                                'heroicon-o-cog' => '⚙️ 설정',
                                                'heroicon-o-academic-cap' => '🎓 교육',
                                                'heroicon-o-beaker' => '🧪 연구',
                                                'heroicon-o-building-office' => '🏢 건물',
                                                'heroicon-o-chat-bubble-left-right' => '💬 채팅',
                                                'heroicon-o-computer-desktop' => '🖥️ 컴퓨터',
                                                'heroicon-o-globe-alt' => '🌍 지구본',
                                                'heroicon-o-light-bulb' => '💡 아이디어',
                                                'heroicon-o-map-pin' => '📍 위치',
                                                'heroicon-o-newspaper' => '📰 뉴스',
                                                'heroicon-o-presentation-chart-line' => '📈 프레젠테이션',
                                                'heroicon-o-question-mark-circle' => '❓ 도움말',
                                                'heroicon-o-star' => '⭐ 별',
                                                'heroicon-o-trophy' => '🏆 트로피',
                                            ])
                                            ->searchable()
                                            ->required()
                                            ->helperText('아이콘을 선택하세요'),
                                        
                                        TextInput::make('title')
                                            ->label('제목')
                                            ->placeholder('서비스명')
                                            ->required()
                                            ->helperText('카드의 제목을 입력하세요'),
                                    ]),
                                
                                Textarea::make('description')
                                    ->label('설명')
                                    ->placeholder('서비스에 대한 간단한 설명을 입력하세요')
                                    ->required()
                                    ->helperText('카드에 표시될 설명을 입력하세요')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                
                                TextInput::make('url')
                                    ->label('클릭 시 이동 URL')
                                    ->placeholder('https://example.com 또는 /page')
                                    ->required()
                                    ->helperText('카드 클릭 시 이동할 페이지 주소')
                                    ->columnSpanFull(),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('카드 추가')
                            ->reorderable()
                            ->minItems(1)
                            ->maxItems(6)
                            ->defaultItems(6)
                            ->collapsible(),
                    ])
                    ->collapsible(false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFooterMenus::route('/'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return null;
    }
    
    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
