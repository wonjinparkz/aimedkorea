<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\Post;

class BlogPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    
    protected static ?string $navigationLabel = '블로그';
    
    protected static ?string $modelLabel = '블로그';
    
    protected static ?string $pluralModelLabel = '블로그';
    
    protected static ?string $postType = Post::TYPE_BLOG;
    
    protected static ?string $navigationGroup = '콘텐츠';
    
    protected static ?int $navigationSort = 31;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_content-view') && PermissionHelper::hasPermission('blog_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('blog_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('blog_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('blog_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('blog_posts-delete');
    }
}