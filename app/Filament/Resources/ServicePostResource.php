<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Models\Post;
use App\Filament\Resources\ServicePostResource\Pages;

class ServicePostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = '서비스';

    protected static ?string $postType = Post::TYPE_SERVICE;

    protected static ?string $navigationGroup = '콘텐츠';

    protected static ?int $navigationSort = 36;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_SERVICE)->count();
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_content-view') && PermissionHelper::hasPermission('service_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('service_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('service_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('service_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('service_posts-delete');
    }
}