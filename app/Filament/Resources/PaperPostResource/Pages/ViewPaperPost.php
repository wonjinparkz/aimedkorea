<?php

namespace App\Filament\Resources\PaperPostResource\Pages;

use App\Filament\Resources\PaperPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPaperPost extends ViewRecord
{
    protected static string $resource = PaperPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('기본 정보')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('제목'),
                            
                        Infolists\Components\TextEntry::make('authors')
                            ->label('저자')
                            ->badge()
                            ->separator(', '),
                            
                        Infolists\Components\TextEntry::make('publisher')
                            ->label('발행기관'),
                            
                        Infolists\Components\TextEntry::make('published_at')
                            ->label('발행일')
                            ->date('Y-m-d'),
                            
                        Infolists\Components\TextEntry::make('link')
                            ->label('논문 링크')
                            ->url(fn ($record) => $record->link)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('내용')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('본문내용')
                            ->html()
                            ->columnSpanFull(),
                    ]),
                    
                Infolists\Components\Section::make('게시 정보')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_published')
                            ->label('게시 상태')
                            ->boolean(),
                            
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('추천 여부')
                            ->boolean(),
                            
                        Infolists\Components\TextEntry::make('author.name')
                            ->label('작성자'),
                            
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('생성일')
                            ->dateTime('Y-m-d H:i:s'),
                            
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('수정일')
                            ->dateTime('Y-m-d H:i:s'),
                    ])
                    ->columns(2),
            ]);
    }
}