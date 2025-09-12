<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Models\Post;
use App\Filament\Resources\ProductPostResource\Pages;

class ProductPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = '상품';

    protected static ?string $postType = Post::TYPE_PRODUCT;

    protected static ?string $navigationGroup = '콘텐츠';

    protected static ?int $navigationSort = 35;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_PRODUCT)->count();
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
        return (PermissionHelper::hasPermission('section_content-view') && PermissionHelper::hasPermission('product_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('product_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('product_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('product_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('product_posts-delete');
    }
}