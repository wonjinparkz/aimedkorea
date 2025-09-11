<?php

namespace App\Filament\Resources\FooterMenuResource\Pages;

use App\Filament\Resources\FooterMenuResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Filament\Forms\Form;

class ManageFooterMenus extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = FooterMenuResource::class;

    protected static string $view = 'filament.resources.footer-menu-resource.pages.manage-footer-menus';
    
    protected static ?string $title = '푸터 메뉴 편집';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        // project_options에서 푸터 메뉴 데이터 가져오기
        $footerData = get_option('footer_settings', [
            'footer_description' => '',
            'footer_description_eng' => '',
            'footer_description_chn' => '',
            'footer_description_hin' => '',
            'footer_description_arb' => '',
            'feature_image' => '',
            'feature_image_url' => '',
            'footer_cards' => []
        ]);
        
        // 기본 카드 데이터가 없으면 생성
        if (empty($footerData['footer_cards'])) {
            $footerData['footer_cards'] = $this->getDefaultCards();
        }
        
        $this->form->fill($footerData);
    }
    
    private function getDefaultCards(): array
    {
        return [
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
    
    public function form(Form $form): Form
    {
        return FooterMenuResource::form($form)->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        // project_options에 저장
        update_option('footer_settings', $data);
        
        // 성공 메시지
        Notification::make()
            ->title('푸터 설정이 성공적으로 업데이트되었습니다!')
            ->success()
            ->duration(5000)
            ->send();
            
        // 페이지 새로고침하여 최신 데이터 표시
        $this->redirect(static::getUrl());
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('설정 저장')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check')
                ->size('lg')
                ->extraAttributes([
                    'class' => 'text-lg'
                ]),
        ];
    }
    
    public function getTitle(): string
    {
        return '푸터 메뉴 편집';
    }
    
    public function getHeading(): string
    {
        return '푸터 메뉴 관리';
    }
    
    public function getSubheading(): ?string
    {
        return '웹사이트 하단 푸터를 편집합니다. 변경사항은 저장 버튼을 클릭해야 적용됩니다.';
    }
}
