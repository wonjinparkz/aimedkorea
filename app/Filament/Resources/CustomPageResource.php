<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomPageResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CustomPageResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = '맞춤형 페이지';
    
    protected static ?string $navigationGroup = '사이트';
    
    protected static ?int $navigationSort = 102;
    
    protected static ?string $modelLabel = '맞춤형 페이지';
    
    protected static ?string $pluralModelLabel = '맞춤형 페이지';
    
    protected static ?string $slug = 'custom-pages';

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
                
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('페이지 제목')
                            ->required()
                            ->maxLength(255)
                            ->helperText('이 제목이 페이지 배너에 표시됩니다')
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('slug')
                            ->default(fn () => Str::uuid()->toString()),
                        
                        Forms\Components\Hidden::make('type')
                            ->default('page'),
                        
                        Forms\Components\Hidden::make('url_preview')
                            ->default(''),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('배너 설정')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('배너 이미지')
                            ->image()
                            ->directory('pages/banners')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120)
                            ->helperText('업로드하지 않으면 기본 색상 배경이 사용됩니다'),
                    ]),
                
                Forms\Components\Section::make('페이지 내용')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('본문 내용')
                            ->required()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages/attachments')
                            ->fileAttachmentsVisibility('public'),
                    ]),
                
                // 공개 설정 - 숨김 처리
                Forms\Components\Hidden::make('is_published')
                    ->default(true),
                    
                Forms\Components\Hidden::make('published_at')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('id')
                    ->label('페이지 ID')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "#{$state}"),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('배너 이미지')
                    ->square(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('공개')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('공개일')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('수정일')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('공개 상태')
                    ->trueLabel('공개')
                    ->falseLabel('비공개')
                    ->placeholder('전체'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('미리보기')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Post $record): string => url("/page/{$record->id}"))
                    ->openUrlInNewTab()
                    ->color('info'),
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
        // For list view, show only Korean pages
        if (request()->routeIs('filament.admin.resources.*.index')) {
            return parent::getEloquentQuery()
                ->where('type', 'page')
                ->where('language', 'kor');
        }
        
        // For edit/view, allow all languages
        return parent::getEloquentQuery()
            ->where('type', 'page');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomPages::route('/'),
            'create' => Pages\CreateCustomPage::route('/create'),
            'edit' => Pages\EditCustomPage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', 'page')->count();
    }
}