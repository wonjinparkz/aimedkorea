<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Filament\Resources\RoutinePostResource\Pages;
use App\Models\Post;

class RoutinePostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = '루틴';
    
    protected static ?string $modelLabel = '루틴';
    
    protected static ?string $pluralModelLabel = '루틴';
    
    protected static ?string $postType = Post::TYPE_ROUTINE;
    
    protected static ?string $navigationGroup = '루틴';
    
    protected static ?int $navigationSort = 50;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutinePosts::route('/'),
            'create' => Pages\CreateRoutinePost::route('/create'),
            'edit' => Pages\EditRoutinePost::route('/{record}/edit'),
        ];
    }

    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_routine-view') && PermissionHelper::hasPermission('routine_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        // URL 직접 접근 시 route guard 로깅
        if (!PermissionHelper::hasPermission('section_routine-view') || !PermissionHelper::hasPermission('routine_posts-view')) {
            PermissionHelper::requireRouteAccess('routine_posts-view');
        }
        return PermissionHelper::hasPermission('routine_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('routine_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('routine_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('routine_posts-delete');
    }
}