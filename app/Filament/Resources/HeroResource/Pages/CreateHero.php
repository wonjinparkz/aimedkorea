<?php

namespace App\Filament\Resources\HeroResource\Pages;

use App\Filament\Resources\HeroResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHero extends CreateRecord
{
    protected static string $resource = HeroResource::class;
    
    public function getFooter(): ?string
    {
        return view('filament.resources.hero-resource.hero-preview-script')->render();
    }
}
