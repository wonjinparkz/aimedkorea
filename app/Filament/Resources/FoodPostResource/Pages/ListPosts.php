<?php

namespace App\Filament\Resources\FoodPostResource\Pages;

use App\Filament\Resources\FoodPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = FoodPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}