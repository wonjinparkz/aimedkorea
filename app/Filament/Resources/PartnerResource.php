<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Helpers\PermissionHelper;
use App\Filament\Resources\PartnerResource\Pages;

class PartnerResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    
    protected static ?string $navigationLabel = '파트너사 관리';
    
    protected static ?string $modelLabel = '파트너사';
    
    protected static ?string $pluralModelLabel = '파트너사';
    
    protected static ?string $navigationGroup = '파트너';
    
    protected static ?int $navigationSort = 60;
    
    protected static ?string $slug = 'partners';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePartners::route('/'),
        ];
    }
    
    // 네비게이션 표시 여부 제어
    public static function shouldRegisterNavigation(): bool
    {
        return (PermissionHelper::hasPermission('section_partner-view') && PermissionHelper::hasPermission('partners-view')) || PermissionHelper::isAdmin();
    }
    
    // 권한 메서드들
    public static function canViewAny(): bool
    {
        // URL 직접 접근 시 route guard 로깅
        if (!PermissionHelper::hasPermission('section_partner-view') || !PermissionHelper::hasPermission('partners-view')) {
            PermissionHelper::requireRouteAccess('partners-view');
        }
        return PermissionHelper::hasPermission('partners-view');
    }
    
    public static function canCreate(): bool
    {
        return PermissionHelper::hasPermission('partners-create');
    }
    
    public static function canEdit($record): bool
    {
        return PermissionHelper::hasPermission('partners-edit');
    }
    
    public static function canDelete($record): bool
    {
        return PermissionHelper::hasPermission('partners-delete');
    }
}