<?php

namespace App\Filament\Resources\FooterMenuResource\Pages;

use App\Filament\Resources\FooterMenuResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;

class ManageFooterMenus extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = FooterMenuResource::class;

    protected static string $view = 'filament.resources.footer-menu-resource.pages.manage-footer-menus';
    
    protected static ?string $title = '푸터 메뉴 편집';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        // project_options에서 푸터 메뉴 데이터 가져오기
        $footerData = get_option('footer_settings', [
            'footer_description' => '',
            'feature_image' => '',
            'feature_image_url' => '',
            'footer_cards' => []
        ]);
        
        // 기본 카드 데이터가 없으면 생성
        if (empty($footerData['footer_cards'])) {
            $footerData['footer_cards'] = $this->getDefaultCards();
        }
        
        $this->form->fill($footerData);
    }
    
    private function getDefaultCards(): array
    {
        return [
            [
                'icon' => 'heroicon-o-beaker',
                'title' => '과학이 만든 회복 솔루션',
                'description' => '임상과 논문으로 검증된 AI 기반 회복 기술',
                'url' => '/recovery-solutions'
            ],
            [
                'icon' => 'heroicon-o-star',
                'title' => '추천하는 제품/서비스',
                'description' => '눈•뇌•수면 회복에 도움되는 루카의 추천 템',
                'url' => '/recommendations'
            ],
            [
                'icon' => 'heroicon-o-newspaper',
                'title' => '디지털 노화 뉴스룸/지식 브리프',
                'description' => '최신 과학 뉴스와 뇌•눈•수면 콘텐츠 정리',
                'url' => '/newsroom'
            ],
            [
                'icon' => 'heroicon-o-calendar',
                'title' => 'NR3 루틴 무료 서비스',
                'description' => '디지털 자가진단-> 맞춤 루틴 코칭 시작하기',
                'url' => '/nr3-routine'
            ],
            [
                'icon' => 'heroicon-o-heart',
                'title' => '루틴실천 회복 스토리',
                'description' => '회복 전후 변화 사례와 사용자 경험 공유',
                'url' => '/recovery-stories'
            ],
            [
                'icon' => 'heroicon-o-user-group',
                'title' => '우리가 함께하는 사람들',
                'description' => '전문가, 기관, 글로벌 파트너들의 소식',
                'url' => '/partners'
            ]
        ];
    }
    
    public function form(Form $form): Form
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
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        // project_options에 저장
        update_option('footer_settings', $data);
        
        // 성공 메시지
        Notification::make()
            ->title('푸터 설정이 성공적으로 업데이트되었습니다!')
            ->success()
            ->duration(5000)
            ->send();
            
        // 페이지 새로고침하여 최신 데이터 표시
        $this->redirect(static::getUrl());
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('설정 저장')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check')
                ->size('lg')
                ->extraAttributes([
                    'class' => 'text-lg'
                ]),
        ];
    }
    
    public function getTitle(): string
    {
        return '푸터 메뉴 편집';
    }
    
    public function getHeading(): string
    {
        return '푸터 메뉴 관리';
    }
    
    public function getSubheading(): ?string
    {
        return '웹사이트 하단 푸터를 편집합니다. 변경사항은 저장 버튼을 클릭해야 적용됩니다.';
    }
}
