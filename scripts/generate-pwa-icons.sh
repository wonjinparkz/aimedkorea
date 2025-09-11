#!/bin/bash

# PWA Icon Generator Script
# This script generates all required PWA icons from the placeholder SVG

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null; then
    echo "ImageMagick is not installed. Installing..."
    apt-get update && apt-get install -y imagemagick
fi

# Source SVG file
SOURCE_SVG="/var/www/html/aimedkorea/public/images/icons/icon-placeholder.svg"
ICON_DIR="/var/www/html/aimedkorea/public/images/icons"

# Create icon directory if it doesn't exist
mkdir -p "$ICON_DIR"

echo "Generating PWA icons..."

# Generate PNG icons of various sizes
sizes=(72 96 128 144 152 192 384 512)
for size in "${sizes[@]}"; do
    echo "Generating ${size}x${size} icon..."
    convert -background none "$SOURCE_SVG" -resize ${size}x${size} "$ICON_DIR/icon-${size}x${size}.png"
done

# Generate Apple touch icon
echo "Generating Apple touch icon..."
convert -background none "$SOURCE_SVG" -resize 180x180 "$ICON_DIR/apple-touch-icon.png"

# Generate favicon sizes
echo "Generating favicons..."
convert -background none "$SOURCE_SVG" -resize 16x16 "/var/www/html/aimedkorea/public/favicon-16x16.png"
convert -background none "$SOURCE_SVG" -resize 32x32 "/var/www/html/aimedkorea/public/favicon-32x32.png"
convert -background none "$SOURCE_SVG" -resize 16x16 "/var/www/html/aimedkorea/public/favicon-16x16.png" -define icon:auto-resize=16 "/var/www/html/aimedkorea/public/favicon.ico"

# Generate badge icon (for notifications)
echo "Generating badge icon..."
convert -background none "$SOURCE_SVG" -resize 72x72 "$ICON_DIR/badge-72x72.png"

# Generate maskable icons (with padding for Android adaptive icons)
echo "Generating maskable icons..."
for size in 192 512; do
    # Add 20% padding for maskable icons
    inner_size=$(( size * 80 / 100 ))
    convert -background white -gravity center "$SOURCE_SVG" -resize ${inner_size}x${inner_size} -extent ${size}x${size} "$ICON_DIR/icon-maskable-${size}x${size}.png"
done

echo "Icon generation complete!"

# List generated files
echo -e "\nGenerated files:"
ls -la "$ICON_DIR"/*.png
ls -la /var/www/html/aimedkorea/public/favicon*