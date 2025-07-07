<?php

namespace App\Filament\Resources\ProductPostResource\Pages;

use App\Filament\Resources\ProductPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = ProductPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}