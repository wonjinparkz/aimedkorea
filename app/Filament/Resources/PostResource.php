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
use League\Csv\Writer;

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
                    Tables\Actions\BulkAction::make('export')
                        ->label('선택항목 CSV 내보내기')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            $csv = Writer::createFromString('');
                            
                            // Add BOM for Excel compatibility with Korean characters
                            $csv->setOutputBOM(Writer::BOM_UTF8);
                            
                            // Add headers - all columns with parsed JSON
                            $csv->insertOne([
                                'ID',
                                '제목',
                                'Slug',
                                'Base Slug',
                                '컨텐츠 그룹 ID',
                                '주 게시글',
                                '타입',
                                '언어',
                                '요약',
                                '더보기 텍스트',
                                '내용',
                                '섹션1 제목',
                                '섹션1 내용',
                                '섹션2 제목',
                                '섹션2 내용',
                                '섹션3 제목',
                                '섹션3 내용',
                                '관련기사1 ID',
                                '관련기사1 제목',
                                '관련기사2 ID',
                                '관련기사2 제목',
                                '관련기사3 ID',
                                '관련기사3 제목',
                                '중요 게시글',
                                '게시 여부',
                                '게시일',
                                '작성자 ID',
                                '작성자들',
                                '출판사',
                                '링크',
                                '이미지',
                                '비디오 타입',
                                'YouTube URL',
                                '비디오 파일',
                                '비디오 썸네일',
                                '비디오 길이',
                                '생성일',
                                '수정일',
                            ]);
                            
                            // Add data - all columns with parsed JSON
                            foreach ($records as $record) {
                                // Parse content_sections JSON
                                $sections = is_string($record->content_sections) 
                                    ? json_decode($record->content_sections, true) 
                                    : $record->content_sections;
                                
                                $section1_title = isset($sections[0]['title']) ? $sections[0]['title'] : '';
                                $section1_content = isset($sections[0]['content']) ? strip_tags($sections[0]['content']) : '';
                                $section2_title = isset($sections[1]['title']) ? $sections[1]['title'] : '';
                                $section2_content = isset($sections[1]['content']) ? strip_tags($sections[1]['content']) : '';
                                $section3_title = isset($sections[2]['title']) ? $sections[2]['title'] : '';
                                $section3_content = isset($sections[2]['content']) ? strip_tags($sections[2]['content']) : '';
                                
                                // Parse related_articles JSON
                                $related = is_string($record->related_articles) 
                                    ? json_decode($record->related_articles, true) 
                                    : $record->related_articles;
                                
                                $related1_id = isset($related[0]['id']) ? $related[0]['id'] : '';
                                $related1_title = isset($related[0]['title']) ? $related[0]['title'] : '';
                                $related2_id = isset($related[1]['id']) ? $related[1]['id'] : '';
                                $related2_title = isset($related[1]['title']) ? $related[1]['title'] : '';
                                $related3_id = isset($related[2]['id']) ? $related[2]['id'] : '';
                                $related3_title = isset($related[2]['title']) ? $related[2]['title'] : '';
                                
                                $csv->insertOne([
                                    $record->id,
                                    $record->title,
                                    $record->slug,
                                    $record->base_slug,
                                    $record->content_group_id,
                                    $record->is_primary ? '예' : '아니오',
                                    $record->type,
                                    $record->language,
                                    $record->summary,
                                    $record->read_more_text,
                                    strip_tags($record->content), // Remove HTML tags for cleaner CSV
                                    $section1_title,
                                    $section1_content,
                                    $section2_title,
                                    $section2_content,
                                    $section3_title,
                                    $section3_content,
                                    $related1_id,
                                    $related1_title,
                                    $related2_id,
                                    $related2_title,
                                    $related3_id,
                                    $related3_title,
                                    $record->is_featured ? '예' : '아니오',
                                    $record->is_published ? '예' : '아니오',
                                    $record->published_at ? $record->published_at->format('Y-m-d H:i:s') : '',
                                    $record->author_id,
                                    $record->authors,
                                    $record->publisher,
                                    $record->link,
                                    $record->image,
                                    $record->video_type,
                                    $record->youtube_url,
                                    $record->video_file,
                                    $record->video_thumbnail,
                                    $record->video_duration,
                                    $record->created_at->format('Y-m-d H:i:s'),
                                    $record->updated_at->format('Y-m-d H:i:s'),
                                ]);
                            }
                            
                            return response()->streamDownload(function () use ($csv) {
                                echo $csv->toString();
                            }, 'posts-' . date('Y-m-d-His') . '.csv', [
                                'Content-Type' => 'text/csv; charset=UTF-8',
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportAll')
                    ->label('전체 데이터 CSV 내보내기')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $records = static::getModel()::query()
                            ->where('type', static::$postType)
                            ->get();
                        
                        $csv = Writer::createFromString('');
                        
                        // Add BOM for Excel compatibility with Korean characters
                        $csv->setOutputBOM(Writer::BOM_UTF8);
                        
                        // Add headers - all columns with parsed JSON
                        $csv->insertOne([
                            'ID',
                            '제목',
                            'Slug',
                            'Base Slug',
                            '컨텐츠 그룹 ID',
                            '주 게시글',
                            '타입',
                            '언어',
                            '요약',
                            '더보기 텍스트',
                            '내용',
                            '섹션1 제목',
                            '섹션1 내용',
                            '섹션2 제목',
                            '섹션2 내용',
                            '섹션3 제목',
                            '섹션3 내용',
                            '관련기사1 ID',
                            '관련기사1 제목',
                            '관련기사2 ID',
                            '관련기사2 제목',
                            '관련기사3 ID',
                            '관련기사3 제목',
                            '중요 게시글',
                            '게시 여부',
                            '게시일',
                            '작성자 ID',
                            '작성자들',
                            '출판사',
                            '링크',
                            '이미지',
                            '비디오 타입',
                            'YouTube URL',
                            '비디오 파일',
                            '비디오 썸네일',
                            '비디오 길이',
                            '생성일',
                            '수정일',
                        ]);
                        
                        // Add data - all columns with parsed JSON
                        foreach ($records as $record) {
                            // Parse content_sections JSON
                            $sections = is_string($record->content_sections) 
                                ? json_decode($record->content_sections, true) 
                                : $record->content_sections;
                            
                            $section1_title = isset($sections[0]['title']) ? $sections[0]['title'] : '';
                            $section1_content = isset($sections[0]['content']) ? strip_tags($sections[0]['content']) : '';
                            $section2_title = isset($sections[1]['title']) ? $sections[1]['title'] : '';
                            $section2_content = isset($sections[1]['content']) ? strip_tags($sections[1]['content']) : '';
                            $section3_title = isset($sections[2]['title']) ? $sections[2]['title'] : '';
                            $section3_content = isset($sections[2]['content']) ? strip_tags($sections[2]['content']) : '';
                            
                            // Parse related_articles JSON
                            $related = is_string($record->related_articles) 
                                ? json_decode($record->related_articles, true) 
                                : $record->related_articles;
                            
                            $related1_id = isset($related[0]['id']) ? $related[0]['id'] : '';
                            $related1_title = isset($related[0]['title']) ? $related[0]['title'] : '';
                            $related2_id = isset($related[1]['id']) ? $related[1]['id'] : '';
                            $related2_title = isset($related[1]['title']) ? $related[1]['title'] : '';
                            $related3_id = isset($related[2]['id']) ? $related[2]['id'] : '';
                            $related3_title = isset($related[2]['title']) ? $related[2]['title'] : '';
                            
                            $csv->insertOne([
                                $record->id,
                                $record->title,
                                $record->slug,
                                $record->base_slug,
                                $record->content_group_id,
                                $record->is_primary ? '예' : '아니오',
                                $record->type,
                                $record->language,
                                $record->summary,
                                $record->read_more_text,
                                strip_tags($record->content), // Remove HTML tags for cleaner CSV
                                $section1_title,
                                $section1_content,
                                $section2_title,
                                $section2_content,
                                $section3_title,
                                $section3_content,
                                $related1_id,
                                $related1_title,
                                $related2_id,
                                $related2_title,
                                $related3_id,
                                $related3_title,
                                $record->is_featured ? '예' : '아니오',
                                $record->is_published ? '예' : '아니오',
                                $record->published_at ? $record->published_at->format('Y-m-d H:i:s') : '',
                                $record->author_id,
                                $record->authors,
                                $record->publisher,
                                $record->link,
                                $record->image,
                                $record->video_type,
                                $record->youtube_url,
                                $record->video_file,
                                $record->video_thumbnail,
                                $record->video_duration,
                                $record->created_at->format('Y-m-d H:i:s'),
                                $record->updated_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                        
                        return response()->streamDownload(function () use ($csv) {
                            echo $csv->toString();
                        }, static::$postType . '-' . date('Y-m-d-His') . '.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
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
