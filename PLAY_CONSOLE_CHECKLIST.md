# Google Play Console 제출 체크리스트

## ✅ 앱 서명 준비

### 1. 키스토어 생성
```bash
./scripts/generate-keystore.sh
```

### 2. 업로드 키 정보
- [ ] 키스토어 파일: `android-signing/upload-keystore.jks`
- [ ] 키 별칭: `upload`
- [ ] SHA-1 지문 확인
- [ ] SHA-256 지문 확인

### 3. Google Play App Signing
- [ ] Play Console에서 App Signing 활성화
- [ ] 업로드 키로 첫 번째 AAB 서명
- [ ] 업로드 인증서 지문 문서화

---

## 📋 데이터 세이프티 섹션

### 데이터 수집
- [x] **개인 정보**
  - 이름 (필수)
  - 이메일 주소 (필수)
  - 사용자명 (선택)

- [x] **건강 및 피트니스**
  - 건강 정보
  - 설문 응답

- [x] **앱 활동**
  - 앱 상호작용
  - 인앱 검색 기록: ❌ 수집 안 함

- [ ] **기기 또는 기타 ID**: ❌ 수집 안 함

### 데이터 공유
- [ ] 제3자와 데이터 공유: ❌ 없음
- [ ] 데이터 판매: ❌ 없음

### 데이터 보안
- [x] 전송 중 암호화 (HTTPS)
- [x] 사용자가 데이터 삭제 요청 가능
- [x] Play 가족 정책 준수

---

## 🔐 앱 권한

### AndroidManifest.xml 권한
```xml
<!-- 필수 권한 -->
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

<!-- 선택 권한 -->
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.POST_NOTIFICATIONS" />
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
```

### 권한 사용 설명
| 권한 | 설명 | 스크린샷 필요 |
|-----|------|-------------|
| 인터넷 | PWA 기능, 서버 통신 | ❌ |
| 카메라 | 프로필 사진 촬영 | ✅ |
| 알림 | 12주 프로그램 리마인더 | ✅ |
| 위치 | 가까운 의료 시설 찾기 | ✅ |

---

## 📱 앱 정보

### 기본 정보
- **패키지명**: `kr.co.aimed.mobile`
- **앱 이름**: AI-MED Korea
- **짧은 설명** (80자):
  ```
  건강과 의료 정보를 제공하는 다국어 플랫폼
  ```
- **전체 설명** (4000자): [별도 문서 참조]

### 카테고리 및 태그
- **카테고리**: 의료
- **태그**: 
  - 건강
  - 의료
  - 설문조사
  - 웰니스
  - 다국어

### 연락처 정보
- **이메일**: support@aimedkorea.com
- **웹사이트**: https://aimedkorea.cafe24.com
- **개인정보처리방침**: https://aimedkorea.cafe24.com/privacy
- **전화번호**: +82-2-XXXX-XXXX

---

## 🖼️ 스토어 등록 에셋

### 필수 그래픽
- [ ] **앱 아이콘**: 512x512px PNG
- [ ] **Feature Graphic**: 1024x500px JPG/PNG

### 스크린샷 (최소 2개, 최대 8개)
- [ ] **휴대전화**: 최소 320px, 최대 3840px
  - 메인 화면
  - 설문조사 화면
  - 결과 화면
  - 12주 프로그램
  - 다국어 전환

### 선택 그래픽
- [ ] **프로모션 그래픽**: 180x120px JPG/PNG
- [ ] **TV 배너**: 1280x720px JPG/PNG

---

## 🌍 현지화

### 지원 언어
- [x] 한국어 (기본)
- [x] 영어
- [x] 중국어 (간체)
- [x] 힌디어
- [x] 아랍어

### 언어별 준비 사항
- [ ] 앱 이름 번역
- [ ] 설명 번역
- [ ] 스크린샷 현지화
- [ ] 릴리스 노트 번역

---

## 📊 앱 콘텐츠

### 콘텐츠 등급
- **연령 등급**: 모든 연령
- **대상 연령층**: 13세 이상
- **콘텐츠 등급 설문조사 완료**: [ ]

### 광고
- **광고 포함**: ❌ 아니오
- **광고 ID 사용**: ❌ 아니오

---

## 🚀 출시 준비

### 테스트
- [ ] 내부 테스트 트랙 설정
- [ ] 테스터 이메일 목록 준비 (최대 100명)
- [ ] 테스트 피드백 양식 준비

### 가격 및 배포
- **가격**: 무료
- **국가/지역**: 전 세계
- **기기 호환성**: 
  - Android 4.4 (API 19) 이상
  - 휴대전화 및 태블릿

### 릴리스 노트
```
버전 1.0.0 - 첫 출시
• 다국어 지원 (5개 언어)
• 건강 설문조사 시스템
• 12주 웰니스 프로그램
• PWA 오프라인 지원
• 개인 맞춤형 건강 분석
```

---

## 📝 최종 체크리스트

### 제출 전 확인
- [ ] AAB 파일 생성 및 서명
- [ ] ProGuard 규칙 설정
- [ ] 버전 코드/이름 확인
- [ ] 모든 필수 정보 입력
- [ ] 스크린샷 업로드
- [ ] 개인정보처리방침 URL 확인
- [ ] 데이터 세이프티 섹션 완료
- [ ] 콘텐츠 등급 설문조사 완료

### 제출 후
- [ ] 내부 테스트 시작
- [ ] 테스터 초대 발송
- [ ] 피드백 수집
- [ ] 버그 수정 및 업데이트

---

*최종 업데이트: 2025-09-14*