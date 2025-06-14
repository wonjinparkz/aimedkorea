<?php

namespace App\Filament\Resources\HeaderMenuResource\Pages;

use App\Filament\Resources\HeaderMenuResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;

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
    
    protected function getFormSchema(): array
    {
        return HeaderMenuResource::form($this->form)->getSchema();
    }
    
    protected function getFormStatePath(): ?string
    {
        return 'data';
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
