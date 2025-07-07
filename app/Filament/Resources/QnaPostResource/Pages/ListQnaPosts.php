<?php

namespace App\Filament\Resources\QnaPostResource\Pages;

use App\Filament\Resources\QnaPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQnaPosts extends ListRecords
{
    protected static string $resource = QnaPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}