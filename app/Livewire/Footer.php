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
    }
    
    public function render()
    {
        return view('livewire.footer');
    }
}
