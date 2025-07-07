<?php

namespace App\Filament\Resources\HeaderMenuResource\Pages;

use App\Filament\Resources\HeaderMenuResource;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Livewire\Attributes\On;

class CustomManageHeaderMenus extends Page
{
    protected static string $resource = HeaderMenuResource::class;

    protected static string $view = 'filament.resources.header-menu-resource.pages.custom-manage-header-menus';
    
    protected static ?string $title = '헤더 메뉴 관리';
    
    public array $menuItems = [];
    
    public function mount(): void
    {
        // project_options에서 헤더 메뉴 데이터 가져오기
        $this->menuItems = get_option('header_menu', []);
        
        // ID가 없는 항목에 ID 추가
        $this->menuItems = $this->ensureMenuIds($this->menuItems);
    }
    
    protected function ensureMenuIds($items)
    {
        foreach ($items as &$item) {
            if (!isset($item['id'])) {
                $item['id'] = uniqid('menu_');
            }
            // type이 없는 경우 기본값 설정
            if (!isset($item['type'])) {
                if (isset($item['groups']) && is_array($item['groups'])) {
                    $item['type'] = 'mega';
                } elseif (isset($item['children']) && !empty($item['children'])) {
                    $item['type'] = 'dropdown';
                } else {
                    $item['type'] = 'single';
                }
            }
            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = $this->ensureMenuIds($item['children']);
            }
        }
        return $items;
    }
    
    public function addMenuItem()
    {
        $newItem = [
            'id' => uniqid('menu_'),
            'label' => '새 메뉴',
            'url' => '',
            'active' => true,
            'type' => 'dropdown',  // type 추가
            'children' => []
        ];
        
        $this->menuItems[] = $newItem;
        
        // UI 업데이트
        $this->dispatch('menuUpdated');
        
        Notification::make()
            ->title('메뉴가 추가되었습니다')
            ->success()
            ->send();
    }
    
    public function addSubMenuItem($parentId)
    {
        $this->menuItems = $this->addSubMenuToParent($this->menuItems, $parentId);
        
        Notification::make()
            ->title('하위 메뉴가 추가되었습니다')
            ->success()
            ->send();
    }
    
    protected function addSubMenuToParent($items, $parentId)
    {
        foreach ($items as &$item) {
            if ($item['id'] === $parentId) {
                if (!isset($item['children'])) {
                    $item['children'] = [];
                }
                $item['children'][] = [
                    'id' => uniqid('menu_'),
                    'label' => '새 하위 메뉴',
                    'url' => '/new-submenu',
                    'active' => true
                ];
                return $items;
            }
            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = $this->addSubMenuToParent($item['children'], $parentId);
            }
        }
        return $items;
    }
    
    public function updateMenuItem($id, $field, $value)
    {
        $this->menuItems = $this->updateMenuItemRecursive($this->menuItems, $id, $field, $value);
        
        // 타입 변경 시 데이터 구조 조정 (데이터는 보존)
        if ($field === 'type') {
            foreach ($this->menuItems as &$item) {
                if ($item['id'] === $id) {
                    switch ($value) {
                        case 'single':
                            // 단일 메뉴로 변경 시 하위 항목을 백업
                            if (isset($item['children']) && !empty($item['children'])) {
                                $item['_backup_children'] = $item['children'];
                            }
                            if (isset($item['groups']) && !empty($item['groups'])) {
                                $item['_backup_groups'] = $item['groups'];
                            }
                            unset($item['children']);
                            unset($item['groups']);
                            break;
                            
                        case 'dropdown':
                            // 드롭다운으로 변경 시 groups는 백업하고 children 복원
                            if (isset($item['groups']) && !empty($item['groups'])) {
                                $item['_backup_groups'] = $item['groups'];
                            }
                            unset($item['groups']);
                            
                            // 백업된 children이 있으면 복원
                            if (isset($item['_backup_children'])) {
                                $item['children'] = $item['_backup_children'];
                                unset($item['_backup_children']);
                            } elseif (!isset($item['children'])) {
                                $item['children'] = [];
                            }
                            
                            // 드롭다운 메뉴는 URL이 없어야 함
                            $item['url'] = '';
                            break;
                            
                        case 'mega':
                            // 메가 메뉴로 변경 시 children은 백업하고 groups 복원
                            if (isset($item['children']) && !empty($item['children'])) {
                                $item['_backup_children'] = $item['children'];
                            }
                            unset($item['children']);
                            
                            // 백업된 groups가 있으면 복원
                            if (isset($item['_backup_groups'])) {
                                $item['groups'] = $item['_backup_groups'];
                                unset($item['_backup_groups']);
                            } elseif (!isset($item['groups'])) {
                                $item['groups'] = [];
                            }
                            
                            // 메가 메뉴도 URL이 없어야 함
                            $item['url'] = '';
                            break;
                    }
                    break;
                }
            }
        }
        
        // 현재 선택된 메뉴 ID와 함께 UI 업데이트
        $this->dispatch('menuUpdated', ['activeMenuId' => $id]);
    }
    
    protected function updateMenuItemRecursive($items, $id, $field, $value)
    {
        foreach ($items as &$item) {
            if ($item['id'] === $id) {
                $item[$field] = $value;
                return $items;
            }
            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = $this->updateMenuItemRecursive($item['children'], $id, $field, $value);
            }
        }
        return $items;
    }
    
    public function deleteMenuItem($id)
    {
        $this->menuItems = $this->deleteMenuItemRecursive($this->menuItems, $id);
        
        Notification::make()
            ->title('메뉴가 삭제되었습니다')
            ->warning()
            ->send();
    }
    
    protected function deleteMenuItemRecursive($items, $id)
    {
        $filtered = [];
        foreach ($items as $item) {
            if ($item['id'] !== $id) {
                if (isset($item['children']) && is_array($item['children'])) {
                    $item['children'] = $this->deleteMenuItemRecursive($item['children'], $id);
                }
                $filtered[] = $item;
            }
        }
        return $filtered;
    }
    
    #[On('menu-items-sorted')]
    public function updateMenuOrder($sortedIds)
    {
        $this->menuItems = $this->sortMenuItems($this->menuItems, $sortedIds);
        
        // 변경사항 즉시 저장
        update_option('header_menu', $this->menuItems);
        
        // UI 업데이트
        $this->dispatch('menuUpdated');
    }
    
    protected function sortMenuItems($items, $sortedIds)
    {
        $sorted = [];
        foreach ($sortedIds as $sortedItem) {
            foreach ($items as $item) {
                if ($item['id'] === $sortedItem['id']) {
                    if (isset($sortedItem['children']) && is_array($sortedItem['children'])) {
                        $item['children'] = $this->sortMenuItems($item['children'] ?? [], $sortedItem['children']);
                    }
                    $sorted[] = $item;
                    break;
                }
            }
        }
        return $sorted;
    }
    
    public function save(): void
    {
        // project_options에 저장
        update_option('header_menu', $this->menuItems);
        
        // 성공 메시지
        Notification::make()
            ->title('메뉴가 성공적으로 저장되었습니다.')
            ->body('변경사항이 웹사이트에 적용되었습니다.')
            ->success()
            ->duration(5000)
            ->send();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('변경사항 저장')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check')
                ->size('lg'),
        ];
    }
    
    public function getTitle(): string
    {
        return '헤더 메뉴 관리';
    }
    
    public function getHeading(): string
    {
        return '헤더 메뉴 관리';
    }
    
    public function getSubheading(): ?string
    {
        return '드래그 앤 드롭으로 메뉴를 편집하고 실시간으로 미리보기를 확인하세요.';
    }
    
    // 페이지 선택 핸들러
    public function handlePageSelect($menuId, $url)
    {
        if (empty($url)) {
            return;
        }
        
        $this->updateMenuItem($menuId, 'url', $url);
    }
    
    // 템플릿 메뉴 추가 메서드들
    public function addCompanyTemplate()
    {
        $companyMenu = [
            'id' => uniqid('menu_'),
            'label' => '회사소개',
            'url' => '',
            'active' => true,
            'type' => 'dropdown',
            'children' => [
                ['id' => uniqid('menu_'), 'label' => 'CEO 인사말', 'url' => '/about/ceo', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '회사 연혁', 'url' => '/about/history', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '조직도', 'url' => '/about/organization', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '오시는 길', 'url' => '/about/location', 'active' => true],
            ]
        ];
        
        $this->menuItems[] = $companyMenu;
        
        // UI 업데이트
        $this->dispatch('menuUpdated');
        
        Notification::make()
            ->title('회사소개 메뉴가 추가되었습니다')
            ->success()
            ->send();
    }
    
    public function addServiceTemplate()
    {
        $serviceMenu = [
            'id' => uniqid('menu_'),
            'label' => '서비스',
            'url' => '',
            'active' => true,
            'type' => 'dropdown',
            'children' => [
                ['id' => uniqid('menu_'), 'label' => '서비스 소개', 'url' => '/service/intro', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '이용 안내', 'url' => '/service/guide', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '요금 안내', 'url' => '/service/pricing', 'active' => true],
            ]
        ];
        
        $this->menuItems[] = $serviceMenu;
        
        // UI 업데이트
        $this->dispatch('menuUpdated');
        
        Notification::make()
            ->title('서비스 메뉴가 추가되었습니다')
            ->success()
            ->send();
    }
    
    public function addSupportTemplate()
    {
        $supportMenu = [
            'id' => uniqid('menu_'),
            'label' => '고객지원',
            'url' => '',
            'active' => true,
            'type' => 'dropdown',
            'children' => [
                ['id' => uniqid('menu_'), 'label' => '공지사항', 'url' => '/support/notice', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => 'FAQ', 'url' => '/support/faq', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '1:1 문의', 'url' => '/support/contact', 'active' => true],
                ['id' => uniqid('menu_'), 'label' => '자료실', 'url' => '/support/downloads', 'active' => true],
            ]
        ];
        
        $this->menuItems[] = $supportMenu;
        
        // UI 업데이트
        $this->dispatch('menuUpdated');
        
        Notification::make()
            ->title('고객지원 메뉴가 추가되었습니다')
            ->success()
            ->send();
    }
    
    // 메가 메뉴 관련 메서드들
    public function addMegaMenuGroup($menuId)
    {
        foreach ($this->menuItems as &$item) {
            if ($item['id'] === $menuId) {
                if (!isset($item['groups'])) {
                    $item['groups'] = [];
                }
                $item['groups'][] = [
                    'group_label' => '새 그룹',
                    'items' => []
                ];
                break;
            }
        }
        
        $this->dispatch('menuUpdated');
        
        Notification::make()
            ->title('메가 메뉴 그룹이 추가되었습니다')
            ->success()
            ->send();
    }
    
    public function updateMegaMenuGroup($menuId, $groupIndex, $field, $value)
    {
        foreach ($this->menuItems as &$item) {
            if ($item['id'] === $menuId && isset($item['groups'][$groupIndex])) {
                $item['groups'][$groupIndex][$field] = $value;
                break;
            }
        }
        
        // 변경사항 즉시 저장
        update_option('header_menu', $this->menuItems);
        
        // UI 업데이트
        $this->dispatch('menuUpdated', ['activeMenuId' => $menuId]);
    }
    
    public function deleteMegaMenuGroup($menuId, $groupIndex)
    {
        foreach ($this->menuItems as &$item) {
            if ($item['id'] === $menuId && isset($item['groups'][$groupIndex])) {
                array_splice($item['groups'], $groupIndex, 1);
                break;
            }
        }
        
        $this->dispatch('menuUpdated');
        
        Notification::make()
            ->title('메가 메뉴 그룹이 삭제되었습니다')
            ->warning()
            ->send();
    }
    
    public function addMegaMenuItem($menuId, $groupIndex)
    {
        foreach ($this->menuItems as &$item) {
            if ($item['id'] === $menuId && isset($item['groups'][$groupIndex])) {
                $item['groups'][$groupIndex]['items'][] = [
                    'label' => '새 메뉴',
                    'url' => '/new-menu'
                ];
                break;
            }
        }
        
        $this->dispatch('menuUpdated');
    }
    
    public function updateMegaMenuItem($menuId, $groupIndex, $itemIndex, $field, $value)
    {
        foreach ($this->menuItems as &$item) {
            if ($item['id'] === $menuId && 
                isset($item['groups'][$groupIndex]) && 
                isset($item['groups'][$groupIndex]['items'][$itemIndex])) {
                $item['groups'][$groupIndex]['items'][$itemIndex][$field] = $value;
                break;
            }
        }
        
        // 변경사항 즉시 저장
        update_option('header_menu', $this->menuItems);
        
        // UI 업데이트
        $this->dispatch('menuUpdated', ['activeMenuId' => $menuId]);
    }
    
    public function deleteMegaMenuItem($menuId, $groupIndex, $itemIndex)
    {
        foreach ($this->menuItems as &$item) {
            if ($item['id'] === $menuId && 
                isset($item['groups'][$groupIndex]) && 
                isset($item['groups'][$groupIndex]['items'][$itemIndex])) {
                array_splice($item['groups'][$groupIndex]['items'], $itemIndex, 1);
                break;
            }
        }
        
        $this->dispatch('menuUpdated');
    }
}