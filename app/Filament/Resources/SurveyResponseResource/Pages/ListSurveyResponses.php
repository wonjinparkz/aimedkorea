<?php

namespace App\Filament\Resources\SurveyResponseResource\Pages;

use App\Filament\Resources\SurveyResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSurveyResponses extends ListRecords
{
    protected static string $resource = SurveyResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 응답은 직접 생성하지 않음
        ];
    }
    
    public function getTabs(): array
    {
        return [
            '전체' => Tab::make(),
            '오늘' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today())),
            '이번 주' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            '이번 달' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('created_at', now()->month)),
        ];
    }
}
