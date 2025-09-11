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
                        Forms\Components\Hidden::make('content_group_id'),
                        Forms\Components\Hidden::make('is_primary')->default(true),
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
                
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('주 게시글')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-language'),
                
                Tables\Columns\TextColumn::make('content_group_id')
                    ->label('그룹 ID')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                    
                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('주 게시글')
                    ->default(true),
                    
                Tables\Filters\SelectFilter::make('language')
                    ->label('언어')
                    ->options([
                        'kor' => '한국어 (KOR)',
                        'eng' => '영어 (ENG)',
                        'chn' => '중국어 (CHN)',
                        'hin' => '힌디어 (HIN)',
                        'arb' => '아랍어 (ARB)',
                    ])
                    ->default('kor'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('type', static::$postType);
        
        // For list view, show based on filters
        if (request()->routeIs('filament.admin.resources.*.index')) {
            // Check if 'is_primary' filter is explicitly set to false
            $isPrimaryFilter = request()->get('tableFilters.is_primary.value');
            // Check if 'language' filter is set
            $languageFilter = request()->get('tableFilters.language.value');
            
            if ($isPrimaryFilter === null || $isPrimaryFilter === '1') {
                // Default: show only primary posts (Korean or main posts)
                $query->where('is_primary', true);
            }
            // If filter is set to '0' (false), show all posts
            // If filter is set to '' (blank), show all posts
            
            // Apply language filter if specified
            if ($languageFilter) {
                $query->where('language', $languageFilter);
            } else {
                // Default: show only Korean posts
                $query->where('language', 'kor');
            }
        }
        
        return $query;
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
