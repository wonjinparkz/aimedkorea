<?php

namespace App\Filament\Resources\HeroResource\Pages;

use App\Filament\Resources\HeroResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\View\View;

class CreateHero extends CreateRecord
{
    protected static string $resource = HeroResource::class;
    
    public function getFooter(): ?View
    {
        return view('filament.resources.hero-resource.hero-preview-script');
    }
}
