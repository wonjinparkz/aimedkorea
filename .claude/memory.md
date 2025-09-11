# AIMED Korea 프로젝트 메모리

## 프로젝트 개요
- **프로젝트명**: AIMED Korea
- **도메인**: aimedkorea.cafe24.com
- **프레임워크**: Laravel + Filament Admin
- **데이터베이스**: MySQL
- **서버**: Ubuntu Linux (Cafe24 호스팅)

## 주요 기능

### 1. 다국어 지원 (Multilingual Support)
- **지원 언어**: 한국어(ko), 영어(en), 아랍어(ar), 힌디어(hi), 중국어(zh)
- **구현 방식**: 
  - SetLocale 미들웨어를 통한 자동 로케일 감지
  - JSON 기반 번역 파일 (lang/*.json, lang/*/auth.php)
  - 데이터베이스 컬럼별 번역 저장 (title_ko, title_en 등)
- **번역 자동화**: Python 스크립트 (scripts/translate.py)로 Google Translate API 활용

### 2. PWA (Progressive Web App)
- **Service Worker**: 오프라인 지원, 캐싱 전략 구현
- **Manifest**: 앱 설치, 아이콘, 테마 색상 설정
- **오프라인 페이지**: 네트워크 연결 없을 때 표시
- **아이콘**: 다양한 크기의 PWA 아이콘 생성 스크립트 제공

### 3. 콘텐츠 관리 시스템
- **게시물 타입**: 
  - 일반 게시물 (blog, news, routine, featured)
  - 특수 게시물 (food, product, promotion, service)
- **콘텐츠 그룹화**: 게시물을 그룹으로 묶어 관리
- **Hero 슬라이더**: 메인 페이지 배너 관리
- **Footer 메뉴**: 다국어 지원 푸터 메뉴

### 4. 설문조사 시스템
- **다국어 설문**: 질문과 답변 모두 다국어 지원
- **응답 분석**: 
  - 빈도 분석 (frequency)
  - 카테고리 분석 (category)
  - 사용자 정의 분석
- **결과 시각화**: Chart.js를 활용한 그래프
- **댓글 시스템**: 설문 결과에 대한 의견 공유

### 5. 사용자 인증
- **Laravel Fortify**: 로그인, 회원가입, 비밀번호 재설정
- **Username 지원**: 이메일 또는 사용자명으로 로그인
- **권한 관리**: Filament Shield로 역할 기반 접근 제어

## 기술 스택

### Backend
- PHP 8.1+
- Laravel 11.x
- Filament 3.x (Admin Panel)
- MySQL 8.0

### Frontend
- Blade Templates
- Tailwind CSS
- Alpine.js
- Livewire
- Chart.js

### DevOps
- Git (GitHub)
- Composer
- NPM/Vite
- Apache2

## 데이터베이스 구조

### 주요 테이블
- `users`: 사용자 정보 (username 필드 추가)
- `posts`: 게시물 (content_group, 다국어 필드)
- `surveys`: 설문조사 (JSON 형태의 다국어 데이터)
- `survey_responses`: 설문 응답
- `heroes`: Hero 슬라이더 (다국어 지원)
- `footer_menus`: 푸터 메뉴 (다국어 지원)

### 마이그레이션 히스토리
- 2025_07_07: content_group 추가
- 2025_07_12: 다국어 필드 추가 (surveys, heroes)
- 2025_07_13: 설문 분석 타입, 이미지 추가
- 2025_07_14: 설문 결과 코멘터리 추가

## 파일 구조

```
/var/www/html/aimedkorea/
├── .claude/                 # Claude AI 프로젝트 설정
│   ├── project.md
│   ├── settings.md
│   └── memory.md (이 파일)
├── app/
│   ├── Console/Commands/    # Artisan 명령어
│   │   └── TranslatePosts.php
│   ├── Filament/            # Admin 패널
│   ├── Http/
│   │   └── Middleware/
│   │       └── SetLocale.php
│   └── Models/
├── database/
│   ├── migrations/
│   └── seeders/
├── lang/                    # 번역 파일
│   ├── ko/
│   ├── en/
│   ├── ar/
│   ├── hi/
│   └── zh/
├── public/
│   ├── images/icons/        # PWA 아이콘
│   ├── js/pwa/              # PWA 스크립트
│   ├── manifest.json
│   └── service-worker.js
├── resources/
│   └── views/
│       ├── filament/        # Admin 뷰 커스터마이징
│       ├── pwa/             # PWA 관련 뷰
│       └── surveys/         # 설문 뷰
└── scripts/                 # 유틸리티 스크립트
    ├── translate.py
    ├── generate-pwa-icons.sh
    └── create-self-signed-ssl.sh
```

## 환경 설정

### 환경 변수 (.env)
- `APP_LOCALE`: 기본 언어 (ko)
- `APP_FALLBACK_LOCALE`: 대체 언어 (en)
- 데이터베이스 연결 정보
- 메일 설정

### Apache 설정
- `.htaccess`: URL 리라이팅, 보안 헤더
- SSL 인증서: Let's Encrypt 또는 자체 서명

## 최근 작업 내역

### 2025-09-11
- 프로젝트 메모리 문서 생성
- 다국어 지원 완성
- PWA 기능 구현
- 설문조사 시스템 고도화
- 콘텐츠 그룹화 기능 추가

## 주의사항

1. **Git 인증**: Personal Access Token 사용 필요
2. **파일 권한**: storage/, bootstrap/cache/ 쓰기 권한 필요
3. **캐시 관리**: 
   - `php artisan config:clear`
   - `php artisan view:clear`
   - `php artisan cache:clear`
4. **번역 작업**: Google Translate API 제한 고려
5. **PWA 테스트**: HTTPS 환경 필요

## 향후 계획

- [ ] 검색 엔진 최적화 (SEO)
- [ ] 소셜 미디어 연동
- [ ] 이메일 알림 시스템
- [ ] 실시간 채팅 기능
- [ ] API 개발 (RESTful/GraphQL)
- [ ] 성능 최적화 (Redis 캐싱)
- [ ] 자동 백업 시스템
- [ ] CI/CD 파이프라인 구축

## 문제 해결

### 일반적인 문제
1. **404 에러**: 라우트 캐시 정리 (`php artisan route:clear`)
2. **번역 안 됨**: 언어 파일 캐시 정리
3. **PWA 설치 안 됨**: HTTPS 확인, manifest.json 검증
4. **DB 마이그레이션 실패**: 외래 키 제약 조건 확인

### 연락처
- GitHub: https://github.com/wonjinparkz/aimedkorea
- 프로젝트 관리자: [관리자 정보]

---
*Last Updated: 2025-09-11*