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
            ];
        }
    }
    
    public function render()
    {
        return view('livewire.footer');
    }
}
