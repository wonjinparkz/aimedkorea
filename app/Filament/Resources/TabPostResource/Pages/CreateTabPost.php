<?php

namespace App\Filament\Resources\TabPostResource\Pages;

use App\Filament\Resources\TabPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateTabPost extends CreatePost
{
    protected static string $resource = TabPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // content_sections가 배열인지 확인하고 구조화
        if (isset($data['content_sections']) && is_array($data['content_sections'])) {
            // 각 섹션이 비어있지 않은지 확인
            $sections = [
                'overview' => $data['content_sections']['overview'] ?? '',
                'our_vision' => $data['content_sections']['our_vision'] ?? '',
                'research_topics' => $data['content_sections']['research_topics'] ?? '',
                'principles_for_ai_ethics' => $data['content_sections']['principles_for_ai_ethics'] ?? '',
            ];
            $data['content_sections'] = $sections;
        }

        // related_articles가 비어있으면 빈 배열로 설정
        if (!isset($data['related_articles'])) {
            $data['related_articles'] = [];
        }

        return $data;
    }
}
