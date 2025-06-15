<?php

namespace Database\Seeders;

use App\Models\ProjectOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            // 사이트 기본 설정
            [
                'option_name' => 'site_title',
                'option_value' => 'AIMED KOREA',
                'autoload' => 'yes'
            ],
            
            // 헤더 메뉴 설정
            [
                'option_name' => 'header_menu',
                'option_value' => json_encode([
                    [
                        'label' => '회사소개',
                        'url' => '/about',
                        'type' => 'dropdown',
                        'children' => [
                            ['label' => 'CEO 인사말', 'url' => '/about/greeting'],
                            ['label' => '비전 & 미션', 'url' => '/about/vision'],
                            ['label' => '연혁', 'url' => '/about/history'],
                            ['label' => '조직도', 'url' => '/about/organization']
                        ]
                    ],
                    [
                        'label' => '제품 & 서비스',
                        'url' => '',
                        'type' => 'mega',
                        'groups' => [
                            [
                                'group_label' => '의료 AI 솔루션',
                                'items' => [
                                    ['label' => 'AI 진단 시스템', 'url' => '/products/ai-diagnosis'],
                                    ['label' => '의료 영상 분석', 'url' => '/products/image-analysis'],
                                    ['label' => '예측 모델링', 'url' => '/products/predictive-modeling']
                                ]
                            ],
                            [
                                'group_label' => '헬스케어 플랫폼',
                                'items' => [
                                    ['label' => '원격 진료 시스템', 'url' => '/products/telemedicine'],
                                    ['label' => '병원 정보 시스템', 'url' => '/products/his'],
                                    ['label' => '환자 관리 앱', 'url' => '/products/patient-app']
                                ]
                            ]
                        ]
                    ],
                    [
                        'label' => '연구개발',
                        'url' => '/research',
                        'type' => 'link'
                    ],
                    [
                        'label' => '뉴스',
                        'url' => '/news',
                        'type' => 'dropdown',
                        'children' => [
                            ['label' => '공지사항', 'url' => '/news/notice'],
                            ['label' => '보도자료', 'url' => '/news/press'],
                            ['label' => '이벤트', 'url' => '/news/events']
                        ]
                    ],
                    [
                        'label' => '고객센터',
                        'url' => '/support',
                        'type' => 'dropdown',
                        'children' => [
                            ['label' => '자주 묻는 질문', 'url' => '/support/faq'],
                            ['label' => '문의하기', 'url' => '/support/contact'],
                            ['label' => '다운로드', 'url' => '/support/downloads']
                        ]
                    ]
                ]),
                'autoload' => 'yes'
            ],
            [
                'option_name' => 'site_tagline',
                'option_value' => '디지털 노화 가속 스위치, 당신의 건강을 무너뜨려 노화를 앞당기게 됩니다. 그 스위치, 이제 끌 수 있습니다.',
                'autoload' => 'yes'
            ],
            [
                'option_name' => 'site_email',
                'option_value' => 'info@aimedkorea.com',
                'autoload' => 'yes'
            ],
            [
                'option_name' => 'site_phone',
                'option_value' => '02-1234-5678',
                'autoload' => 'yes'
            ],
            [
                'option_name' => 'site_address',
                'option_value' => '서울특별시 강남구 테헤란로 123 의료혁신빌딩 10층',
                'autoload' => 'yes'
            ],
            
            // 소셜 미디어 링크
            [
                'option_name' => 'social_links',
                'option_value' => json_encode([
                    'facebook' => 'https://facebook.com/aimedkorea',
                    'twitter' => 'https://twitter.com/aimedkorea',
                    'instagram' => 'https://instagram.com/aimedkorea',
                    'linkedin' => 'https://linkedin.com/company/aimedkorea',
                    'youtube' => ''
                ]),
                'autoload' => 'yes'
            ],
            
            // 홈페이지 설정
            [
                'option_name' => 'homepage_settings',
                'option_value' => json_encode([
                    'show_hero_slider' => true,
                    'show_featured_post' => true,
                    'routines_per_page' => 6,
                    'blogs_per_page' => 6,
                    'show_research_areas' => true
                ]),
                'autoload' => 'yes'
            ],
            
            // 푸터 설정
            [
                'option_name' => 'footer_links',
                'option_value' => json_encode([
                    ['title' => '회사 소개', 'url' => '/about'],
                    ['title' => '이용약관', 'url' => '/terms'],
                    ['title' => '개인정보처리방침', 'url' => '/privacy'],
                    ['title' => '고객센터', 'url' => '/support'],
                    ['title' => '제휴제안', 'url' => '/partnership']
                ]),
                'autoload' => 'yes'
            ],
            
            // 메타 태그 설정
            [
                'option_name' => 'meta_settings',
                'option_value' => json_encode([
                    'meta_description' => 'AIMED KOREA - AI 기반 의료 혁신 솔루션',
                    'meta_keywords' => '의료 AI, 헬스케어, 디지털 헬스, 원격진료, 의료 데이터',
                    'og_image' => '',
                    'twitter_card' => 'summary_large_image'
                ]),
                'autoload' => 'yes'
            ],
            
            // 업무 시간
            [
                'option_name' => 'business_hours',
                'option_value' => json_encode([
                    'monday' => '09:00 - 18:00',
                    'tuesday' => '09:00 - 18:00',
                    'wednesday' => '09:00 - 18:00',
                    'thursday' => '09:00 - 18:00',
                    'friday' => '09:00 - 18:00',
                    'saturday' => '휴무',
                    'sunday' => '휴무'
                ]),
                'autoload' => 'no'
            ],
            
            // 기능 토글
            [
                'option_name' => 'feature_toggles',
                'option_value' => json_encode([
                    'enable_newsletter' => true,
                    'enable_search' => true,
                    'enable_comments' => false,
                    'enable_registration' => true,
                    'maintenance_mode' => false
                ]),
                'autoload' => 'yes'
            ],
            
            // 푸터 설정
            [
                'option_name' => 'footer_settings',
                'option_value' => json_encode([
                    'footer_description' => '디지털 노화 가속 스위치, 당신의 건강을 무너뜨려 노화를 앞당기게 됩니다. 그 스위치, 이제 끌 수 있습니다.',
                    'feature_image' => '',
                    'feature_image_url' => '/about',
                    'footer_cards' => [
                        [
                            'icon' => 'heroicon-o-beaker',
                            'title' => '과학이 만든 회복 솔루션',
                            'description' => '임상과 논문으로 검증된 AI 기반 회복 기술',
                            'url' => '/recovery-solutions'
                        ],
                        [
                            'icon' => 'heroicon-o-star',
                            'title' => '추천하는 제품/서비스',
                            'description' => '눈•뇌•수면 회복에 도움되는 루카의 추천 템',
                            'url' => '/recommendations'
                        ],
                        [
                            'icon' => 'heroicon-o-newspaper',
                            'title' => '디지털 노화 뉴스룸/지식 브리프',
                            'description' => '최신 과학 뉴스와 뇌•눈•수면 콘텐츠 정리',
                            'url' => '/newsroom'
                        ],
                        [
                            'icon' => 'heroicon-o-calendar',
                            'title' => 'NR3 루틴 무료 서비스',
                            'description' => '디지털 자가진단-> 맞춤 루틴 코칭 시작하기',
                            'url' => '/nr3-routine'
                        ],
                        [
                            'icon' => 'heroicon-o-heart',
                            'title' => '루틴실천 회복 스토리',
                            'description' => '회복 전후 변화 사례와 사용자 경험 공유',
                            'url' => '/recovery-stories'
                        ],
                        [
                            'icon' => 'heroicon-o-user-group',
                            'title' => '우리가 함께하는 사람들',
                            'description' => '전문가, 기관, 글로벌 파트너들의 소식',
                            'url' => '/partners'
                        ]
                    ]
                ]),
                'autoload' => 'yes'
            ],
        ];

        foreach ($options as $option) {
            ProjectOption::updateOrCreate(
                ['option_name' => $option['option_name']],
                [
                    'option_value' => $option['option_value'],
                    'autoload' => $option['autoload']
                ]
            );
        }
    }
}
