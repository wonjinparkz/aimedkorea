# AI-MED Korea 앱 빌드 및 배포 가이드

## 📱 Android (Google Play Store)

### 사전 준비
1. Node.js 14+ 설치
2. Java JDK 8+ 설치
3. Android Studio 설치 (선택사항)

### TWA (Trusted Web Activity) 빌드

#### 1. Bubblewrap CLI 설치
```bash
npm i -g @bubblewrap/cli
```

#### 2. 프로젝트 초기화
```bash
bubblewrap init --manifest https://aimedkorea.cafe24.com/manifest.json
```

#### 3. 키스토어 생성
```bash
keytool -genkey -v -keystore android.keystore -alias android -keyalg RSA -keysize 2048 -validity 10000
```

#### 4. SHA256 핑거프린트 추출
```bash
keytool -list -v -keystore android.keystore -alias android | grep SHA256
```

#### 5. assetlinks.json 업데이트
- `/public/.well-known/assetlinks.json` 파일의 SHA256 핑거프린트 업데이트

#### 6. AAB 빌드
```bash
bubblewrap build
```

### Play Console 업로드
1. [Google Play Console](https://play.google.com/console) 접속
2. 새 앱 생성 → 패키지명: `kr.co.aimed.mobile`
3. 내부 테스트 트랙 선택
4. `app-release-bundle.aab` 파일 업로드
5. 테스터 이메일 추가

### 필요 정보
- **패키지명**: kr.co.aimed.mobile
- **버전코드**: 1
- **버전명**: 1.0.0
- **최소 SDK**: 19 (Android 4.4+)

---

## 🍎 iOS (App Store)

### 사전 준비
1. macOS 환경
2. Xcode 14+ 설치
3. Apple Developer 계정

### PWA 래퍼 앱 생성

#### 1. PWABuilder 사용
```bash
npm i -g @pwabuilder/cli
pwabuilder package -p ios -m https://aimedkorea.cafe24.com/manifest.json
```

#### 2. Xcode 프로젝트 설정
1. 생성된 `.xcodeproj` 파일 열기
2. Bundle Identifier: `kr.co.aimed.mobile`
3. Version: 1.0.0
4. Build: 1

#### 3. 인증서 및 프로비저닝 프로파일
1. Apple Developer Console에서 생성
2. Xcode → Signing & Capabilities 설정

#### 4. 아카이브 및 업로드
```bash
xcodebuild archive -scheme "AI-MED Korea" -archivePath build/AIMED.xcarchive
xcodebuild -exportArchive -archivePath build/AIMED.xcarchive -exportPath build -exportOptionsPlist exportOptions.plist
```

### TestFlight 배포
1. [App Store Connect](https://appstoreconnect.apple.com) 접속
2. 새 앱 생성
3. TestFlight 탭 → 빌드 업로드
4. 테스터 그룹 생성 및 초대

---

## 📦 메타데이터 및 에셋

### 필수 에셋
- **앱 아이콘**: 1024x1024px (iOS), 512x512px (Android)
- **스플래시 스크린**: 다양한 해상도
- **스크린샷**:
  - iOS: 6.7" (1290x2796), 6.1" (1179x2556)
  - Android: Phone (1080x1920 minimum)

### 앱 정보
```
앱 이름: AI-MED Korea
짧은 설명: 건강과 의료 정보를 제공하는 다국어 플랫폼
카테고리: 의료/건강
연령 등급: 4+ (모든 연령)
언어: 한국어, 영어, 중국어, 힌디어, 아랍어
```

### 개인정보 처리방침
- 수집 데이터: 설문 응답, 사용자 프로필
- 데이터 사용: 서비스 개선, 맞춤형 건강 정보 제공
- 데이터 공유: 없음

---

## 🔧 환경 변수 (.env.sample)

```env
APP_NAME="AI-MED Korea"
APP_URL=https://aimedkorea.cafe24.com
APP_ENV=production
APP_DEBUG=false

# PWA Settings
PWA_NAME="AI-MED Korea"
PWA_SHORT_NAME="AI-MED"
PWA_THEME_COLOR="#1e40af"
PWA_BACKGROUND_COLOR="#ffffff"
```

---

## 📝 릴리스 노트 (v1.0.0)

### 새로운 기능
- 다국어 지원 (5개 언어)
- 설문조사 시스템
- 12주 회복 프로그램
- PWA 오프라인 지원

### 개선사항
- 모바일 UI/UX 최적화
- 반응형 디자인 개선
- 성능 최적화

### 버그 수정
- 테이블 스크롤 문제 해결
- 다국어 번역 오류 수정

---

## 🚀 빌드 명령어 요약

### Android
```bash
# 초기 설정
npm i -g @bubblewrap/cli
bubblewrap init --manifest https://aimedkorea.cafe24.com/manifest.json

# 빌드
bubblewrap build

# 결과물: ./app-release-bundle.aab
```

### iOS
```bash
# PWABuilder 사용
npm i -g @pwabuilder/cli
pwabuilder package -p ios -m https://aimedkorea.cafe24.com/manifest.json

# 또는 Xcode에서 직접 빌드
# Product → Archive → Distribute App
```

---

## 📞 지원 연락처
- 이메일: support@aimedkorea.com
- 웹사이트: https://aimedkorea.cafe24.com
- 문서: https://github.com/wonjinparkz/aimedkorea

---

*마지막 업데이트: 2025-09-14*