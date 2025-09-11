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
    public $currentLanguage;
    
    public function mount()
    {
        // Get current language
        $this->currentLanguage = session('locale', 'kor');
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
            'footer_description_eng' => '',
            'footer_description_chn' => '',
            'footer_description_hin' => '',
            'footer_description_arb' => '',
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
                    'title_eng' => 'Science-Based Recovery Solutions',
                    'title_chn' => '科学制造的恢复解决方案',
                    'title_hin' => 'विज्ञान आधारित रिकवरी समाधान',
                    'title_arb' => 'حلول التعافي القائمة على العلم',
                    'description' => '임상과 논문으로 검증된 AI 기반 회복 기술',
                    'description_eng' => 'AI-based recovery technology verified by clinical trials and research',
                    'description_chn' => '经过临床和论文验证的基于AI的恢复技术',
                    'description_hin' => 'नैदानिक परीक्षणों और अनुसंधान द्वारा सत्यापित AI-आधारित रिकवरी तकनीक',
                    'description_arb' => 'تقنية التعافي القائمة على الذكاء الاصطناعي المعتمدة من التجارب السريرية والأبحاث',
                    'url' => '/recovery-solutions'
                ],
                [
                    'icon' => 'heroicon-o-star',
                    'title' => '추천하는 제품/서비스',
                    'title_eng' => 'Recommended Products/Services',
                    'title_chn' => '推荐产品/服务',
                    'title_hin' => 'अनुशंसित उत्पाद/सेवाएं',
                    'title_arb' => 'المنتجات/الخدمات الموصى بها',
                    'description' => '눈•뇌•수면 회복에 도움되는 루카의 추천 템',
                    'description_eng' => 'Luca\'s recommendations for eye, brain, and sleep recovery',
                    'description_chn' => '有助于眼睛·大脑·睡眠恢复的Luca推荐品',
                    'description_hin' => 'आंख, मस्तिष्क और नींद की रिकवरी के लिए लुका की सिफारिशें',
                    'description_arb' => 'توصيات لوكا لتعافي العين والدماغ والنوم',
                    'url' => '/recommendations'
                ],
                [
                    'icon' => 'heroicon-o-newspaper',
                    'title' => '디지털 노화 뉴스룸/지식 브리프',
                    'title_eng' => 'Digital Aging Newsroom/Knowledge Brief',
                    'title_chn' => '数字老化新闻室/知识简报',
                    'title_hin' => 'डिजिटल एजिंग न्यूजरूम/ज्ञान ब्रीफ',
                    'title_arb' => 'غرفة أخبار الشيخوخة الرقمية/موجز المعرفة',
                    'description' => '최신 과학 뉴스와 뇌•눈•수면 콘텐츠 정리',
                    'description_eng' => 'Latest science news and brain, eye, sleep content summary',
                    'description_chn' => '最新科学新闻和大脑·眼睛·睡眠内容整理',
                    'description_hin' => 'नवीनतम विज्ञान समाचार और मस्तिष्क, आंख, नींद सामग्री सारांश',
                    'description_arb' => 'أحدث الأخبار العلمية وملخص محتوى الدماغ والعين والنوم',
                    'url' => '/newsroom'
                ],
                [
                    'icon' => 'heroicon-o-calendar',
                    'title' => 'NR3 루틴 무료 서비스',
                    'title_eng' => 'NR3 Routine Free Service',
                    'title_chn' => 'NR3日常免费服务',
                    'title_hin' => 'NR3 रूटीन मुफ्त सेवा',
                    'title_arb' => 'خدمة NR3 الروتينية المجانية',
                    'description' => '디지털 자가진단-> 맞춤 루틴 코칭 시작하기',
                    'description_eng' => 'Digital self-diagnosis -> Start personalized routine coaching',
                    'description_chn' => '数字自我诊断->开始定制日常指导',
                    'description_hin' => 'डिजिटल स्व-निदान -> व्यक्तिगत दिनचर्या कोचिंग शुरू करें',
                    'description_arb' => 'التشخيص الذاتي الرقمي -> ابدأ التدريب الروتيني المخصص',
                    'url' => '/nr3-routine'
                ],
                [
                    'icon' => 'heroicon-o-heart',
                    'title' => '루틴실천 회복 스토리',
                    'title_eng' => 'Routine Practice Recovery Stories',
                    'title_chn' => '日常实践恢复故事',
                    'title_hin' => 'दिनचर्या अभ्यास रिकवरी कहानियां',
                    'title_arb' => 'قصص التعافي من ممارسة الروتين',
                    'description' => '회복 전후 변화 사례와 사용자 경험 공유',
                    'description_eng' => 'Sharing recovery before/after cases and user experiences',
                    'description_chn' => '分享恢复前后变化案例和用户经验',
                    'description_hin' => 'रिकवरी से पहले/बाद के मामले और उपयोगकर्ता अनुभव साझा करना',
                    'description_arb' => 'مشاركة حالات ما قبل/بعد التعافي وتجارب المستخدمين',
                    'url' => '/recovery-stories'
                ],
                [
                    'icon' => 'heroicon-o-user-group',
                    'title' => '우리가 함께하는 사람들',
                    'title_eng' => 'Our Partners',
                    'title_chn' => '我们的合作伙伴',
                    'title_hin' => 'हमारे साथी',
                    'title_arb' => 'شركاؤنا',
                    'description' => '전문가, 기관, 글로벌 파트너들의 소식',
                    'description_eng' => 'News from experts, institutions, and global partners',
                    'description_chn' => '专家、机构、全球合作伙伴的消息',
                    'description_hin' => 'विशेषज्ञों, संस्थाओं और वैश्विक भागीदारों की खबरें',
                    'description_arb' => 'أخبار من الخبراء والمؤسسات والشركاء العالميين',
                    'url' => '/partners'
                ]
            ];
        }
    }
    
    public function getLocalizedDescription()
    {
        $languageField = match($this->currentLanguage) {
            'eng' => 'footer_description_eng',
            'chn' => 'footer_description_chn',
            'hin' => 'footer_description_hin',
            'arb' => 'footer_description_arb',
            default => 'footer_description'
        };
        
        return $this->footerSettings[$languageField] ?? $this->footerSettings['footer_description'] ?? '';
    }
    
    public function getLocalizedCardTitle($card)
    {
        $languageField = match($this->currentLanguage) {
            'eng' => 'title_eng',
            'chn' => 'title_chn',
            'hin' => 'title_hin',
            'arb' => 'title_arb',
            default => 'title'
        };
        
        return $card[$languageField] ?? $card['title'] ?? '';
    }
    
    public function getLocalizedCardDescription($card)
    {
        $languageField = match($this->currentLanguage) {
            'eng' => 'description_eng',
            'chn' => 'description_chn',
            'hin' => 'description_hin',
            'arb' => 'description_arb',
            default => 'description'
        };
        
        return $card[$languageField] ?? $card['description'] ?? '';
    }
    
    public function render()
    {
        return view('livewire.footer');
    }
}
