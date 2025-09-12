<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use App\Models\Permission;
use App\Helpers\PermissionHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\CheckboxList;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = '설정';
    
    protected static ?int $navigationSort = 101;
    
    public static function getLabel(): string
    {
        return '역할';
    }
    
    public static function getPluralLabel(): string
    {
        return '역할 관리';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('역할 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('역할명')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('display_name_ko')
                            ->label('한국어 표시명')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('display_name_en')
                            ->label('영어 표시명')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('level')
                            ->label('권한 레벨')
                            ->helperText('높을수록 더 많은 권한을 가짐')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                    
                Section::make('권한 설정')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('권한')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                $permissions = Permission::all()->groupBy('module');
                                $options = [];
                                
                                foreach ($permissions as $module => $modulePermissions) {
                                    foreach ($modulePermissions as $permission) {
                                        $options[$permission->id] = "[{$module}] {$permission->display_name}";
                                    }
                                }
                                
                                return $options;
                            })
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('역할명')
                    ->searchable(['display_name_ko', 'display_name_en', 'name']),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('레벨')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'danger',
                        $state >= 75 => 'warning',
                        $state >= 50 => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('사용자 수')
                    ->counts('users')
                    ->badge(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('권한 수')
                    ->counts('permissions')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => PermissionHelper::hasPermission('roles-edit')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Role $record) => 
                        PermissionHelper::hasPermission('roles-delete') && 
                        !in_array($record->slug, ['admin', 'user'])
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => PermissionHelper::hasPermission('roles-delete')),
                ]),
            ])
            ->defaultSort('level', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasModulePermission('roles', 'view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('roles-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('roles-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('roles-delete') && 
               !in_array($record->slug, ['admin', 'user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
            'permissions' => Pages\PermissionMatrix::route('/permissions'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_settings-view') && PermissionHelper::hasPermission('roles-view')) || PermissionHelper::isAdmin();
    }
}
