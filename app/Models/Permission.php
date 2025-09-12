<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'module',
        'action',
        'display_name_ko',
        'display_name_en',
        'description',
    ];

    // 역할과의 관계
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    // 현재 언어에 따른 표시 이름 반환
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        $field = "display_name_{$locale}";
        
        return $this->$field ?? $this->display_name_en ?? $this->name;
    }

    // 모듈별 권한 그룹화
    public static function groupedByModule()
    {
        return static::all()->groupBy('module');
    }

    // 계층형 권한 구조 (실제 메뉴와 동일)
    public static function getPermissionHierarchy(): array
    {
        return [
            '1' => [
                'name' => '대시보드',
                'section_permission' => 'section_dashboard-view',
                'children' => [
                    '1.1' => [
                        'name' => '대시보드',
                        'permissions' => ['dashboard-view']
                    ]
                ]
            ],
            '2' => [
                'name' => '홈 구성',
                'section_permission' => 'section_home-view',
                'children' => [
                    '2.1' => [
                        'name' => 'Heroes',
                        'permissions' => ['heroes-view', 'heroes-create', 'heroes-edit', 'heroes-delete']
                    ],
                    '2.2' => [
                        'name' => 'Featured Posts',
                        'permissions' => ['featured_posts-view', 'featured_posts-create', 'featured_posts-edit', 'featured_posts-delete']
                    ],
                    '2.3' => [
                        'name' => 'Banner Posts',
                        'permissions' => ['banner_posts-view', 'banner_posts-create', 'banner_posts-edit', 'banner_posts-delete']
                    ]
                ]
            ],
            '3' => [
                'name' => '콘텐츠',
                'section_permission' => 'section_content-view',
                'children' => [
                    '3.1' => [
                        'name' => 'Blog Posts',
                        'permissions' => ['blog_posts-view', 'blog_posts-create', 'blog_posts-edit', 'blog_posts-delete']
                    ],
                    '3.2' => [
                        'name' => 'Food Posts',
                        'permissions' => ['food_posts-view', 'food_posts-create', 'food_posts-edit', 'food_posts-delete']
                    ],
                    '3.3' => [
                        'name' => 'News Posts',
                        'permissions' => ['news_posts-view', 'news_posts-create', 'news_posts-edit', 'news_posts-delete']
                    ],
                    '3.4' => [
                        'name' => 'Product Posts',
                        'permissions' => ['product_posts-view', 'product_posts-create', 'product_posts-edit', 'product_posts-delete']
                    ],
                    '3.5' => [
                        'name' => 'QnA Posts',
                        'permissions' => ['qna_posts-view', 'qna_posts-create', 'qna_posts-edit', 'qna_posts-delete']
                    ],
                    '3.6' => [
                        'name' => 'Service Posts',
                        'permissions' => ['service_posts-view', 'service_posts-create', 'service_posts-edit', 'service_posts-delete']
                    ]
                ]
            ],
            '4' => [
                'name' => '리서치 허브',
                'section_permission' => 'section_research-view',
                'children' => [
                    '4.1' => [
                        'name' => 'Paper Posts',
                        'permissions' => ['paper_posts-view', 'paper_posts-create', 'paper_posts-edit', 'paper_posts-delete']
                    ],
                    '4.2' => [
                        'name' => 'Tab Posts',
                        'permissions' => ['tab_posts-view', 'tab_posts-create', 'tab_posts-edit', 'tab_posts-delete']
                    ]
                ]
            ],
            '5' => [
                'name' => '루틴',
                'section_permission' => 'section_routine-view',
                'children' => [
                    '5.1' => [
                        'name' => 'Routine Posts',
                        'permissions' => ['routine_posts-view', 'routine_posts-create', 'routine_posts-edit', 'routine_posts-delete']
                    ]
                ]
            ],
            '6' => [
                'name' => '파트너',
                'section_permission' => 'section_partner-view',
                'children' => [
                    '6.1' => [
                        'name' => 'Partners',
                        'permissions' => ['partners-view', 'partners-create', 'partners-edit', 'partners-delete']
                    ]
                ]
            ],
            '7' => [
                'name' => '설문',
                'section_permission' => 'section_survey-view',
                'children' => [
                    '7.1' => [
                        'name' => 'Surveys',
                        'permissions' => ['surveys-view', 'surveys-create', 'surveys-edit', 'surveys-delete', 'surveys-analyze']
                    ],
                    '7.2' => [
                        'name' => 'Survey Responses',
                        'permissions' => ['survey_responses-view', 'survey_responses-create', 'survey_responses-edit', 'survey_responses-delete']
                    ]
                ]
            ],
            '8' => [
                'name' => '미디어',
                'section_permission' => 'section_media-view',
                'children' => [
                    '8.1' => [
                        'name' => 'Video Posts',
                        'permissions' => ['video_posts-view', 'video_posts-create', 'video_posts-edit', 'video_posts-delete']
                    ]
                ]
            ],
            '9' => [
                'name' => '마케팅',
                'section_permission' => 'section_marketing-view',
                'children' => [
                    '9.1' => [
                        'name' => 'Promotion Posts',
                        'permissions' => ['promotion_posts-view', 'promotion_posts-create', 'promotion_posts-edit', 'promotion_posts-delete']
                    ]
                ]
            ],
            '10' => [
                'name' => '사이트',
                'section_permission' => 'section_site-view',
                'children' => [
                    '10.1' => [
                        'name' => 'Custom Pages',
                        'permissions' => ['custom_pages-view', 'custom_pages-create', 'custom_pages-edit', 'custom_pages-delete']
                    ],
                    '10.2' => [
                        'name' => 'Footer Menus',
                        'permissions' => ['footer_menus-view', 'footer_menus-create', 'footer_menus-edit', 'footer_menus-delete']
                    ],
                    '10.3' => [
                        'name' => 'Header Menus',
                        'permissions' => ['header_menus-view', 'header_menus-create', 'header_menus-edit', 'header_menus-delete']
                    ]
                ]
            ],
            '11' => [
                'name' => '설정',
                'section_permission' => 'section_settings-view',
                'children' => [
                    '11.1' => [
                        'name' => '사용자 관리',
                        'permissions' => ['users-view', 'users-create', 'users-edit', 'users-delete']
                    ],
                    '11.2' => [
                        'name' => '역할 관리',
                        'permissions' => ['roles-view', 'roles-create', 'roles-edit', 'roles-delete']
                    ]
                ]
            ]
        ];
    }

    // 기본 권한 목록 (실제 리소스와 일치)
    public static function getDefaultPermissions(): array
    {
        $modules = [
            'dashboard' => [
                'display_ko' => '대시보드',
                'display_en' => 'Dashboard',
                'actions' => ['view']
            ],
            // 섹션 레벨 권한 (메뉴 그룹 표시 제어)
            'section_dashboard' => [
                'display_ko' => '대시보드 섹션',
                'display_en' => 'Dashboard Section',
                'actions' => ['view']
            ],
            'section_home' => [
                'display_ko' => '홈 구성 섹션',
                'display_en' => 'Home Section',
                'actions' => ['view']
            ],
            'section_content' => [
                'display_ko' => '콘텐츠 섹션',
                'display_en' => 'Content Section',
                'actions' => ['view']
            ],
            'section_research' => [
                'display_ko' => '리서치 허브 섹션',
                'display_en' => 'Research Hub Section',
                'actions' => ['view']
            ],
            'section_routine' => [
                'display_ko' => '루틴 섹션',
                'display_en' => 'Routine Section',
                'actions' => ['view']
            ],
            'section_partner' => [
                'display_ko' => '파트너 섹션',
                'display_en' => 'Partner Section',
                'actions' => ['view']
            ],
            'section_survey' => [
                'display_ko' => '설문 섹션',
                'display_en' => 'Survey Section',
                'actions' => ['view']
            ],
            'section_media' => [
                'display_ko' => '미디어 섹션',
                'display_en' => 'Media Section',
                'actions' => ['view']
            ],
            'section_marketing' => [
                'display_ko' => '마케팅 섹션',
                'display_en' => 'Marketing Section',
                'actions' => ['view']
            ],
            'section_site' => [
                'display_ko' => '사이트 섹션',
                'display_en' => 'Site Section',
                'actions' => ['view']
            ],
            'section_settings' => [
                'display_ko' => '설정 섹션',
                'display_en' => 'Settings Section',
                'actions' => ['view']
            ],
            // 홈 구성
            'heroes' => [
                'display_ko' => 'Heroes',
                'display_en' => 'Heroes',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'featured_posts' => [
                'display_ko' => 'Featured Posts',
                'display_en' => 'Featured Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'banner_posts' => [
                'display_ko' => 'Banner Posts',
                'display_en' => 'Banner Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 콘텐츠
            'blog_posts' => [
                'display_ko' => 'Blog Posts',
                'display_en' => 'Blog Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'food_posts' => [
                'display_ko' => 'Food Posts',
                'display_en' => 'Food Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'news_posts' => [
                'display_ko' => 'News Posts',
                'display_en' => 'News Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'product_posts' => [
                'display_ko' => 'Product Posts',
                'display_en' => 'Product Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'qna_posts' => [
                'display_ko' => 'QnA Posts',
                'display_en' => 'QnA Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'service_posts' => [
                'display_ko' => 'Service Posts',
                'display_en' => 'Service Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 리서치 허브
            'paper_posts' => [
                'display_ko' => 'Paper Posts',
                'display_en' => 'Paper Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'tab_posts' => [
                'display_ko' => 'Tab Posts',
                'display_en' => 'Tab Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 루틴
            'routine_posts' => [
                'display_ko' => 'Routine Posts',
                'display_en' => 'Routine Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 파트너
            'partners' => [
                'display_ko' => 'Partners',
                'display_en' => 'Partners',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 설문
            'surveys' => [
                'display_ko' => 'Surveys',
                'display_en' => 'Surveys',
                'actions' => ['view', 'create', 'edit', 'delete', 'analyze']
            ],
            'survey_responses' => [
                'display_ko' => 'Survey Responses',
                'display_en' => 'Survey Responses',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 미디어
            'video_posts' => [
                'display_ko' => 'Video Posts',
                'display_en' => 'Video Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 마케팅
            'promotion_posts' => [
                'display_ko' => 'Promotion Posts',
                'display_en' => 'Promotion Posts',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 사이트
            'custom_pages' => [
                'display_ko' => 'Custom Pages',
                'display_en' => 'Custom Pages',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'footer_menus' => [
                'display_ko' => 'Footer Menus',
                'display_en' => 'Footer Menus',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'header_menus' => [
                'display_ko' => 'Header Menus',
                'display_en' => 'Header Menus',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            // 설정
            'users' => [
                'display_ko' => 'Users',
                'display_en' => 'Users',
                'actions' => ['view', 'create', 'edit', 'delete']
            ],
            'roles' => [
                'display_ko' => 'Roles',
                'display_en' => 'Roles',
                'actions' => ['view', 'create', 'edit', 'delete']
            ]
        ];

        $permissions = [];
        
        foreach ($modules as $module => $config) {
            foreach ($config['actions'] as $action) {
                $actionDisplays = [
                    'view' => ['보기', 'View'],
                    'create' => ['생성', 'Create'],
                    'edit' => ['수정', 'Edit'],
                    'delete' => ['삭제', 'Delete'],
                    'analyze' => ['분석', 'Analyze']
                ];
                
                $permissions[] = [
                    'name' => "{$module}.{$action}",
                    'slug' => "{$module}-{$action}",
                    'module' => $module,
                    'action' => $action,
                    'display_name_ko' => $config['display_ko'] . ' ' . ($actionDisplays[$action][0] ?? $action),
                    'display_name_en' => $config['display_en'] . ' ' . ($actionDisplays[$action][1] ?? $action),
                    'description' => "Allow {$action} action on {$module}",
                ];
            }
        }
        
        return $permissions;
    }
}
