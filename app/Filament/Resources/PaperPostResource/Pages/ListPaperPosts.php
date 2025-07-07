<?php

namespace App\Filament\Resources\PaperPostResource\Pages;

use App\Filament\Resources\PaperPostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPaperPosts extends ListRecords
{
    protected static string $resource = PaperPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getTableQuery(): Builder
    {
        return Post::query()->where('type', Post::TYPE_PAPER);
    }
}