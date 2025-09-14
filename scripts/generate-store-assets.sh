#!/bin/bash

# AI-MED Korea 앱스토어 에셋 생성 스크립트

echo "🎨 AI-MED Korea 앱스토어 에셋 생성 시작..."

# 디렉토리 생성
mkdir -p public/images/store-assets/android
mkdir -p public/images/store-assets/ios
mkdir -p public/images/splash

# 1024x1024 아이콘이 있는지 확인
if [ ! -f "public/images/icons/icon-1024x1024.png" ]; then
    echo "⚠️  1024x1024 아이콘이 없습니다. 512x512에서 생성합니다..."
    if [ -f "public/images/icons/icon-512x512.png" ]; then
        convert public/images/icons/icon-512x512.png -resize 1024x1024 public/images/icons/icon-1024x1024.png
        echo "✅ 1024x1024 아이콘 생성 완료"
    else
        echo "❌ 512x512 아이콘도 없습니다. 수동으로 생성해주세요."
        exit 1
    fi
fi

# Android Play Store 에셋
echo "📱 Android 에셋 생성 중..."
cp public/images/icons/icon-512x512.png public/images/store-assets/android/icon-512x512.png

# Feature Graphic (1024x500)
convert -size 1024x500 xc:'#1e40af' \
    -gravity center -fill white -pointsize 80 -annotate +0+0 'AI-MED Korea' \
    public/images/store-assets/android/feature-graphic.png

# iOS App Store 에셋
echo "🍎 iOS 에셋 생성 중..."
cp public/images/icons/icon-1024x1024.png public/images/store-assets/ios/icon-1024x1024.png

# iOS 스플래시 스크린 (다양한 크기)
# iPhone 14 Pro Max (1290x2796)
convert -size 1290x2796 xc:'#1e40af' \
    -gravity center public/images/icons/icon-512x512.png -composite \
    public/images/splash/splash-1290x2796.png

# iPhone 14 Pro (1179x2556)
convert -size 1179x2556 xc:'#1e40af' \
    -gravity center public/images/icons/icon-512x512.png -composite \
    public/images/splash/splash-1179x2556.png

# iPad Pro 12.9" (2048x2732)
convert -size 2048x2732 xc:'#1e40af' \
    -gravity center public/images/icons/icon-512x512.png -composite \
    public/images/splash/splash-2048x2732.png

# Android 스플래시 스크린
# MDPI (320x480)
convert -size 320x480 xc:'#1e40af' \
    -gravity center public/images/icons/icon-192x192.png -composite \
    public/images/splash/splash-320x480.png

# HDPI (480x800)
convert -size 480x800 xc:'#1e40af' \
    -gravity center public/images/icons/icon-192x192.png -composite \
    public/images/splash/splash-480x800.png

# XHDPI (720x1280)
convert -size 720x1280 xc:'#1e40af' \
    -gravity center public/images/icons/icon-384x384.png -composite \
    public/images/splash/splash-720x1280.png

# XXHDPI (1080x1920)
convert -size 1080x1920 xc:'#1e40af' \
    -gravity center public/images/icons/icon-384x384.png -composite \
    public/images/splash/splash-1080x1920.png

echo "✅ 모든 에셋 생성 완료!"
echo ""
echo "📁 생성된 파일:"
echo "  - Android: public/images/store-assets/android/"
echo "  - iOS: public/images/store-assets/ios/"
echo "  - Splash: public/images/splash/"
echo ""
echo "📝 다음 단계:"
echo "  1. 생성된 에셋을 검토하세요"
echo "  2. 필요시 디자인 팀에 고품질 에셋 요청"
echo "  3. 앱스토어 업로드 시 해당 에셋 사용"