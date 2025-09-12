<?php

namespace App\Filament\Resources;

use App\Helpers\PermissionHelper;
use App\Filament\Resources\NewsPostResource\Pages;
use App\Models\Post;

class NewsPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = '관련기사';
    
    protected static ?string $modelLabel = '관련기사';
    
    protected static ?string $pluralModelLabel = '관련기사';
    
    protected static ?string $postType = Post::TYPE_NEWS;
    
    protected static ?string $navigationGroup = '콘텐츠';
    
    protected static ?int $navigationSort = 32;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsPosts::route('/'),
            'create' => Pages\CreateNewsPost::route('/create'),
            'edit' => Pages\EditNewsPost::route('/{record}/edit'),
        ];
    }

    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_content-view') && PermissionHelper::hasPermission('news_posts-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        return PermissionHelper::hasPermission('news_posts-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('news_posts-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('news_posts-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('news_posts-delete');
    }
}