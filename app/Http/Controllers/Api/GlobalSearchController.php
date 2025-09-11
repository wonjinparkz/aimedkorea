<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Survey;
use App\Models\User;
use App\Models\Hero;
use App\Models\FooterMenu;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 20);
        
        \Log::info('GlobalSearch API called', [
            'query' => $query,
            'limit' => $limit,
            'user' => auth()->check() ? auth()->user()->id : 'guest',
            'session_locale' => session('locale', 'kor')
        ]);
        
        if (strlen($query) < 2) {
            \Log::info('Query too short', ['query' => $query]);
            return response()->json([
                'results' => [],
                'query' => $query,
                'message' => '검색어를 2자 이상 입력하세요'
            ]);
        }

        $results = [];
        $currentLang = session('locale', 'kor');
        
        try {
        
        // 게시물 검색
        $posts = Post::where(function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('slug', 'LIKE', "%{$query}%")
              ->orWhere('content', 'LIKE', "%{$query}%");
        })
        ->where('is_published', true)
        ->take(10)
        ->get();
        
        \Log::info('Posts search results', [
            'query' => $query,
            'count' => $posts->count(),
            'posts' => $posts->pluck('title')->toArray()
        ]);
        
        $postResults = $posts->map(function($post) {
            $title = $post->title;
            
            // 페이지 타입인 경우 custom-pages URL 사용
            $url = $post->type === 'page' 
                ? '/admin/custom-pages/' . $post->id . '/edit'
                : '/admin/posts/' . $post->id . '/edit';
                
            return [
                'id' => 'post-' . $post->id,
                'title' => $title,
                'description' => \Str::limit(strip_tags($post->content), 100),
                'type' => $this->getPostTypeLabel($post->type),
                'path' => '콘텐츠 > ' . $this->getPostTypeLabel($post->type),
                'url' => $url,
                'model' => 'Post',
                'created_at' => $post->created_at->format('Y-m-d')
            ];
        });
        
        $results = array_merge($results, $postResults->toArray());
        
        // 설문조사 검색
        $surveys = Survey::where(function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%");
            
            // JSON 필드 검색 (다국어 지원) - 따옴표 처리
            $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title_translations, '$.kor')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title_translations, '$.eng')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description_translations, '$.kor')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description_translations, '$.eng')) LIKE ?", ["%{$query}%"]);
        })
        ->take(5)
        ->get()
        ->map(function($survey) use ($currentLang) {
            // JSON 필드에서 번역 가져오기
            $titleTranslations = json_decode($survey->title_translations, true) ?? [];
            $descTranslations = json_decode($survey->description_translations, true) ?? [];
            
            $title = isset($titleTranslations[$currentLang]) ? $titleTranslations[$currentLang] : $survey->title;
            $description = isset($descTranslations[$currentLang]) ? $descTranslations[$currentLang] : $survey->description;
            
            return [
                'id' => 'survey-' . $survey->id,
                'title' => $title,
                'description' => \Str::limit($description, 100),
                'type' => '설문조사',
                'path' => '설문 > 설문조사',
                'url' => '/admin/surveys/' . $survey->id . '/edit',
                'model' => 'Survey',
                'response_count' => $survey->responses()->count()
            ];
        });
        
        $results = array_merge($results, $surveys->toArray());
        
        // 사용자 검색
        try {
            if (auth()->user() && method_exists(auth()->user(), 'can') && auth()->user()->can('view_any_user')) {
            $users = User::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%");
            })
            ->take(5)
            ->get()
            ->map(function($user) {
                return [
                    'id' => 'user-' . $user->id,
                    'title' => $user->name,
                    'description' => $user->email . ($user->username ? ' (@' . $user->username . ')' : ''),
                    'type' => '사용자',
                    'path' => '사용자 관리',
                    'url' => '/admin/users/' . $user->id . '/edit',
                    'model' => 'User',
                    'created_at' => $user->created_at->format('Y-m-d')
                ];
            });
            
            $results = array_merge($results, $users->toArray());
            }
        } catch (\Exception $e) {
            // 권한 체크 실패 시 무시
        }
        
        // Hero 슬라이더 검색
        $heroes = Hero::where(function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('subtitle', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%");
            
            // JSON 다국어 필드 검색 - 따옴표 처리
            $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title_translations, '$.kor')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title_translations, '$.eng')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subtitle_translations, '$.kor')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(subtitle_translations, '$.eng')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description_translations, '$.kor')) LIKE ?", ["%{$query}%"])
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description_translations, '$.eng')) LIKE ?", ["%{$query}%"]);
        })
        ->where('is_active', true)
        ->take(5)
        ->get();
        
        \Log::info('Heroes search results', [
            'query' => $query,
            'count' => $heroes->count(),
            'heroes' => $heroes->map(function($h) use ($currentLang) { 
                return $h->getTitle($currentLang); 
            })->toArray()
        ]);
        
        $heroResults = $heroes->map(function($hero) use ($currentLang) {
            $title = $hero->getTitle($currentLang);
            
            return [
                'id' => 'hero-' . $hero->id,
                'title' => $title,
                'description' => $hero->getSubtitle($currentLang),
                'type' => 'Hero 슬라이더',
                'path' => '홈 구성 > Hero',
                'url' => '/admin/heroes/' . $hero->id . '/edit',
                'model' => 'Hero'
            ];
        });
        
        $results = array_merge($results, $heroResults->toArray());
        
        // 파트너 검색 - Partner 모델이 없으므로 제거
        
        // 정적 메뉴 항목 추가
        $staticMenus = $this->getStaticMenuItems($query);
        $results = array_merge($results, $staticMenus);
        
        // 결과 정렬 (검색어와의 관련성 순)
        usort($results, function($a, $b) use ($query) {
            $aScore = $this->calculateRelevanceScore($a, $query);
            $bScore = $this->calculateRelevanceScore($b, $query);
            return $bScore - $aScore;
        });
        
        // 결과 제한
        $results = array_slice($results, 0, $limit);
        
        \Log::info('Final search results', [
            'query' => $query,
            'total_results' => count($results),
            'result_types' => array_count_values(array_column($results, 'type'))
        ]);
        
        // Google Analytics 이벤트 로깅
        if ($request->get('log_event', false)) {
            \Log::channel('search')->info('Global Search', [
                'query' => $query,
                'results_count' => count($results),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
        }
        
        return response()->json([
            'results' => $results,
            'query' => $query,
            'total' => count($results),
            'timestamp' => now()->toIso8601String()
        ]);
        
        } catch (\Exception $e) {
            // 에러 발생 시 로그 기록 및 빈 결과 반환
            \Log::error('Global Search Error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'results' => [],
                'query' => $query,
                'total' => 0,
                'error' => 'Search error occurred',
                'timestamp' => now()->toIso8601String()
            ], 200); // 200으로 반환하여 프론트엔드에서 처리 가능하도록
        }
    }
    
    private function getPostTypeLabel($type)
    {
        $labels = [
            'blog' => '블로그',
            'news' => '뉴스',
            'routine' => '루틴',
            'featured' => '특징',
            'product' => '상품',
            'food' => '식품',
            'service' => '서비스',
            'promotion' => '홍보',
            'video' => '영상',
            'tab' => '탭',
            'page' => '페이지'
        ];
        
        return $labels[$type] ?? $type;
    }
    
    private function getStaticMenuItems($query)
    {
        $menus = [
            ['title' => '대시보드', 'description' => '관리자 대시보드', 'type' => '페이지', 'path' => '관리자', 'url' => '/admin'],
            ['title' => '사용자 목록', 'description' => '전체 사용자 관리', 'type' => '메뉴', 'path' => '사용자', 'url' => '/admin/users'],
            ['title' => '역할 관리', 'description' => '사용자 역할 및 권한', 'type' => '설정', 'path' => '설정 > 역할', 'url' => '/admin/shield/roles'],
            ['title' => '미디어 라이브러리', 'description' => '이미지 및 파일 관리', 'type' => '메뉴', 'path' => '미디어', 'url' => '/admin/media'],
            ['title' => '일반 설정', 'description' => '사이트 설정', 'type' => '설정', 'path' => '설정', 'url' => '/admin/settings'],
            ['title' => '푸터 메뉴', 'description' => '푸터 메뉴 관리', 'type' => '메뉴', 'path' => '사이트 > 푸터', 'url' => '/admin/footer-menus'],
            ['title' => '게시물 작성', 'description' => '새 게시물 작성', 'type' => '액션', 'path' => '콘텐츠 > 게시물', 'url' => '/admin/posts/create'],
            ['title' => '설문 생성', 'description' => '새 설문조사 만들기', 'type' => '액션', 'path' => '설문', 'url' => '/admin/surveys/create'],
            ['title' => '사용자 생성', 'description' => '새 사용자 추가', 'type' => '액션', 'path' => '사용자', 'url' => '/admin/users/create'],
        ];
        
        $query = mb_strtolower($query);
        $filtered = [];
        
        foreach ($menus as $menu) {
            if (mb_strpos(mb_strtolower($menu['title']), $query) !== false ||
                mb_strpos(mb_strtolower($menu['description']), $query) !== false ||
                mb_strpos(mb_strtolower($menu['path']), $query) !== false) {
                $menu['id'] = 'menu-' . md5($menu['url']);
                $menu['model'] = 'Menu';
                $filtered[] = $menu;
            }
        }
        
        return $filtered;
    }
    
    private function calculateRelevanceScore($item, $query)
    {
        $score = 0;
        $query = mb_strtolower($query);
        $title = mb_strtolower($item['title']);
        
        // 제목에 완전 일치
        if ($title === $query) {
            $score += 100;
        }
        
        // 제목 시작 부분 일치
        if (mb_strpos($title, $query) === 0) {
            $score += 50;
        }
        
        // 제목에 포함
        if (mb_strpos($title, $query) !== false) {
            $score += 30;
        }
        
        // 설명에 포함
        if (isset($item['description']) && mb_strpos(mb_strtolower($item['description']), $query) !== false) {
            $score += 10;
        }
        
        // 최근 생성된 항목 우선
        if (isset($item['created_at'])) {
            $date = \Carbon\Carbon::parse($item['created_at']);
            if ($date->isToday()) {
                $score += 5;
            } elseif ($date->isYesterday()) {
                $score += 3;
            } elseif ($date->isCurrentWeek()) {
                $score += 1;
            }
        }
        
        return $score;
    }
}