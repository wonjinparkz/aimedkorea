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
                'icon' => 'heroicon-o-heart',
                'title' => '건강관리',
                'description' => '개인 맞춤형 건강관리 서비스를 제공합니다.',
                'url' => '/health-care'
            ],
            [
                'icon' => 'heroicon-o-chart-bar',
                'title' => '데이터 분석',
                'description' => 'AI 기반 건강 데이터 분석 서비스입니다.',
                'url' => '/data-analysis'
            ],
            [
                'icon' => 'heroicon-o-user-group',
                'title' => '전문가 상담',
                'description' => '의료 전문가와의 온라인 상담 서비스입니다.',
                'url' => '/consultation'
            ],
            [
                'icon' => 'heroicon-o-document-text',
                'title' => '건강 기록',
                'description' => '체계적인 건강 기록 관리 시스템입니다.',
                'url' => '/health-records'
            ],
            [
                'icon' => 'heroicon-o-bell',
                'title' => '알림 서비스',
                'description' => '건강 관리 알림 및 리마인더 서비스입니다.',
                'url' => '/notifications'
            ],
            [
                'icon' => 'heroicon-o-shield-check',
                'title' => '보안',
                'description' => '안전한 개인정보 보호 시스템입니다.',
                'url' => '/security'
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
                                    ->url()
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
                                        TextInput::make('icon')
                                            ->label('아이콘')
                                            ->placeholder('heroicon-o-home')
                                            ->helperText('Heroicon 이름을 입력하세요 (예: heroicon-o-home)')
                                            ->required(),
                                        
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
                                    ->url()
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
