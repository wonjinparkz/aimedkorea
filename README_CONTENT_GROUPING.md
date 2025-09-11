# 다국어 콘텐츠 그룹핑 시스템

## 📋 개요

다국어 번역으로 인한 중복 콘텐츠 노출 문제를 해결하기 위해 콘텐츠 그룹핑 시스템을 구현했습니다.

## 🎯 해결된 문제

**이전**: 홈페이지에서 한국어, 영어, 중국어 등 같은 콘텐츠의 다국어 버전이 모두 최신 게시글로 표시
**현재**: 홈페이지에서는 주 게시글(Primary Post)만 표시되어 중복 노출 방지

## 🔧 구현 내용

### 1. 데이터베이스 구조

새로 추가된 컬럼:
- `content_group_id`: 같은 콘텐츠의 다국어 버전들을 그룹핑하는 고유 ID
- `is_primary`: 해당 게시글이 그룹의 대표 게시글인지 여부

```sql
-- 예시 데이터
content_group_id: "blog_ai-medical-innovation-20250707"
├── Post 1: language=kor, is_primary=1 (주 게시글)
├── Post 2: language=eng, is_primary=0 
├── Post 3: language=chn, is_primary=0
└── Post 4: language=hin, is_primary=0
```

### 2. 자동 그룹핑 로직

- **신규 번역 생성 시**: 원본 게시글과 같은 `content_group_id` 할당
- **주 게시글 설정**: 한국어 게시글이 우선, 없으면 가장 먼저 생성된 게시글
- **고유 ID 생성**: `{type}_{base_slug}_{timestamp}_{random}` 형식

### 3. Filament 관리자 UI

- **기본 뷰**: 주 게시글만 표시 (중복 제거)
- **전체 뷰**: "주 게시글" 필터를 해제하면 모든 번역본 표시
- **시각적 표시**: ⭐ 주 게시글, 🌐 번역본
- **그룹 ID**: 관리자가 필요시 확인 가능

## 🚀 사용 방법

### 프론트엔드에서 사용

```php
// 홈페이지: 주 게시글만 가져오기 (중복 제거)
$posts = Post::primary()
    ->published()
    ->latest()
    ->take(10)
    ->get();

// 특정 언어의 게시글 가져오기
$koreanPosts = Post::inLanguage('kor')
    ->published()
    ->latest()
    ->get();

// 특정 게시글의 다국어 버전들 가져오기
$post = Post::find(1);
$translations = $post->getAllVersions();
$englishVersion = $post->getTranslation('eng');
```

### 번역 작업 시

```bash
# 번역 생성 시 자동으로 그룹핑됨
php artisan posts:translate "1,2,3" chn
```

## 📊 마이그레이션 결과

- **총 게시글**: 77개
- **콘텐츠 그룹**: 74개
- **주 게시글**: 74개 (그룹당 1개)

## 🎨 관리자 인터페이스

### 필터 옵션
- **주 게시글**: ✅ (기본값) - 중복 없이 깔끔하게 표시
- **모든 게시글**: ❌ - 번역본까지 모두 표시
- **언어별**: 한국어, 영어, 중국어, 힌디어, 아랍어

### 컬럼 표시
- **제목**: 게시글 제목
- **요약**: 내용 요약 (50자 제한)
- **주 게시글**: ⭐/🌐 아이콘으로 구분
- **그룹 ID**: 같은 콘텐츠 확인용 (기본 숨김)

## 🔍 모델 메서드

```php
// 콘텐츠 그룹 관련
$post->contentGroup()          // 같은 그룹의 다른 게시글들
$post->getPrimaryPost()        // 그룹의 주 게시글
$post->getAllVersions()        // 그룹의 모든 언어 버전

// 번역 관련 (기존과 동일)
$post->getTranslation('eng')   // 영어 버전 가져오기
$post->hasTranslation('chn')   // 중국어 버전 존재 여부
$post->getAvailableLanguages() // 사용 가능한 언어 목록

// 스코프
Post::primary()                // 주 게시글만
Post::inLanguage('kor')        // 특정 언어만
```

## ✅ 이점

1. **홈페이지 정리**: 같은 콘텐츠의 다국어 버전 중복 노출 방지
2. **SEO 개선**: 언어별 canonical URL 설정 가능
3. **관리 편의성**: 관리자에서 주 콘텐츠와 번역본 구분
4. **성능 향상**: 불필요한 중복 데이터 로딩 감소
5. **사용자 경험**: 깔끔한 콘텐츠 목록 제공

## 🔧 향후 확장

- **언어 감지**: 사용자 브라우저 언어에 따른 자동 번역본 표시
- **다국어 라우팅**: `/ko/posts/title`, `/en/posts/title` 형태
- **번역 상태**: 번역 품질, 검토 상태 등 메타데이터 추가