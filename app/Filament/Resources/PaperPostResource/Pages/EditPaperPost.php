<?php

namespace App\Filament\Resources\PaperPostResource\Pages;

use App\Filament\Resources\PaperPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;
use Filament\Actions;

class EditPaperPost extends EditPost
{
    protected static string $resource = PaperPostResource::class;

    protected function getHeaderActions(): array
    {
        $actions = parent::getHeaderActions();
        
        // Add ViewAction at the beginning
        array_unshift($actions, Actions\ViewAction::make());
        
        return $actions;
    }
}