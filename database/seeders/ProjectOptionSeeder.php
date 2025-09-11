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
                    'footer_description_eng' => 'The digital aging acceleration switch undermines your health and accelerates aging. Now you can turn off that switch.',
                    'footer_description_chn' => '数字老化加速开关，破坏您的健康，加速衰老。现在，您可以关闭该开关。',
                    'footer_description_hin' => 'डिजिटल एजिंग एक्सेलेरेशन स्विच आपके स्वास्थ्य को कमजोर करता है और उम्र बढ़ने में तेजी लाता है। अब आप उस स्विच को बंद कर सकते हैं।',
                    'footer_description_arb' => 'مفتاح تسريع الشيخوخة الرقمية يقوض صحتك ويسرع الشيخوخة. الآن يمكنك إيقاف تشغيل هذا المفتاح.',
                    'feature_image' => '',
                    'feature_image_url' => '/about',
                    'footer_cards' => [
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
