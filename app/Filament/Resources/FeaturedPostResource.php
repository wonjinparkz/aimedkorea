<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Filament\Resources\FeaturedPostResource\Pages;
use App\Models\Post;

class FeaturedPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationLabel = 'The Science Card';
    
    protected static ?string $modelLabel = 'The Science Card';
    
    protected static ?string $pluralModelLabel = 'The Science Card';
    
    protected static ?string $postType = Post::TYPE_FEATURED;
    
    protected static ?string $navigationGroup = '홈 구성';
    
    protected static ?int $navigationSort = 24;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeaturedPosts::route('/'),
            'create' => Pages\CreateFeaturedPost::route('/create'),
            'edit' => Pages\EditFeaturedPost::route('/{record}/edit'),
        ];
    }

    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_home-view') && PermissionHelper::hasPermission('featured_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('featured_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('featured_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('featured_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('featured_posts-delete');
    }
}