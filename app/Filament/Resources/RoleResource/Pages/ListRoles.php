<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('permissions')
                ->label('권한 배치표')
                ->icon('heroicon-o-shield-check')
                ->url(fn (): string => RoleResource::getUrl('permissions'))
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
