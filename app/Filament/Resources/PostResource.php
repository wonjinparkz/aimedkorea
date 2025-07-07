<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Forms\Components\QuillEditor;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

abstract class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationGroup = '게시물 관리';

    protected static ?string $postType = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\ViewField::make('language_selector')
                            ->view('filament.forms.language-selector')
                            ->label('언어 버전')
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('language')
                            ->default('kor'),
                        
                        Forms\Components\Hidden::make('base_slug'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('게시물 정보')
                    ->schema([
                        
                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Hidden::make('type')
                            ->default(static::$postType),
                        
                        Forms\Components\Textarea::make('summary')
                            ->label('요약')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('read_more_text')
                            ->label('더보기 문구')
                            ->default('더 보기')
                            ->maxLength(255),
                        
                        QuillEditor::make('content')
                            ->label('본문')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/content-images')
                            ->minHeight(400),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('이미지')
                            ->helperText('387x217 비율로 크롭됩니다')
                            ->image()
                            ->directory('posts')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '387:217',
                            ])
                            ->imageCropAspectRatio('387:217')
                            ->imageResizeTargetWidth(387)
                            ->imageResizeTargetHeight(217)
                            ->imageResizeMode('cover'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('summary')
                    ->label('요약')
                    ->limit(50),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('이미지'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label('언어')
                    ->options([
                        'kor' => '한국어 (KOR)',
                        'eng' => '영어 (ENG)',
                        'chn' => '중국어 (CHN)',
                        'hin' => '힌디어 (HIN)',
                        'arb' => '아랍어 (ARB)',
                    ])
                    ->multiple(),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('중요 게시글'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('change_language')
                        ->label('언어 변경')
                        ->icon('heroicon-o-language')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('language')
                                ->label('언어 선택')
                                ->options([
                                    'kor' => '한국어 (KOR)',
                                    'eng' => '영어 (ENG)',
                                    'chn' => '중국어 (CHN)',
                                    'hin' => '힌디어 (HIN)',
                                    'arb' => '아랍어 (ARB)',
                                ])
                                ->required()
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn ($record) => $record->update(['language' => $data['language']]));
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        // For list view, show only Korean posts
        if (request()->routeIs('filament.admin.resources.*.index')) {
            return parent::getEloquentQuery()
                ->where('type', static::$postType)
                ->where('language', 'kor');
        }
        
        // For edit/view, allow all languages
        return parent::getEloquentQuery()
            ->where('type', static::$postType);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
