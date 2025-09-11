# Argos Translate 일괄 번역 기능

## 📋 개요

Argos Translate를 사용하여 게시글을 자동으로 다국어 번역하는 기능입니다. 한국어 게시글을 영어, 중국어, 힌디어, 아랍어로 자동 번역하여 다국어 버전을 생성합니다.

## 🚀 설치 및 설정

### 1. Python 환경 설정

```bash
# Python 3.8+ 설치 확인
python3 --version

# Argos Translate 설치
pip install argostranslate

# 번역 스크립트 실행 권한 부여
chmod +x scripts/translate.py
```

### 2. 언어 모델 다운로드

번역 스크립트를 처음 실행하면 자동으로 필요한 언어 모델을 다운로드합니다:
- Korean → English
- Korean → Chinese
- Korean → Hindi  
- Korean → Arabic

### 3. 임시 폴더 생성

```bash
mkdir -p storage/app/temp
chmod 755 storage/app/temp
```

## 💻 사용 방법

### 1. Filament 관리자 패널에서 사용

1. **게시글 목록**에서 번역할 게시글들을 체크박스로 선택
2. **일괄 번역** 버튼 클릭
3. **번역할 언어** 선택 (영어, 중국어, 힌디어, 아랍어)
4. **기존 번역 덮어쓰기** 옵션 설정 (선택사항)
5. **번역 시작** 버튼 클릭

### 2. Artisan 명령어로 사용

```bash
# 단일 언어 번역
php artisan posts:translate "1,2,3" eng

# 기존 번역 덮어쓰기
php artisan posts:translate "1,2,3" eng --force

# 도움말 보기
php artisan posts:translate --help
```

### 3. Python 스크립트 직접 사용

```bash
# 개별 게시글 번역
python3 scripts/translate.py \
  --input input.json \
  --output output.json \
  --target-language eng \
  --verbose
```

## 📁 파일 구조

```
aimedkorea/
├── scripts/
│   └── translate.py                     # Python 번역 스크립트
├── app/
│   └── Console/Commands/
│       └── TranslatePosts.php           # Artisan 명령어
├── app/Filament/Resources/
│   └── PostResource.php                 # Filament UI (일괄 번역 기능)
├── resources/views/filament/actions/
│   └── translate-progress.blade.php     # 번역 진행률 UI
└── storage/app/temp/                    # 임시 파일 저장소
```

## 🔧 주요 기능

### 1. 지원 언어
- **한국어 (kor)** → **영어 (eng)**
- **한국어 (kor)** → **중국어 (chn)**  
- **한국어 (kor)** → **힌디어 (hin)**
- **한국어 (kor)** → **아랍어 (arb)**

### 2. 번역 필드
- **제목 (title)**
- **요약 (summary)**  
- **본문 (content)**

### 3. 자동 기능
- **언어 모델 자동 다운로드**
- **중복 번역 방지** (force 옵션으로 재정의 가능)
- **base_slug 자동 연결** (다국어 버전 간 관계 설정)
- **고유 slug 생성** (제목-언어-번호 형식)

## 🎯 작동 원리

1. **게시글 선택**: Filament에서 체크박스로 번역할 게시글 선택
2. **데이터 준비**: 선택된 게시글의 제목, 요약, 본문을 JSON으로 추출
3. **Python 호출**: Laravel에서 Python 번역 스크립트 실행
4. **Argos 번역**: Python에서 Argos Translate로 텍스트 번역
5. **데이터 저장**: 번역된 내용으로 새로운 게시글 생성
6. **관계 설정**: base_slug로 원본과 번역본 연결

## ⚠️ 주의사항

### 1. 시스템 요구사항
- **Python 3.8+** 필수
- **최소 2GB RAM** (언어 모델 로딩용)
- **인터넷 연결** (최초 모델 다운로드용)

### 2. 번역 품질
- **완벽하지 않은 번역**: 기계 번역이므로 수동 검토 권장
- **컨텍스트 제한**: 문장 단위 번역으로 문맥 파악 한계
- **전문 용어**: 의료/기술 용어는 정확도가 떨어질 수 있음

### 3. 성능 고려사항
- **번역 시간**: 게시글당 10-30초 소요
- **메모리 사용량**: 번역 중 높은 메모리 사용
- **동시 실행**: 여러 번역 작업 동시 실행 시 성능 저하

## 🔍 트러블슈팅

### 1. Python 관련 오류

```bash
# Python 설치 확인
python3 --version

# pip 업그레이드
python3 -m pip install --upgrade pip

# argostranslate 재설치
pip uninstall argostranslate
pip install argostranslate
```

### 2. 권한 오류

```bash
# 스크립트 실행 권한
chmod +x scripts/translate.py

# 임시 폴더 권한
chmod 755 storage/app/temp
```

### 3. 메모리 부족

```bash
# PHP 메모리 제한 증가 (php.ini)
memory_limit = 512M

# Python 프로세스 타임아웃 증가
timeout = 600
```

### 4. 언어 모델 다운로드 실패

```bash
# 수동 모델 설치
python3 -c "
import argostranslate.package
argostranslate.package.update_package_index()
available = argostranslate.package.get_available_packages()
ko_en = next(p for p in available if p.from_code=='ko' and p.to_code=='en')
argostranslate.package.install_from_path(ko_en.download())
"
```

## 📊 로그 및 모니터링

### 1. Laravel 로그

```bash
# 번역 로그 확인
tail -f storage/logs/laravel.log | grep "Translation"
```

### 2. Python 스크립트 로그

```bash
# 상세 로그로 실행
python3 scripts/translate.py --verbose --input test.json --output result.json --target-language eng
```

### 3. 성능 모니터링

```bash
# 시스템 리소스 확인
htop

# Python 프로세스 확인
ps aux | grep python
```

## 🚀 고급 사용법

### 1. 배치 처리

```bash
# 대량 게시글 번역 (큐 사용 권장)
php artisan queue:work --sleep=3 --tries=3
```

### 2. 사용자 정의 번역

```python
# scripts/translate.py 수정하여 번역 로직 커스터마이징
def custom_translate_text(self, text, target_language):
    # 전처리
    text = self.preprocess_text(text)
    
    # 번역
    translated = self.translate_text(text, target_language)
    
    # 후처리
    translated = self.postprocess_text(translated)
    
    return translated
```

### 3. 번역 품질 개선

```python
# 문장 분할 번역으로 품질 향상
def translate_by_sentences(self, text, target_language):
    sentences = text.split('.')
    translated_sentences = []
    
    for sentence in sentences:
        if sentence.strip():
            translated = self.translate_text(sentence.strip(), target_language)
            translated_sentences.append(translated)
    
    return '. '.join(translated_sentences)
```

## 📞 지원

문제가 발생하면 다음을 확인하세요:

1. **시스템 로그**: `storage/logs/laravel.log`
2. **Python 환경**: `python3 --version`
3. **메모리 사용량**: `free -h`
4. **디스크 공간**: `df -h`

추가 지원이 필요하면 개발팀에 문의하세요.