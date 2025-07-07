<?php

namespace App\Filament\Resources;

use App\Forms\Components\QuillEditor;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaperPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = '논문요약';
    
    protected static ?string $modelLabel = '논문요약';
    
    protected static ?string $pluralModelLabel = '논문요약';
    
    protected static ?string $postType = Post::TYPE_PAPER;
    
    protected static ?string $navigationGroup = '게시물 관리';
    
    protected static ?int $navigationSort = 7;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('언어 선택')
                    ->schema([
                        Forms\Components\Select::make('language')
                            ->label('언어')
                            ->options([
                                'kor' => '한국어',
                                'eng' => 'English',
                                'chn' => '中文',
                                'hin' => 'हिन्दी',
                                'arb' => 'العربية',
                            ])
                            ->default('kor')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $baseSlug = $get('base_slug');
                                if ($baseSlug) {
                                    $set('slug', $baseSlug . '-' . $state);
                                }
                            }),
                            
                        Forms\Components\Hidden::make('base_slug'),
                    ])
                    ->columns(1),
                    
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (!$get('base_slug')) {
                                    $baseSlug = Str::slug($state);
                                    $set('base_slug', $baseSlug);
                                    $language = $get('language') ?: 'kor';
                                    $set('slug', $baseSlug . '-' . $language);
                                }
                            }),
                            
                        Forms\Components\Hidden::make('slug'),
                            
                        Forms\Components\TagsInput::make('authors')
                            ->label('저자')
                            ->placeholder('저자명을 입력하고 Enter를 누르세요')
                            ->helperText('여러 명의 저자를 추가할 수 있습니다'),
                            
                        Forms\Components\TextInput::make('publisher')
                            ->label('발행기관')
                            ->maxLength(255),
                            
                        Forms\Components\DatePicker::make('published_at')
                            ->label('발행일')
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->format('Y-m-d H:i:s')
                            ->default(now()),
                            
                        Forms\Components\TextInput::make('link')
                            ->label('논문 링크')
                            ->url()
                            ->maxLength(65535)
                            ->helperText('논문 원문 링크를 입력하세요'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('내용')
                    ->schema([
                        QuillEditor::make('content')
                            ->label('본문내용')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('paper-posts/content-images')
                            ->minHeight(400),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('이미지')
                            ->helperText('387x217 비율로 크롭됩니다')
                            ->image()
                            ->directory('paper-posts')
                            ->columnSpanFull()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '387:217',
                            ])
                            ->imageCropAspectRatio('387:217')
                            ->imageResizeTargetWidth(387)
                            ->imageResizeTargetHeight(217)
                            ->imageResizeMode('cover'),
                    ]),
                    
                Forms\Components\Hidden::make('is_published')
                    ->default(true),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('language')
                    ->label('언어')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'kor' => '한국어',
                        'eng' => 'English',
                        'chn' => '中文',
                        'hin' => 'हिन्दी',
                        'arb' => 'العربية',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'kor' => 'primary',
                        'eng' => 'success',
                        'chn' => 'warning',
                        'hin' => 'info',
                        'arb' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('authors')
                    ->label('저자')
                    ->badge()
                    ->separator(', ')
                    ->limit(3)
                    ->tooltip(function (Post $record): ?string {
                        if (!$record->authors || count($record->authors) <= 3) {
                            return null;
                        }
                        return implode(', ', $record->authors);
                    }),
                    
                Tables\Columns\TextColumn::make('publisher')
                    ->label('발행기관')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\ImageColumn::make('image')
                    ->label('이미지'),
                    
                Tables\Columns\TextColumn::make('published_at')
                    ->label('발행일')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_published')
                    ->label('게시')
                    ->boolean(),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('추천')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label('언어')
                    ->options([
                        'kor' => '한국어',
                        'eng' => 'English',
                        'chn' => '中文',
                        'hin' => 'हिन्दी',
                        'arb' => 'العربية',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('게시 상태'),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('추천 여부'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('게시')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('게시 취소')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => PaperPostResource\Pages\ListPaperPosts::route('/'),
            'create' => PaperPostResource\Pages\CreatePaperPost::route('/create'),
            'edit' => PaperPostResource\Pages\EditPaperPost::route('/{record}/edit'),
            'view' => PaperPostResource\Pages\ViewPaperPost::route('/{record}'),
        ];
    }
}