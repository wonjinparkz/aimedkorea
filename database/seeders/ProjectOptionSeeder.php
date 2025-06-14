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
