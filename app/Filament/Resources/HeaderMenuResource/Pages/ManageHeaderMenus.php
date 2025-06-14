<?php

namespace App\Filament\Resources\HeaderMenuResource\Pages;

use App\Filament\Resources\HeaderMenuResource;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Str;

class ManageHeaderMenus extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = HeaderMenuResource::class;

    protected static string $view = 'filament.resources.header-menu-resource.pages.manage-header-menus';
    
    protected static ?string $title = '헤더 메뉴 편집';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        // project_options에서 헤더 메뉴 데이터 가져오기
        $headerMenu = get_option('header_menu', []);
        
        $this->form->fill([
            'menu_items' => $headerMenu
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('헤더 메뉴 설정')
                    ->description('웹사이트 상단에 표시되는 메뉴를 관리합니다. 드래그하여 순서를 변경할 수 있습니다.')
                    ->schema([
                        Repeater::make('menu_items')
                            ->label('메인 메뉴')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('메뉴 이름')
                                            ->required()
                                            ->placeholder('예: 회사소개')
                                            ->helperText('메뉴에 표시될 이름을 입력하세요')
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, $set) => 
                                                $set('slug', Str::slug($state))
                                            ),
                                        
                                        TextInput::make('url')
                                            ->label('링크 주소')
                                            ->placeholder('예: /about 또는 https://...')
                                            ->helperText('페이지 주소를 입력하세요 (하위 메뉴가 있으면 비워두세요)')
                                            ->url()
                                            ->reactive(),
                                    ]),
                                
                                Select::make('type')
                                    ->label('메뉴 유형')
                                    ->options([
                                        'link' => '일반 링크',
                                        'dropdown' => '드롭다운 메뉴 (하위 메뉴 있음)',
                                        'mega' => '메가 메뉴 (그룹으로 구성)'
                                    ])
                                    ->default('link')
                                    ->reactive()
                                    ->helperText('메뉴의 종류를 선택하세요'),
                                
                                // 일반 드롭다운 메뉴
                                Repeater::make('children')
                                    ->label('하위 메뉴')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('하위 메뉴 이름')
                                            ->required()
                                            ->placeholder('예: 인사말'),
                                        
                                        TextInput::make('url')
                                            ->label('링크 주소')
                                            ->required()
                                            ->placeholder('예: /about/greeting')
                                            ->url(),
                                    ])
                                    ->visible(fn ($get) => $get('type') === 'dropdown')
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->addActionLabel('하위 메뉴 추가')
                                    ->reorderable()
                                    ->collapsible(),
                                
                                // 메가 메뉴 (그룹)
                                Repeater::make('groups')
                                    ->label('메뉴 그룹')
                                    ->schema([
                                        TextInput::make('group_label')
                                            ->label('그룹 이름')
                                            ->required()
                                            ->placeholder('예: 회사 정보')
                                            ->helperText('그룹의 제목을 입력하세요'),
                                        
                                        Repeater::make('items')
                                            ->label('그룹 내 메뉴')
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('메뉴 이름')
                                                    ->required()
                                                    ->placeholder('예: CEO 인사말'),
                                                
                                                TextInput::make('url')
                                                    ->label('링크 주소')
                                                    ->required()
                                                    ->placeholder('예: /about/ceo')
                                                    ->url(),
                                            ])
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                            ->addActionLabel('메뉴 추가')
                                            ->reorderable()
                                            ->collapsible(),
                                    ])
                                    ->visible(fn ($get) => $get('type') === 'mega')
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['group_label'] ?? null)
                                    ->addActionLabel('그룹 추가')
                                    ->reorderable()
                                    ->maxItems(4)
                                    ->collapsible(),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->addActionLabel('메인 메뉴 추가')
                            ->reorderable()
                            ->maxItems(8)
                            ->defaultItems(0)
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
        update_option('header_menu', $data['menu_items'] ?? []);
        
        // 성공 메시지
        Notification::make()
            ->title('메뉴가 성공적으로 업데이트되었습니다!')
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
                ->label('메뉴 저장')
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
        return '헤더 메뉴 편집';
    }
    
    public function getHeading(): string
    {
        return '헤더 메뉴 관리';
    }
    
    public function getSubheading(): ?string
    {
        return '웹사이트 상단 메뉴를 편집합니다. 변경사항은 저장 버튼을 클릭해야 적용됩니다.';
    }
}
