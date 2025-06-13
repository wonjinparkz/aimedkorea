<?php

namespace App\Filament\Resources\TabPostResource\Pages;

use App\Filament\Resources\TabPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;

class EditTabPost extends EditPost
{
    protected static string $resource = TabPostResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // content_sections가 문자열인 경우 JSON으로 디코드
        if (isset($data['content_sections']) && is_string($data['content_sections'])) {
            $data['content_sections'] = json_decode($data['content_sections'], true);
        }

        // related_articles가 문자열인 경우 JSON으로 디코드
        if (isset($data['related_articles']) && is_string($data['related_articles'])) {
            $data['related_articles'] = json_decode($data['related_articles'], true);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // content_sections가 배열인지 확인하고 JSON으로 인코딩
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

        return $data;
    }
}
