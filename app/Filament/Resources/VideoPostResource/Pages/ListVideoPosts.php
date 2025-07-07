<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideoPosts extends ListRecords
{
    protected static string $resource = VideoPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}