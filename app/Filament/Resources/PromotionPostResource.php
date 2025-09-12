<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Models\Post;
use App\Filament\Resources\PromotionPostResource\Pages;

class PromotionPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = '홍보';

    protected static ?string $postType = Post::TYPE_PROMOTION;

    protected static ?string $navigationGroup = '마케팅';

    protected static ?int $navigationSort = 90;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_PROMOTION)->count();
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
        return (PermissionHelper::hasPermission('section_marketing-view') && PermissionHelper::hasPermission('promotion_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('promotion_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('promotion_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('promotion_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('promotion_posts-delete');
    }
}