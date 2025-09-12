<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Role;
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
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = '설정';
    
    protected static ?int $navigationSort = 102;
    
    public static function getLabel(): string
    {
        return '사용자';
    }
    
    public static function getPluralLabel(): string
    {
        return '사용자 관리';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('사용자 정보')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->label('사용자명')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('이름')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('이메일')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('비밀번호')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                    
                Section::make('역할 할당')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('역할')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                return Role::all()->mapWithKeys(function ($role) {
                                    return [$role->id => $role->display_name . " (Level {$role->level})"];
                                });
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
                Tables\Columns\ImageColumn::make('profile_photo_path')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (User $record) => $record->profile_photo_url),
                Tables\Columns\TextColumn::make('username')
                    ->label('사용자명')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('이메일')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('roles.display_name')
                    ->label('역할')
                    ->badge()
                    ->color(fn ($record) => [
                        'danger' => $record->hasRole('admin'),
                        'warning' => $record->hasRole('site-manager'),
                        'success' => $record->hasRole('content-manager'),
                        'gray' => true,
                    ])
                    ->separator(', '),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('인증')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('가입일')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => PermissionHelper::hasPermission('users-edit')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => 
                        PermissionHelper::hasPermission('users-delete') && 
                        $record->id !== auth()->id()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => PermissionHelper::hasPermission('users-delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasModulePermission('users', 'view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('users-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('users-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('users-delete') && 
               $record->id !== auth()->id();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_settings-view') && PermissionHelper::hasPermission('users-view')) || PermissionHelper::isAdmin();
    }
}
