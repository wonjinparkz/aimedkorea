<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;

class EditVideoPost extends EditPost
{
    protected static string $resource = VideoPostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Call parent method to handle language and slug generation
        $data = parent::mutateFormDataBeforeSave($data);
        
        // 유튜브 비디오의 경우 썸네일 URL 자동 생성
        if ($data['video_type'] === 'youtube' && !empty($data['youtube_url'])) {
            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $data['youtube_url'], $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                $data['video_thumbnail'] = "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
            }
        } elseif ($data['video_type'] === 'upload') {
            // 업로드 타입으로 변경 시 유튜브 썸네일 제거
            if (isset($data['video_thumbnail']) && str_contains($data['video_thumbnail'], 'youtube.com')) {
                $data['video_thumbnail'] = null;
            }
        }
        
        return $data;
    }
}