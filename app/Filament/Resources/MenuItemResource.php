<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    
    protected static ?string $navigationLabel = '메뉴 항목';
    
    protected static ?string $modelLabel = '메뉴 항목';
    
    protected static ?string $pluralModelLabel = '메뉴 항목 목록';

    protected static ?string $navigationGroup = '사이트 관리';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->description('메뉴 항목의 기본 정보를 입력해주세요.')
                    ->schema([
                        Forms\Components\Select::make('menu_id')
                            ->label('소속 메뉴')
                            ->options(Menu::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('이 항목이 속할 메뉴를 선택하세요.'),
                            
                        Forms\Components\Select::make('parent_id')
                            ->label('상위 메뉴 항목')
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('최상위 메뉴')
                            ->helperText('하위 메뉴로 만들려면 상위 항목을 선택하세요.'),
                            
                        Forms\Components\TextInput::make('title')
                            ->label('메뉴 이름')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('예: 회사소개')
                            ->helperText('웹사이트에 표시될 메뉴 이름입니다.'),
                            
                        Forms\Components\TextInput::make('url')
                            ->label('링크 주소')
                            ->maxLength(255)
                            ->placeholder('예: /about 또는 https://example.com')
                            ->helperText('클릭 시 이동할 페이지 주소입니다.')
                            ->suffixIcon('heroicon-m-link'),
                            
                        Forms\Components\Select::make('target')
                            ->label('링크 열기 방식')
                            ->options([
                                '_self' => '현재 창에서 열기',
                                '_blank' => '새 창에서 열기',
                            ])
                            ->default('_self')
                            ->helperText('링크를 어떻게 열지 선택하세요.'),
                            
                        Forms\Components\TextInput::make('order')
                            ->label('표시 순서')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('숫자가 작을수록 먼저 표시됩니다.'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('추가 설정')
                    ->description('메뉴의 추가 기능을 설정할 수 있습니다.')
                    ->schema([
                        Forms\Components\Toggle::make('is_mega_menu')
                            ->label('메가 메뉴 사용')
                            ->helperText('큰 드롭다운 메뉴를 사용하려면 활성화하세요.')
                            ->reactive()
                            ->afterStateUpdated(fn (Set $set, ?bool $state) => 
                                $state ?: $set('mega_menu_content', null)
                            ),
                            
                        Forms\Components\TextInput::make('icon')
                            ->label('아이콘')
                            ->placeholder('예: heroicon-o-home')
                            ->helperText('메뉴 앞에 표시할 아이콘 (선택사항)'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(2)
                            ->placeholder('메뉴에 대한 간단한 설명')
                            ->helperText('메가 메뉴에서 표시될 설명입니다.'),
                            
                        Forms\Components\TextInput::make('css_class')
                            ->label('CSS 클래스')
                            ->placeholder('예: text-primary')
                            ->helperText('특별한 스타일을 적용하려면 입력하세요.'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('사용 여부')
                            ->default(true)
                            ->helperText('비활성화하면 웹사이트에 표시되지 않습니다.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('메가 메뉴 설정')
                    ->description('메가 메뉴의 내용을 구성합니다.')
                    ->schema([
                        Forms\Components\Repeater::make('mega_menu_content.columns')
                            ->label('메가 메뉴 컬럼')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('컬럼 제목')
                                    ->helperText('예: 제품 & 서비스'),
                                    
                                Forms\Components\Repeater::make('items')
                                    ->label('항목들')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('항목 이름')
                                            ->required(),
                                            
                                        Forms\Components\TextInput::make('url')
                                            ->label('링크')
                                            ->required(),
                                            
                                        Forms\Components\Textarea::make('description')
                                            ->label('설명')
                                            ->rows(2),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                    ->collapsible()
                                    ->cloneable()
                                    ->reorderableWithButtons()
                                    ->addActionLabel('항목 추가'),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? '새 컬럼')
                            ->collapsible()
                            ->cloneable()
                            ->reorderableWithButtons()
                            ->addActionLabel('컬럼 추가')
                            ->grid(1),
                    ])
                    ->visible(fn (Get $get): bool => $get('is_mega_menu') === true)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $menuId = request()->get('menu_id');
                if ($menuId) {
                    $query->where('menu_id', $menuId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('메뉴 이름')
                    ->searchable()
                    ->sortable()
                    ->size('lg')
                    ->weight('bold')
                    ->description(fn (MenuItem $record): ?string => 
                        $record->parent ? "└ {$record->parent->title}" : null
                    ),
                    
                Tables\Columns\TextColumn::make('menu.name')
                    ->label('소속 메뉴')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('url')
                    ->label('링크')
                    ->limit(30)
                    ->tooltip(fn (MenuItem $record): ?string => $record->url)
                    ->copyable()
                    ->copyMessage('링크가 복사되었습니다.'),
                    
                Tables\Columns\IconColumn::make('is_mega_menu')
                    ->label('메가메뉴')
                    ->boolean()
                    ->trueIcon('heroicon-o-squares-2x2')
                    ->falseIcon('heroicon-o-minus'),
                    
                Tables\Columns\TextColumn::make('order')
                    ->label('순서')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('사용')
                    ->onColor('success')
                    ->offColor('danger'),
                    
                Tables\Columns\TextColumn::make('children_count')
                    ->label('하위 항목')
                    ->counts('children')
                    ->badge()
                    ->color('info')
                    ->visible(fn () => !request()->get('menu_id')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_id')
                    ->label('메뉴 선택')
                    ->options(Menu::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('사용 여부')
                    ->boolean()
                    ->trueLabel('사용 중')
                    ->falseLabel('미사용'),
                    
                Tables\Filters\TernaryFilter::make('is_mega_menu')
                    ->label('메가 메뉴')
                    ->boolean()
                    ->trueLabel('메가 메뉴만')
                    ->falseLabel('일반 메뉴만'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('수정')
                    ->size('lg'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('삭제')
                    ->size('lg')
                    ->requiresConfirmation()
                    ->modalHeading('메뉴 항목 삭제')
                    ->modalDescription('이 메뉴 항목을 삭제하시겠습니까? 하위 항목도 함께 삭제됩니다.')
                    ->modalSubmitActionLabel('삭제'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('선택한 항목 삭제')
                        ->modalDescription('선택한 메뉴 항목들을 삭제하시겠습니까?')
                        ->modalSubmitActionLabel('삭제'),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->emptyStateHeading('메뉴 항목이 없습니다')
            ->emptyStateDescription('메뉴에 표시할 항목을 추가해주세요.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('메뉴 항목 추가')
                    ->icon('heroicon-o-plus-circle')
                    ->size('lg'),
            ]);
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
