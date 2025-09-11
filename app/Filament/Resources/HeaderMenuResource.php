<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderMenuResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class HeaderMenuResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?string $navigationLabel = '헤더 메뉴 관리';
    
    protected static ?string $navigationGroup = '사이트';
    
    protected static ?int $navigationSort = 100;
    
    protected static ?string $slug = 'header-menu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 안내 메시지
                Section::make()
                    ->schema([
                        Placeholder::make('guide')
                            ->label('')
                            ->content('🔍 메뉴를 추가하고 드래그하여 순서를 변경할 수 있습니다. 하위 메뉴가 있으면 자동으로 드롭다운 메뉴가 됩니다.')
                    ]),
                    
                Section::make('헤더 메뉴 설정')
                    ->description('웹사이트 상단에 표시되는 메뉴를 관리합니다.')
                    ->schema([
                        Repeater::make('menu_items')
                            ->label('메뉴 목록')
                            ->schema([
                                // 다국어 메뉴 이름
                                Section::make('메뉴 이름 (다국어)')
                                    ->schema([
                                        Grid::make(5)
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('🇰🇷 한국어')
                                                    ->required()
                                                    ->placeholder('예: 회사소개'),
                                                
                                                TextInput::make('label_eng')
                                                    ->label('🇬🇧 English')
                                                    ->placeholder('ex: About Us'),
                                                
                                                TextInput::make('label_chn')
                                                    ->label('🇨🇳 中文')
                                                    ->placeholder('例: 关于我们'),
                                                
                                                TextInput::make('label_hin')
                                                    ->label('🇮🇳 हिन्दी')
                                                    ->placeholder('उदा: हमारे बारे में'),
                                                
                                                TextInput::make('label_arb')
                                                    ->label('🇸🇦 العربية')
                                                    ->placeholder('مثال: معلومات عنا'),
                                            ]),
                                    ])
                                    ->collapsible(),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('url')
                                            ->label('🔗 링크 주소')
                                            ->placeholder('/about')
                                            ->helperText('하위 메뉴가 있으면 비워두세요')
                                            ->reactive()
                                            ->extraAttributes([
                                                'style' => 'font-size: 1.1rem;'
                                            ]),
                                            
                                        Toggle::make('active')
                                            ->label('활성화')
                                            ->default(true)
                                            ->inline()
                                            ->helperText('메뉴 표시 여부'),
                                    ]),
                                
                                // 하위 메뉴 (단순화)
                                Repeater::make('children')
                                    ->label('📂 하위 메뉴')
                                    ->schema([
                                        Section::make('하위 메뉴 이름 (다국어)')
                                            ->schema([
                                                Grid::make(5)
                                                    ->schema([
                                                        TextInput::make('label')
                                                            ->label('🇰🇷 한국어')
                                                            ->required()
                                                            ->placeholder('예: CEO 인사말'),
                                                        
                                                        TextInput::make('label_eng')
                                                            ->label('🇬🇧 English')
                                                            ->placeholder('ex: CEO Message'),
                                                        
                                                        TextInput::make('label_chn')
                                                            ->label('🇨🇳 中文')
                                                            ->placeholder('例: CEO致辞'),
                                                        
                                                        TextInput::make('label_hin')
                                                            ->label('🇮🇳 हिन्दी')
                                                            ->placeholder('उदा: CEO संदेश'),
                                                        
                                                        TextInput::make('label_arb')
                                                            ->label('🇸🇦 العربية')
                                                            ->placeholder('مثال: رسالة الرئيس'),
                                                    ]),
                                            ])
                                            ->compact(),
                                        
                                        TextInput::make('url')
                                            ->label('링크 주소')
                                            ->required()
                                            ->placeholder('/about/ceo')
                                            ->extraAttributes([
                                                'style' => 'font-size: 1.05rem;'
                                            ]),
                                    ])
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => '└─ ' . ($state['label'] ?? '하위 메뉴'))
                                    ->addActionLabel('➕ 하위 메뉴 추가')
                                    ->reorderable()
                                    ->collapsible()
                                    ->helperText('하위 메뉴를 추가하면 자동으로 드롭다운 메뉴가 됩니다'),
                            ])
                            ->collapsed()
                            ->itemLabel(function (array $state): ?string {
                                $label = $state['label'] ?? '새 메뉴';
                                $childCount = count($state['children'] ?? []);
                                return $childCount > 0 
                                    ? "📁 {$label} ({$childCount}개 하위메뉴)" 
                                    : "📄 {$label}";
                            })
                            ->addActionLabel('➕ 메인 메뉴 추가')
                            ->reorderable()
                            ->maxItems(10)
                            ->defaultItems(0)
                            ->collapsible()
                            ->extraAttributes([
                                'class' => 'menu-repeater'
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\CustomManageHeaderMenus::route('/'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        $menuItems = get_option('header_menu', []);
        return count($menuItems) > 0 ? count($menuItems) : null;
    }
}