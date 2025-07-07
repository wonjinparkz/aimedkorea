<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Resources\PartnerResource\Pages;

class PartnerResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    
    protected static ?string $navigationLabel = '파트너사 관리';
    
    protected static ?string $modelLabel = '파트너사';
    
    protected static ?string $pluralModelLabel = '파트너사';
    
    protected static ?string $navigationGroup = '설정';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $slug = 'partners';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePartners::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}