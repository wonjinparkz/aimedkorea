<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            Post::TYPE_FEATURED => [
                'title_prefix' => '특징',
                'content_template' => '이것은 특징 게시물입니다. 주요 기능과 특성을 소개하는 내용을 담고 있습니다.',
            ],
            Post::TYPE_ROUTINE => [
                'title_prefix' => '루틴',
                'content_template' => '일상적인 루틴과 관련된 내용입니다. 매일의 습관과 반복되는 활동에 대한 정보를 제공합니다.',
            ],
            Post::TYPE_BLOG => [
                'title_prefix' => '블로그',
                'content_template' => '블로그 형식의 자유로운 글입니다. 다양한 주제와 개인적인 생각을 공유합니다.',
            ],
            Post::TYPE_NEWS => [
                'title_prefix' => '뉴스',
                'content_template' => '최신 뉴스와 관련 기사입니다. 업계 동향과 중요한 소식을 전달합니다.',
            ],
            Post::TYPE_TAB => [
                'title_prefix' => '탭',
                'content_template' => '탭 형식으로 구성된 콘텐츠입니다. 카테고리별로 정리된 정보를 제공합니다.',
            ],
            Post::TYPE_BANNER => [
                'title_prefix' => '배너',
                'content_template' => '시각적으로 중요한 배너 콘텐츠입니다. 주목할 만한 이벤트나 공지사항을 담고 있습니다.',
            ],
        ];

        foreach ($types as $type => $config) {
            for ($i = 1; $i <= 7; $i++) {
                Post::create([
                    'title' => $config['title_prefix'] . ' 게시물 #' . $i,
                    'type' => $type,
                    'summary' => $config['title_prefix'] . ' 타입의 ' . $i . '번째 게시물 요약입니다. 이 게시물은 중요한 정보를 담고 있습니다.',
                    'read_more_text' => '더 알아보기',
                    'content' => '<h2>' . $config['title_prefix'] . ' ' . $i . '</h2>' .
                               '<p>' . $config['content_template'] . '</p>' .
                               '<p>이 게시물은 <strong>' . date('Y년 m월 d일') . '</strong>에 작성된 ' . $i . '번째 게시물입니다.</p>' .
                               '<ul>' .
                               '<li>첫 번째 중요한 포인트</li>' .
                               '<li>두 번째 핵심 내용</li>' .
                               '<li>세 번째 주요 특징</li>' .
                               '</ul>' .
                               '<p>더 자세한 내용은 계속해서 업데이트될 예정입니다.</p>',
                    'featured' => $i <= 3 ? true : false, // 각 타입의 처음 3개는 featured로 설정
                    'image' => null, // 실제 이미지는 나중에 추가 가능
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ]);
            }
        }

        $this->command->info('총 ' . (count($types) * 7) . '개의 게시물이 생성되었습니다!');
    }
}
