<?php

namespace App\Filament\Resources\PromotionPostResource\Pages;

use App\Filament\Resources\PromotionPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PromotionPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}