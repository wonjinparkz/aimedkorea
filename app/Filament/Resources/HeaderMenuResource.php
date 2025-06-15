<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderMenuResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class HeaderMenuResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?string $navigationLabel = '헤더 메뉴 관리';
    
    protected static ?string $navigationGroup = '사이트 관리';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $slug = 'header-menu';

    public static function form(Form $form): Form
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
                                            ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                                $set('slug', Str::slug($state))
                                            ),
                                        
                                        TextInput::make('url')
                                            ->label('링크 주소')
                                            ->placeholder('예: /about 또는 https://...')
                                            ->helperText('페이지 주소를 입력하세요 (하위 메뉴가 있으면 비워두세요)')
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
                                            ->placeholder('예: /about/greeting'),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'dropdown')
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
                                                    ->placeholder('예: /about/ceo'),
                                            ])
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                            ->addActionLabel('메뉴 추가')
                                            ->reorderable()
                                            ->collapsible(),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'mega')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHeaderMenus::route('/'),
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
