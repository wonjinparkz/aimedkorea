<?php

namespace App\Filament\Resources;

use App\Forms\Components\QuillEditor;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VideoPostResource extends PostResource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationLabel = '영상 미디어';

    protected static ?string $pluralModelLabel = '영상 미디어';

    protected static ?string $modelLabel = '영상';

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = '미디어';

    protected static ?string $postType = Post::TYPE_VIDEO;

    protected static ?int $navigationSort = 80;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Language selector section
                Forms\Components\Section::make('언어 설정')
                    ->schema([
                        Forms\Components\Select::make('language')
                            ->label('언어')
                            ->options([
                                'kor' => '한국어',
                                'eng' => 'English',
                                'chn' => '中文',
                                'hin' => 'हिन्दी',
                                'arb' => 'العربية'
                            ])
                            ->default('kor')
                            ->required()
                            ->helperText('게시물의 언어를 선택하세요')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $baseSlug = $get('base_slug');
                                if ($baseSlug) {
                                    $set('slug', $baseSlug . '-' . $state);
                                }
                            }),
                        
                        Forms\Components\TextInput::make('base_slug')
                            ->label('기본 슬러그')
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $language = $get('language');
                                if ($language) {
                                    $set('slug', $state . '-' . $language);
                                }
                            })
                            ->helperText('언어별 버전을 그룹화하는 기본 슬러그입니다'),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('슬러그')
                            ->disabled()
                            ->maxLength(255)
                            ->helperText('URL에 사용되는 고유 식별자 (자동 생성)'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('제목')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('type')
                            ->default(static::$postType),
                        
                        Forms\Components\Textarea::make('summary')
                            ->label('요약 설명')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('영상에 대한 간단한 설명을 입력하세요'),
                        
                        QuillEditor::make('content')
                            ->label('상세 설명')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('video-posts/content-images')
                            ->minHeight(300),
                    ])
                    ->columnSpanFull(),
                
                Forms\Components\Section::make('영상 설정')
                    ->schema([
                        Forms\Components\Radio::make('video_type')
                            ->label('영상 타입')
                            ->options([
                                'youtube' => '유튜브 링크',
                                'upload' => '직접 업로드',
                            ])
                            ->default('youtube')
                            ->reactive()
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('youtube_url')
                            ->label('유튜브 URL')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('유튜브 영상의 전체 URL을 입력하세요')
                            ->visible(fn (Forms\Get $get): bool => $get('video_type') === 'youtube')
                            ->required(fn (Forms\Get $get): bool => $get('video_type') === 'youtube')
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('video_file')
                            ->label('영상 파일')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/mpeg', 'video/quicktime'])
                            ->maxSize(512000) // 500MB
                            ->directory('posts/videos')
                            ->visible(fn (Forms\Get $get): bool => $get('video_type') === 'upload')
                            ->required(fn (Forms\Get $get): bool => $get('video_type') === 'upload')
                            ->columnSpanFull()
                            ->helperText('최대 500MB, MP4/WebM/OGG 형식 지원'),
                        
                        Forms\Components\TextInput::make('youtube_thumbnail_url')
                            ->label('유튜브 썸네일 URL')
                            ->hidden()
                            ->dehydrated(false),
                        
                        Forms\Components\FileUpload::make('video_thumbnail')
                            ->label('썸네일 이미지')
                            ->helperText('387x217 비율로 크롭됩니다')
                            ->image()
                            ->directory('posts/video-thumbnails')
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get): bool => $get('video_type') !== 'youtube')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '387:217',
                            ])
                            ->imageCropAspectRatio('387:217')
                            ->imageResizeTargetWidth(387)
                            ->imageResizeTargetHeight(217)
                            ->imageResizeMode('cover'),
                        
                        Forms\Components\TextInput::make('video_duration')
                            ->label('영상 길이 (초)')
                            ->numeric()
                            ->placeholder('180')
                            ->helperText('영상의 총 재생 시간을 초 단위로 입력 (예: 3분 = 180초)'),
                    ])
                    ->columnSpanFull(),
                
                Forms\Components\Section::make('게시 설정')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('추천 영상')
                            ->helperText('메인 페이지 상단에 표시됩니다')
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
                Tables\Columns\ImageColumn::make('video_thumbnail')
                    ->label('썸네일')
                    ->width(120)
                    ->height(80)
                    ->getStateUsing(function ($record) {
                        // 저장된 썸네일이 있으면 사용
                        if ($record->video_thumbnail) {
                            // 유튜브 썸네일 URL이면 그대로 반환
                            if (str_contains($record->video_thumbnail, 'youtube.com')) {
                                return $record->video_thumbnail;
                            }
                            // 업로드된 파일이면 Storage URL 반환
                            return $record->video_thumbnail;
                        }
                        
                        // 없으면 유튜브 URL에서 추출
                        if ($record->youtube_url && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $record->youtube_url, $matches)) {
                            return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
                        }
                        
                        return null;
                    }),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),
                
                Tables\Columns\BadgeColumn::make('language')
                    ->label('언어')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'kor' => '한국어',
                        'eng' => 'English',
                        'chn' => '中文',
                        'hin' => 'हिन्दी',
                        'arb' => 'العربية',
                        default => $state
                    })
                    ->colors([
                        'primary' => 'kor',
                        'success' => 'eng',
                        'warning' => 'chn',
                        'danger' => 'hin',
                        'secondary' => 'arb',
                    ]),
                
                Tables\Columns\BadgeColumn::make('video_type')
                    ->label('타입')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'youtube' => '유튜브',
                        'upload' => '업로드',
                        default => '-'
                    })
                    ->colors([
                        'danger' => 'youtube',
                        'success' => 'upload',
                    ]),
                
                Tables\Columns\TextColumn::make('video_duration')
                    ->label('길이')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $minutes = floor($state / 60);
                        $seconds = $state % 60;
                        return sprintf('%02d:%02d', $minutes, $seconds);
                    })
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('추천')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('게시 상태')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('게시일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                
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
                        'arb' => 'العربية'
                    ]),
                Tables\Filters\SelectFilter::make('video_type')
                    ->label('영상 타입')
                    ->options([
                        'youtube' => '유튜브',
                        'upload' => '업로드',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('추천 영상'),
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
            'index' => VideoPostResource\Pages\ListVideoPosts::route('/'),
            'create' => VideoPostResource\Pages\CreateVideoPost::route('/create'),
            'edit' => VideoPostResource\Pages\EditVideoPost::route('/{record}/edit'),
        ];
    }

}