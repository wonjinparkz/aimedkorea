<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 메인 메뉴 생성
        $mainMenu = Menu::create([
            'name' => '메인 메뉴',
            'slug' => 'main-menu',
            'is_active' => true,
        ]);

        // 회사소개 메뉴 (메가메뉴 예시)
        $company = MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => '회사소개',
            'url' => '#',
            'order' => 1,
            'is_mega_menu' => true,
            'description' => 'AIMED KOREA의 비전과 사업 영역을 소개합니다.',
            'mega_menu_content' => [
                'columns' => [
                    [
                        'title' => '회사 정보',
                        'items' => [
                            ['title' => '회사 개요', 'url' => '/about/overview', 'description' => '회사의 역사와 비전'],
                            ['title' => 'CEO 인사말', 'url' => '/about/ceo', 'description' => 'CEO의 경영 철학'],
                            ['title' => '조직도', 'url' => '/about/organization', 'description' => '회사 조직 구조'],
                        ]
                    ],
                    [
                        'title' => '사업 영역',
                        'items' => [
                            ['title' => '의료 AI', 'url' => '/business/medical-ai', 'description' => 'AI 기반 의료 진단'],
                            ['title' => '헬스케어', 'url' => '/business/healthcare', 'description' => '디지털 헬스케어 솔루션'],
                            ['title' => '연구 개발', 'url' => '/business/rnd', 'description' => 'R&D 현황'],
                        ]
                    ],
                    [
                        'title' => '위치 & 연락처',
                        'items' => [
                            ['title' => '오시는 길', 'url' => '/contact/location', 'description' => '본사 위치 안내'],
                            ['title' => '연락처', 'url' => '/contact', 'description' => '문의하기'],
                        ]
                    ]
                ]
            ]
        ]);

        // 제품/서비스 메뉴
        $products = MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => '제품/서비스',
            'url' => '#',
            'order' => 2,
            'is_mega_menu' => true,
            'description' => '혁신적인 의료 AI 솔루션을 만나보세요.',
            'mega_menu_content' => [
                'columns' => [
                    [
                        'title' => 'AI 진단 시스템',
                        'items' => [
                            ['title' => 'X-ray AI', 'url' => '/products/xray-ai', 'description' => 'X-ray 영상 분석'],
                            ['title' => 'CT/MRI AI', 'url' => '/products/ct-mri-ai', 'description' => 'CT/MRI 영상 분석'],
                            ['title' => '병리 AI', 'url' => '/products/pathology-ai', 'description' => '병리 영상 분석'],
                        ]
                    ],
                    [
                        'title' => '헬스케어 플랫폼',
                        'items' => [
                            ['title' => '원격 진료', 'url' => '/products/telemedicine', 'description' => '비대면 진료 시스템'],
                            ['title' => '건강 관리', 'url' => '/products/health-management', 'description' => '개인 건강 관리'],
                            ['title' => '의료 데이터', 'url' => '/products/medical-data', 'description' => '의료 빅데이터 분석'],
                        ]
                    ]
                ]
            ]
        ]);

        // 뉴스/공지 메뉴 (일반 드롭다운)
        $news = MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => '뉴스/공지',
            'url' => '#',
            'order' => 3,
            'is_mega_menu' => false,
        ]);

        // 뉴스/공지 하위 메뉴
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'parent_id' => $news->id,
            'title' => '공지사항',
            'url' => '/news/notices',
            'order' => 1,
        ]);

        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'parent_id' => $news->id,
            'title' => '보도자료',
            'url' => '/news/press',
            'order' => 2,
        ]);

        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'parent_id' => $news->id,
            'title' => '이벤트',
            'url' => '/news/events',
            'order' => 3,
        ]);

        // 고객지원 메뉴 (일반 링크)
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => '고객지원',
            'url' => '/support',
            'order' => 4,
            'is_mega_menu' => false,
        ]);

        // 채용정보 메뉴 (일반 링크)
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => '채용정보',
            'url' => '/careers',
            'order' => 5,
            'is_mega_menu' => false,
            'target' => '_self',
        ]);
    }
}
