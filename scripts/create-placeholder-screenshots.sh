#!/bin/bash

# Create placeholder screenshots for PWA
echo "Creating placeholder screenshots for PWA..."

# Create screenshots directory if it doesn't exist
mkdir -p /var/www/html/aimedkorea/public/images/screenshots

# Mobile screenshots (1080x1920)
convert -size 1080x1920 xc:'#1e40af' \
  -gravity center -fill white -pointsize 60 \
  -annotate +0+0 'AI-MED Korea\n홈 화면' \
  /var/www/html/aimedkorea/public/images/screenshots/mobile-home.png

convert -size 1080x1920 xc:'#059669' \
  -gravity center -fill white -pointsize 60 \
  -annotate +0+0 'AI-MED Korea\n셀프체크' \
  /var/www/html/aimedkorea/public/images/screenshots/mobile-survey.png

convert -size 1080x1920 xc:'#dc2626' \
  -gravity center -fill white -pointsize 60 \
  -annotate +0+0 'AI-MED Korea\n결과 화면' \
  /var/www/html/aimedkorea/public/images/screenshots/mobile-results.png

convert -size 1080x1920 xc:'#7c3aed' \
  -gravity center -fill white -pointsize 60 \
  -annotate +0+0 'AI-MED Korea\n대시보드' \
  /var/www/html/aimedkorea/public/images/screenshots/mobile-dashboard.png

# Desktop screenshots (1920x1080)
convert -size 1920x1080 xc:'#1e40af' \
  -gravity center -fill white -pointsize 80 \
  -annotate +0+0 'AI-MED Korea Desktop\n홈 화면' \
  /var/www/html/aimedkorea/public/images/screenshots/desktop-home.png

convert -size 1920x1080 xc:'#059669' \
  -gravity center -fill white -pointsize 80 \
  -annotate +0+0 'AI-MED Korea Desktop\n설문 화면' \
  /var/www/html/aimedkorea/public/images/screenshots/desktop-survey.png

convert -size 1920x1080 xc:'#7c3aed' \
  -gravity center -fill white -pointsize 80 \
  -annotate +0+0 'AI-MED Korea Desktop\n대시보드' \
  /var/www/html/aimedkorea/public/images/screenshots/desktop-dashboard.png

# Widget screenshot (600x400)
convert -size 600x400 xc:'#fbbf24' \
  -gravity center -fill black -pointsize 40 \
  -annotate +0+0 'Health Widget' \
  /var/www/html/aimedkorea/public/images/screenshots/widget.png

# Widget icon
mkdir -p /var/www/html/aimedkorea/public/images/icons
convert -size 512x512 xc:'#1e40af' \
  -gravity center -fill white -pointsize 120 \
  -annotate +0+0 'W' \
  /var/www/html/aimedkorea/public/images/icons/widget-icon.png

echo "Screenshots created successfully!"
ls -la /var/www/html/aimedkorea/public/images/screenshots/