<?php

namespace App\Filament\Resources;

use App\Forms\Components\QuillEditor;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QnaPostResource extends PostResource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationLabel = 'Q&A';

    protected static ?string $pluralModelLabel = 'Q&A';

    protected static ?string $modelLabel = 'Q&A';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = '콘텐츠';

    protected static ?string $postType = Post::TYPE_QNA;

    protected static ?int $navigationSort = 37;

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
                
                Forms\Components\Section::make('Q&A 내용')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('질문')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('type')
                            ->default(static::$postType),
                        
                        QuillEditor::make('content')
                            ->label('답변')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('qna-posts/content-images')
                            ->minHeight(400),
                        
                        Forms\Components\Textarea::make('summary')
                            ->label('요약')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('검색 결과나 목록에서 보여질 짧은 답변 요약'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('관련 이미지')
                            ->helperText('387x217 비율로 크롭됩니다')
                            ->image()
                            ->directory('posts/qna')
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
                    ->columnSpanFull(),
                
                Forms\Components\Section::make('게시 설정')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('자주 묻는 질문')
                            ->helperText('체크하면 자주 묻는 질문 섹션에 표시됩니다')
                            ->inline(false),
                        
                        Forms\Components\Toggle::make('is_published')
                            ->label('게시')
                            ->default(true)
                            ->helperText('체크 해제하면 임시저장 상태가 됩니다')
                            ->inline(false),
                        
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('게시 일시')
                            ->default(now())
                            ->displayFormat('Y-m-d H:i'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('질문')
                    ->searchable()
                    ->wrap()
                    ->formatStateUsing(fn (string $state): string => "Q. {$state}")
                    ->extraAttributes(['class' => 'font-semibold']),
                
                Tables\Columns\TextColumn::make('summary')
                    ->label('답변 요약')
                    ->wrap()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) {
                            return "A. {$state}";
                        }
                        $content = strip_tags($record->content);
                        return "A. " . Str::limit($content, 100);
                    })
                    ->extraAttributes(['class' => 'text-gray-600']),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('이미지'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('자주 묻는 질문')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('게시 상태')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('게시일')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('자주 묻는 질문'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('게시 상태')
                    ->placeholder('모두')
                    ->trueLabel('게시됨')
                    ->falseLabel('임시저장'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => QnaPostResource\Pages\ListQnaPosts::route('/'),
            'create' => QnaPostResource\Pages\CreateQnaPost::route('/create'),
            'edit' => QnaPostResource\Pages\EditQnaPost::route('/{record}/edit'),
        ];
    }
}