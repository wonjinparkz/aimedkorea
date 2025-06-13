<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?string $navigationLabel = '메뉴 관리';
    
    protected static ?string $modelLabel = '메뉴';
    
    protected static ?string $pluralModelLabel = '메뉴 목록';

    protected static ?string $navigationGroup = '사이트 관리';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('메뉴 정보')
                    ->description('메뉴의 기본 정보를 입력해주세요.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('메뉴 이름')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('예: 메인 메뉴')
                            ->helperText('관리자가 구분하기 위한 메뉴 이름입니다.'),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('메뉴 코드')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('예: main-menu')
                            ->helperText('영문 소문자와 하이픈(-)만 사용 가능합니다.')
                            ->regex('/^[a-z0-9-]+$/'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('사용 여부')
                            ->default(true)
                            ->helperText('비활성화하면 웹사이트에 표시되지 않습니다.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('메뉴 이름')
                    ->searchable()
                    ->sortable()
                    ->size('lg')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('slug')
                    ->label('메뉴 코드')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('메뉴 코드가 복사되었습니다.')
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('사용 여부')
                    ->onColor('success')
                    ->offColor('danger')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark'),
                    
                Tables\Columns\TextColumn::make('items_count')
                    ->label('메뉴 항목 수')
                    ->counts('items')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('수정일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('사용 여부')
                    ->boolean()
                    ->trueLabel('사용 중')
                    ->falseLabel('미사용')
                    ->placeholder('전체'),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_items')
                    ->label('메뉴 항목 관리')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->url(fn (Menu $record): string => MenuItemResource::getUrl('index', ['menu_id' => $record->id]))
                    ->color('info')
                    ->size('lg'),
                    
                Tables\Actions\EditAction::make()
                    ->label('수정')
                    ->size('lg'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('삭제')
                    ->size('lg')
                    ->requiresConfirmation()
                    ->modalHeading('메뉴 삭제')
                    ->modalDescription('이 메뉴를 삭제하시겠습니까? 메뉴에 속한 모든 항목도 함께 삭제됩니다.')
                    ->modalSubmitActionLabel('삭제'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('선택한 메뉴 삭제')
                        ->modalDescription('선택한 메뉴들을 삭제하시겠습니까? 메뉴에 속한 모든 항목도 함께 삭제됩니다.')
                        ->modalSubmitActionLabel('삭제'),
                ]),
            ])
            ->emptyStateHeading('메뉴가 없습니다')
            ->emptyStateDescription('새 메뉴를 만들어 웹사이트의 내비게이션을 구성하세요.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('메뉴 만들기')
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
