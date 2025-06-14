# Project Options 사용 가이드

Laravel 프로젝트에서 워드프레스의 wp-options와 같은 유연한 설정 관리 시스템을 제공합니다.

## 사용 방법

### 1. 옵션 값 가져오기
```php
// 기본 사용법
$value = get_option('option_name');

// 기본값 설정
$value = get_option('option_name', 'default_value');

// JSON 데이터는 자동으로 디코딩됨
$social_links = get_option('social_links'); // 배열 반환
```

### 2. 옵션 값 설정하기
```php
// 단순 값 저장
update_option('site_title', 'My Website');

// 배열이나 객체 저장 (자동으로 JSON 인코딩)
update_option('homepage_settings', [
    'show_hero' => true,
    'posts_per_page' => 10
]);

// autoload 설정 (기본값: 'yes')
update_option('heavy_data', $data, 'no');
```

### 3. 옵션 추가하기 (이미 존재하면 추가하지 않음)
```php
add_option('new_feature', 'enabled');
```

### 4. 옵션 삭제하기
```php
delete_option('deprecated_feature');
```

## 모델 직접 사용

헬퍼 함수 대신 모델을 직접 사용할 수도 있습니다:

```php
use App\Models\ProjectOption;

// 값 가져오기
$value = ProjectOption::get('option_name', 'default');

// 값 설정하기
ProjectOption::set('option_name', 'value');

// 삭제하기
ProjectOption::remove('option_name');

// 자동 로드 옵션들 가져오기
$autoloadOptions = ProjectOption::getAutoloadOptions();

// 캐시 초기화
ProjectOption::clearCache();
```

## 기본 제공 옵션들

프로젝트에는 다음과 같은 기본 옵션들이 제공됩니다:

- `site_title`: 사이트 제목
- `site_tagline`: 사이트 태그라인
- `site_email`: 사이트 이메일
- `site_phone`: 사이트 전화번호
- `site_address`: 사이트 주소
- `social_links`: 소셜 미디어 링크 (JSON)
- `homepage_settings`: 홈페이지 설정 (JSON)
- `footer_links`: 푸터 링크 (JSON)
- `meta_settings`: 메타 태그 설정 (JSON)
- `business_hours`: 업무 시간 (JSON)
- `feature_toggles`: 기능 토글 (JSON)

## Blade 템플릿에서 사용

```blade
<h1>{{ get_option('site_title') }}</h1>
<p>{{ get_option('site_tagline') }}</p>

@php
    $social_links = get_option('social_links', []);
@endphp

@foreach($social_links as $platform => $url)
    @if($url)
        <a href="{{ $url }}">{{ ucfirst($platform) }}</a>
    @endif
@endforeach
```

## 활용 예시

### 1. 사이트 설정 관리
```php
// 사이트 기본 정보
update_option('site_title', '에임드 코리아');
update_option('site_description', 'AI 기반 의료 혁신 플랫폼');
update_option('site_logo', '/images/logo.png');
```

### 2. 기능 토글
```php
// 기능 활성화/비활성화
if (get_option('feature_toggles')['maintenance_mode'] ?? false) {
    return view('maintenance');
}
```

### 3. API 키 관리
```php
// API 키 저장 (autoload 'no'로 설정)
update_option('google_maps_api_key', 'your-api-key', 'no');
update_option('payment_gateway_settings', [
    'key' => 'xxx',
    'secret' => 'yyy'
], 'no');
```

### 4. 테마/레이아웃 설정
```php
update_option('theme_settings', [
    'primary_color' => '#3B82F6',
    'font_family' => 'Inter',
    'layout' => 'wide'
]);
```

## 캐싱

모든 옵션은 자동으로 캐싱되어 성능이 최적화됩니다:
- 개별 옵션은 1시간 동안 캐싱
- 옵션 업데이트 시 자동으로 캐시 갱신
- `autoload='yes'` 옵션들은 별도로 캐싱

## 마이그레이션 및 시더

새로운 기본 옵션을 추가하려면 `ProjectOptionSeeder`를 수정하세요:

```php
// database/seeders/ProjectOptionSeeder.php
$options = [
    [
        'option_name' => 'new_option',
        'option_value' => 'value',
        'autoload' => 'yes'
    ],
    // ...
];
```

시더 실행:
```bash
./vendor/bin/sail artisan db:seed --class=ProjectOptionSeeder
```

## 장점

1. **유연성**: 별도 테이블 없이 다양한 설정 저장
2. **성능**: 자동 캐싱으로 빠른 읽기 속도
3. **간편함**: 워드프레스와 유사한 직관적인 API
4. **확장성**: JSON 지원으로 복잡한 데이터 구조 저장 가능
5. **유지보수**: 중앙 집중식 설정 관리
