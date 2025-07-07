<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateVideoPost extends CreatePost
{
    protected static string $resource = VideoPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Call parent method to handle language and slug generation
        $data = parent::mutateFormDataBeforeCreate($data);
        
        // Add video-specific data
        $data['type'] = \App\Models\Post::TYPE_VIDEO;
        $data['author_id'] = auth()->id();
        
        // 유튜브 비디오의 경우 썸네일 URL 자동 생성
        if ($data['video_type'] === 'youtube' && !empty($data['youtube_url'])) {
            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $data['youtube_url'], $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                $data['video_thumbnail'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }
        }
        
        return $data;
    }
}