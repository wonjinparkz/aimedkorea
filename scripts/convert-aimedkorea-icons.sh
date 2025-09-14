#!/bin/bash

# Convert aimedkorea_icon.png to all required PWA icon sizes
echo "Converting aimedkorea_icon.png to all PWA icon sizes..."

SOURCE_ICON="/var/www/html/aimedkorea/public/images/icons/aimedkorea_icon.png"
ICON_DIR="/var/www/html/aimedkorea/public/images/icons"

# Check if source icon exists
if [ ! -f "$SOURCE_ICON" ]; then
    echo "Error: Source icon not found at $SOURCE_ICON"
    exit 1
fi

# Array of sizes needed for PWA
SIZES=(16 32 48 72 96 128 144 152 180 192 384 512)

# Generate each icon size
for size in "${SIZES[@]}"; do
    OUTPUT_FILE="$ICON_DIR/icon-${size}x${size}.png"
    echo "Creating ${size}x${size} icon..."
    convert "$SOURCE_ICON" -resize ${size}x${size} "$OUTPUT_FILE"
done

# Generate Apple Touch Icon (180x180)
echo "Creating Apple Touch Icon..."
convert "$SOURCE_ICON" -resize 180x180 "$ICON_DIR/apple-touch-icon.png"

# Generate favicon sizes
echo "Creating favicon-32x32.png..."
convert "$SOURCE_ICON" -resize 32x32 "/var/www/html/aimedkorea/public/favicon-32x32.png"

echo "Creating favicon-16x16.png..."
convert "$SOURCE_ICON" -resize 16x16 "/var/www/html/aimedkorea/public/favicon-16x16.png"

# Generate ICO file with multiple sizes
echo "Creating favicon.ico..."
convert "$SOURCE_ICON" -resize 16x16 -resize 32x32 -resize 48x48 -colors 256 "/var/www/html/aimedkorea/public/favicon.ico"

# Generate maskable icons (with padding for safe area)
echo "Creating maskable icons..."
# For maskable icons, we add 20% padding
for size in 192 512; do
    OUTPUT_FILE="$ICON_DIR/icon-maskable-${size}x${size}.png"
    echo "Creating ${size}x${size} maskable icon..."
    # Calculate the safe area (80% of total size)
    safe_area=$((size * 80 / 100))
    # Create a white background and composite the icon centered with padding
    convert -size ${size}x${size} xc:white \
            \( "$SOURCE_ICON" -resize ${safe_area}x${safe_area} \) \
            -gravity center -composite \
            "$OUTPUT_FILE"
done

# Generate widget icon
echo "Creating widget icon..."
convert "$SOURCE_ICON" -resize 512x512 "$ICON_DIR/widget-icon.png"

# Generate Microsoft Tile Images
echo "Creating Microsoft tile images..."
convert "$SOURCE_ICON" -resize 144x144 "$ICON_DIR/mstile-144x144.png"
convert "$SOURCE_ICON" -resize 150x150 "$ICON_DIR/mstile-150x150.png"
convert "$SOURCE_ICON" -resize 310x310 "$ICON_DIR/mstile-310x310.png"

# Generate Safari Pinned Tab SVG (simplified monochrome version)
echo "Creating Safari pinned tab icon..."
convert "$SOURCE_ICON" -resize 512x512 -monochrome "$ICON_DIR/safari-pinned-tab.svg"

echo "Icon conversion complete!"
echo "Generated icons:"
ls -la $ICON_DIR/*.png | grep -E "icon-[0-9]+x[0-9]+\.png|apple-touch-icon|mstile|widget-icon|maskable"
ls -la /var/www/html/aimedkorea/public/favicon*