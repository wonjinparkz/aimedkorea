<?php

namespace App\Livewire;

use Livewire\Component;

class Footer extends Component
{
    public $siteTitle;
    public $siteTagline;
    public $siteEmail;
    public $sitePhone;
    public $siteAddress;
    public $footerLinks;
    public $footerSettings;
    
    public function mount()
    {
        // 프로젝트 옵션에서 설정 가져오기
        $this->siteTitle = get_option('site_title', 'AIMED KOREA');
        $this->siteTagline = get_option('site_tagline', '디지털 노화 가속 스위치, 당신의 건강을 무너뜨려 노화를 앞당기게 됩니다. 그 스위치, 이제 끌 수 있습니다.');
        $this->siteEmail = get_option('site_email', 'info@aimedkorea.com');
        $this->sitePhone = get_option('site_phone', '02-1234-5678');
        $this->siteAddress = get_option('site_address', '서울특별시 강남구 테헤란로 123 의료혁신빌딩 10층');
        $this->footerLinks = get_option('footer_links', [
            ['title' => '회사 소개', 'url' => '/about'],
            ['title' => '이용약관', 'url' => '/terms'],
            ['title' => '개인정보처리방침', 'url' => '/privacy'],
            ['title' => '고객센터', 'url' => '/support'],
            ['title' => '제휴제안', 'url' => '/partnership']
        ]);
        
        // 푸터 설정 가져오기
        $this->footerSettings = get_option('footer_settings', [
            'footer_description' => '',
            'feature_image' => '',
            'feature_image_url' => '',
            'footer_cards' => []
        ]);
        
        // 푸터 카드가 비어있으면 기본값 설정
        if (empty($this->footerSettings['footer_cards'])) {
            $this->footerSettings['footer_cards'] = [
                [
                    'icon' => 'heroicon-o-heart',
                    'title' => '건강관리',
                    'description' => '개인 맞춤형 건강관리 서비스를 제공합니다.',
                    'url' => '/health-care'
                ],
                [
                    'icon' => 'heroicon-o-chart-bar',
                    'title' => '데이터 분석',
                    'description' => 'AI 기반 건강 데이터 분석 서비스입니다.',
                    'url' => '/data-analysis'
                ],
                [
                    'icon' => 'heroicon-o-user-group',
                    'title' => '전문가 상담',
                    'description' => '의료 전문가와의 온라인 상담 서비스입니다.',
                    'url' => '/consultation'
                ],
                [
                    'icon' => 'heroicon-o-document-text',
                    'title' => '건강 기록',
                    'description' => '체계적인 건강 기록 관리 시스템입니다.',
                    'url' => '/health-records'
                ],
                [
                    'icon' => 'heroicon-o-bell',
                    'title' => '알림 서비스',
                    'description' => '건강 관리 알림 및 리마인더 서비스입니다.',
                    'url' => '/notifications'
                ],
                [
                    'icon' => 'heroicon-o-shield-check',
                    'title' => '보안',
                    'description' => '안전한 개인정보 보호 시스템입니다.',
                    'url' => '/security'
                ]
            ];
        }
    }
    
    public function render()
    {
        return view('livewire.footer');
    }
}
