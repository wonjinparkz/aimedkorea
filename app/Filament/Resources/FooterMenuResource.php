<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
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
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class FooterMenuResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    
    protected static ?string $navigationLabel = '푸터 메뉴 관리';
    
    protected static ?string $navigationGroup = '사이트';
    
    protected static ?int $navigationSort = 101;
    
    protected static ?string $slug = 'footer-menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('푸터 기본 설정')
                    ->description('웹사이트 하단 푸터의 기본 정보를 설정합니다.')
                    ->schema([
                        Tabs::make('Footer Description Translations')
                            ->tabs([
                                Tab::make('한국어')
                                    ->schema([
                                        Textarea::make('footer_description')
                                            ->label('푸터 설명')
                                            ->placeholder('회사 소개 또는 푸터에 표시할 설명을 입력하세요')
                                            ->helperText('푸터 상단에 표시되는 설명 문구입니다')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('English')
                                    ->schema([
                                        Textarea::make('footer_description_eng')
                                            ->label('Footer Description')
                                            ->placeholder('Enter company introduction or footer description')
                                            ->helperText('Description text displayed at the top of footer')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('中文')
                                    ->schema([
                                        Textarea::make('footer_description_chn')
                                            ->label('页脚说明')
                                            ->placeholder('输入公司介绍或页脚说明')
                                            ->helperText('页脚顶部显示的说明文字')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('हिंदी')
                                    ->schema([
                                        Textarea::make('footer_description_hin')
                                            ->label('फुटर विवरण')
                                            ->placeholder('कंपनी परिचय या फुटर विवरण दर्ज करें')
                                            ->helperText('फुटर के शीर्ष पर प्रदर्शित विवरण पाठ')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                                Tab::make('العربية')
                                    ->schema([
                                        Textarea::make('footer_description_arb')
                                            ->label('وصف التذييل')
                                            ->placeholder('أدخل مقدمة الشركة أو وصف التذييل')
                                            ->helperText('نص الوصف المعروض في أعلى التذييل')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ])
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
                                    ->helperText('아이콘을 선택하세요')
                                    ->columnSpanFull(),
                                
                                TextInput::make('url')
                                    ->label('클릭 시 이동 URL')
                                    ->placeholder('https://example.com 또는 /page')
                                    ->required()
                                    ->helperText('카드 클릭 시 이동할 페이지 주소')
                                    ->columnSpanFull(),
                                
                                Tabs::make('Card Translations')
                                    ->tabs([
                                        Tab::make('한국어')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('제목')
                                                    ->placeholder('서비스명')
                                                    ->required()
                                                    ->helperText('카드의 제목을 입력하세요'),
                                                
                                                Textarea::make('description')
                                                    ->label('설명')
                                                    ->placeholder('서비스에 대한 간단한 설명을 입력하세요')
                                                    ->required()
                                                    ->helperText('카드에 표시될 설명을 입력하세요')
                                                    ->rows(2),
                                            ]),
                                        Tab::make('English')
                                            ->schema([
                                                TextInput::make('title_eng')
                                                    ->label('Title')
                                                    ->placeholder('Service name')
                                                    ->helperText('Enter card title'),
                                                
                                                Textarea::make('description_eng')
                                                    ->label('Description')
                                                    ->placeholder('Enter a brief description of the service')
                                                    ->helperText('Enter description to be displayed on the card')
                                                    ->rows(2),
                                            ]),
                                        Tab::make('中文')
                                            ->schema([
                                                TextInput::make('title_chn')
                                                    ->label('标题')
                                                    ->placeholder('服务名称')
                                                    ->helperText('输入卡片标题'),
                                                
                                                Textarea::make('description_chn')
                                                    ->label('说明')
                                                    ->placeholder('输入服务的简要说明')
                                                    ->helperText('输入卡片上显示的说明')
                                                    ->rows(2),
                                            ]),
                                        Tab::make('हिंदी')
                                            ->schema([
                                                TextInput::make('title_hin')
                                                    ->label('शीर्षक')
                                                    ->placeholder('सेवा का नाम')
                                                    ->helperText('कार्ड शीर्षक दर्ज करें'),
                                                
                                                Textarea::make('description_hin')
                                                    ->label('विवरण')
                                                    ->placeholder('सेवा का संक्षिप्त विवरण दर्ज करें')
                                                    ->helperText('कार्ड पर प्रदर्शित किया जाने वाला विवरण दर्ज करें')
                                                    ->rows(2),
                                            ]),
                                        Tab::make('العربية')
                                            ->schema([
                                                TextInput::make('title_arb')
                                                    ->label('العنوان')
                                                    ->placeholder('اسم الخدمة')
                                                    ->helperText('أدخل عنوان البطاقة'),
                                                
                                                Textarea::make('description_arb')
                                                    ->label('الوصف')
                                                    ->placeholder('أدخل وصفًا موجزًا للخدمة')
                                                    ->helperText('أدخل الوصف الذي سيتم عرضه على البطاقة')
                                                    ->rows(2),
                                            ]),
                                    ])
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
        return (PermissionHelper::hasPermission('section_site-view') && PermissionHelper::hasPermission('footer_menus-view')) || PermissionHelper::isAdmin();
    }
}
