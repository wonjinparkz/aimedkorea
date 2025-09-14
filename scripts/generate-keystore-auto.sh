#!/bin/bash

# AI-MED Korea Android 키스토어 자동 생성 스크립트

echo "🔐 AI-MED Korea Android 키스토어 생성 중..."
echo "========================================="
echo ""

# 키스토어 정보 설정
KEYSTORE_DIR="./android-signing"
KEYSTORE_FILE="$KEYSTORE_DIR/upload-keystore.jks"
KEY_ALIAS="upload"
VALIDITY_DAYS=10950  # 30년

# 디렉토리 생성
mkdir -p $KEYSTORE_DIR

# 기본 정보 설정
ORG_NAME="AI-MED Korea"
ORG_UNIT="Development"
CITY="Seoul"
STATE="Seoul"
COUNTRY="KR"
KEYSTORE_PASS="AImed2025!@#"

# DN 문자열 생성
DN="CN=AI-MED Korea, OU=$ORG_UNIT, O=$ORG_NAME, L=$CITY, ST=$STATE, C=$COUNTRY"

# 키스토어 생성
keytool -genkey -v \
    -keystore "$KEYSTORE_FILE" \
    -keyalg RSA \
    -keysize 2048 \
    -validity $VALIDITY_DAYS \
    -alias $KEY_ALIAS \
    -dname "$DN" \
    -storepass "$KEYSTORE_PASS" \
    -keypass "$KEYSTORE_PASS"

if [ $? -eq 0 ]; then
    echo "✅ 키스토어 생성 완료: $KEYSTORE_FILE"
    echo ""
    
    # SHA-1 및 SHA-256 지문 추출
    echo "📋 인증서 지문 정보:"
    echo "========================================="
    
    # SHA-1 지문
    echo ""
    echo "SHA-1 Fingerprint:"
    keytool -list -v -keystore "$KEYSTORE_FILE" -alias $KEY_ALIAS -storepass "$KEYSTORE_PASS" 2>/dev/null | grep "SHA1:" | head -1
    
    # SHA-256 지문
    echo ""
    echo "SHA-256 Fingerprint:"
    keytool -list -v -keystore "$KEYSTORE_FILE" -alias $KEY_ALIAS -storepass "$KEYSTORE_PASS" 2>/dev/null | grep "SHA256:" | head -1
    
    # 키스토어 정보를 properties 파일로 저장
    cat > "$KEYSTORE_DIR/keystore.properties" << EOL
# AI-MED Korea Android Signing Configuration
# Generated: $(date)
# ⚠️ 이 파일을 안전하게 보관하고 절대 공개 저장소에 커밋하지 마세요!

storeFile=$KEYSTORE_FILE
storePassword=$KEYSTORE_PASS
keyAlias=$KEY_ALIAS
keyPassword=$KEYSTORE_PASS

# Package Information
packageName=kr.co.aimed.mobile

# Certificate Information
organization=$ORG_NAME
organizationalUnit=$ORG_UNIT
city=$CITY
state=$STATE
country=$COUNTRY
EOL
    
    # 문서화용 정보 파일 생성 (비밀번호 제외)
    cat > "$KEYSTORE_DIR/signing-info.md" << EOL
# AI-MED Korea Android 앱 서명 정보

## 키스토어 정보
- **파일명**: upload-keystore.jks
- **키 별칭**: upload
- **유효 기간**: 30년 ($(date +%Y)년 ~ $(($(date +%Y) + 30))년)
- **생성일**: $(date)

## 인증서 지문
\`\`\`
$(keytool -list -v -keystore "$KEYSTORE_FILE" -alias $KEY_ALIAS -storepass "$KEYSTORE_PASS" 2>/dev/null | grep -E "SHA1:|SHA256:")
\`\`\`

## Google Play App Signing 설정
1. Play Console에서 앱 생성
2. **Play App Signing** 활성화
3. 업로드 키로 이 키스토어 사용
4. Google이 앱 서명 키를 관리

## 보안 주의사항
- ⚠️ keystore.properties 파일은 절대 Git에 커밋하지 마세요
- ⚠️ 키스토어 파일과 비밀번호는 안전하게 보관하세요
- ✅ .gitignore에 android-signing/ 디렉토리 추가 완료

## 패키지 정보
- **패키지명**: kr.co.aimed.mobile
- **조직**: $ORG_NAME
- **국가**: $COUNTRY

## 인증서 상세 정보
\`\`\`
$(keytool -list -v -keystore "$KEYSTORE_FILE" -alias $KEY_ALIAS -storepass "$KEYSTORE_PASS" 2>/dev/null | head -30)
\`\`\`
EOL
    
    echo ""
    echo "========================================="
    echo "✅ 모든 파일이 생성되었습니다:"
    echo "  - 키스토어: $KEYSTORE_FILE"
    echo "  - 설정 파일: $KEYSTORE_DIR/keystore.properties"
    echo "  - 문서: $KEYSTORE_DIR/signing-info.md"
    echo ""
    echo "⚠️  중요: 비밀번호는 AImed2025!@# 입니다."
    echo "⚠️  이 정보를 안전하게 보관하세요!"
    echo ""
else
    echo "❌ 키스토어 생성 실패"
    exit 1
fi