#!/bin/bash

# WebP 이미지 변환 스크립트
# 사용법: ./convert-to-webp.sh [directory]

# 색상 코드 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 변환할 디렉토리 설정
if [ -z "$1" ]; then
    BASE_DIR="/var/www/html/aimedkorea"
else
    BASE_DIR="$1"
fi

echo -e "${GREEN}=== WebP 이미지 변환 시작 ===${NC}"
echo -e "기본 디렉토리: ${YELLOW}$BASE_DIR${NC}"
echo ""

# 통계 변수
TOTAL_FILES=0
CONVERTED_FILES=0
SKIPPED_FILES=0
FAILED_FILES=0
TOTAL_SAVED=0

# 품질 설정
QUALITY=85
LOSSLESS_THRESHOLD=100000  # 100KB 이하는 무손실 압축

# public 디렉토리의 이미지 변환
echo -e "${YELLOW}Public 디렉토리 이미지 처리 중...${NC}"
for file in $(find "$BASE_DIR/public" -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) ! -name "*.webp" 2>/dev/null); do
    TOTAL_FILES=$((TOTAL_FILES + 1))
    
    # WebP 파일 경로 생성
    webp_file="${file%.*}.webp"
    
    # 이미 WebP 파일이 존재하는지 확인
    if [ -f "$webp_file" ]; then
        echo -e "${YELLOW}[SKIP]${NC} $(basename "$file") - WebP 파일이 이미 존재합니다"
        SKIPPED_FILES=$((SKIPPED_FILES + 1))
        continue
    fi
    
    # 파일 크기 확인
    file_size=$(stat -c%s "$file")
    
    # 작은 파일은 무손실, 큰 파일은 손실 압축
    if [ $file_size -lt $LOSSLESS_THRESHOLD ]; then
        # 무손실 압축 (아이콘 등)
        cwebp -lossless "$file" -o "$webp_file" 2>/dev/null
        compression_type="무손실"
    else
        # 손실 압축 (일반 이미지)
        cwebp -q $QUALITY "$file" -o "$webp_file" 2>/dev/null
        compression_type="손실 $QUALITY%"
    fi
    
    if [ $? -eq 0 ]; then
        # 변환 성공
        webp_size=$(stat -c%s "$webp_file")
        saved=$((file_size - webp_size))
        saved_percent=$((saved * 100 / file_size))
        TOTAL_SAVED=$((TOTAL_SAVED + saved))
        
        echo -e "${GREEN}[OK]${NC} $(basename "$file") → $(basename "$webp_file")"
        echo -e "    압축: ${compression_type}, 크기: $(($file_size/1024))KB → $(($webp_size/1024))KB (${saved_percent}% 절감)"
        CONVERTED_FILES=$((CONVERTED_FILES + 1))
    else
        echo -e "${RED}[FAIL]${NC} $(basename "$file") - 변환 실패"
        FAILED_FILES=$((FAILED_FILES + 1))
    fi
done

# storage 디렉토리의 이미지 변환
echo ""
echo -e "${YELLOW}Storage 디렉토리 이미지 처리 중...${NC}"
for file in $(find "$BASE_DIR/storage/app/public" -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) ! -name "*.webp" 2>/dev/null); do
    TOTAL_FILES=$((TOTAL_FILES + 1))
    
    # WebP 파일 경로 생성
    webp_file="${file%.*}.webp"
    
    # 이미 WebP 파일이 존재하는지 확인
    if [ -f "$webp_file" ]; then
        echo -e "${YELLOW}[SKIP]${NC} $(basename "$file") - WebP 파일이 이미 존재합니다"
        SKIPPED_FILES=$((SKIPPED_FILES + 1))
        continue
    fi
    
    # 파일 크기 확인
    file_size=$(stat -c%s "$file")
    
    # 손실 압축 (업로드된 이미지는 대부분 큰 파일)
    cwebp -q $QUALITY "$file" -o "$webp_file" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        # 변환 성공
        webp_size=$(stat -c%s "$webp_file")
        saved=$((file_size - webp_size))
        saved_percent=$((saved * 100 / file_size))
        TOTAL_SAVED=$((TOTAL_SAVED + saved))
        
        echo -e "${GREEN}[OK]${NC} $(basename "$file") → $(basename "$webp_file")"
        echo -e "    크기: $(($file_size/1024))KB → $(($webp_size/1024))KB (${saved_percent}% 절감)"
        CONVERTED_FILES=$((CONVERTED_FILES + 1))
    else
        echo -e "${RED}[FAIL]${NC} $(basename "$file") - 변환 실패"
        FAILED_FILES=$((FAILED_FILES + 1))
    fi
done

# Responsive 이미지 생성 (선택적)
echo ""
echo -e "${YELLOW}Responsive 이미지 생성 중...${NC}"

# 주요 이미지에 대해 여러 크기 생성
create_responsive_versions() {
    local original="$1"
    local sizes=(400 800 1200)
    
    for size in "${sizes[@]}"; do
        local resized="${original%.*}-${size}w.${original##*.}"
        local resized_webp="${original%.*}-${size}w.webp"
        
        if [ ! -f "$resized_webp" ]; then
            # 리사이즈 후 WebP 변환
            convert "$original" -resize "${size}x>" "$resized" 2>/dev/null
            if [ $? -eq 0 ]; then
                cwebp -q $QUALITY "$resized" -o "$resized_webp" 2>/dev/null
                if [ $? -eq 0 ]; then
                    echo -e "${GREEN}[OK]${NC} Responsive: $(basename "$resized_webp") 생성됨"
                    rm "$resized"  # 임시 리사이즈 파일 삭제
                fi
            fi
        fi
    done
}

# storage의 주요 이미지들에 대해 responsive 버전 생성
for file in $(find "$BASE_DIR/storage/app/public" -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) ! -name "*-*w.*" | head -10); do
    create_responsive_versions "$file"
done

# 심볼릭 링크 재생성 (storage 링크)
echo ""
echo -e "${YELLOW}Storage 심볼릭 링크 확인 중...${NC}"
if [ ! -L "$BASE_DIR/public/storage" ]; then
    php "$BASE_DIR/artisan" storage:link
    echo -e "${GREEN}Storage 링크 생성됨${NC}"
else
    echo -e "${GREEN}Storage 링크 이미 존재함${NC}"
fi

# 결과 요약
echo ""
echo -e "${GREEN}=== WebP 변환 완료 ===${NC}"
echo -e "전체 파일: ${TOTAL_FILES}개"
echo -e "${GREEN}변환 성공: ${CONVERTED_FILES}개${NC}"
echo -e "${YELLOW}건너뜀: ${SKIPPED_FILES}개${NC}"
echo -e "${RED}실패: ${FAILED_FILES}개${NC}"
echo -e "총 절감 용량: ${GREEN}$((TOTAL_SAVED/1024))KB${NC}"

# 권한 설정
echo ""
echo -e "${YELLOW}파일 권한 설정 중...${NC}"
find "$BASE_DIR/public" -name "*.webp" -exec chmod 644 {} \;
find "$BASE_DIR/storage/app/public" -name "*.webp" -exec chmod 644 {} \;
echo -e "${GREEN}권한 설정 완료${NC}"

echo ""
echo -e "${GREEN}스크립트 실행 완료!${NC}"
echo -e "WebP 이미지를 사용하려면 HTML/Blade 템플릿에서 <picture> 태그를 사용하세요."