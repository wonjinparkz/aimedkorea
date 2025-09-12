<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Filament\Resources\BannerPostResource\Pages;
use App\Models\Post;

class BannerPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Hero 콘텐츠';
    
    protected static ?string $modelLabel = 'Hero 콘텐츠';
    
    protected static ?string $pluralModelLabel = 'Hero 콘텐츠';
    
    protected static ?string $postType = Post::TYPE_BANNER;
    
    protected static ?string $navigationGroup = '홈 구성';
    
    protected static ?int $navigationSort = 22;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBannerPosts::route('/'),
            'create' => Pages\CreateBannerPost::route('/create'),
            'edit' => Pages\EditBannerPost::route('/{record}/edit'),
        ];
    }

    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_home-view') && PermissionHelper::hasPermission('banner_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('banner_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('banner_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('banner_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('banner_posts-delete');
    }
}