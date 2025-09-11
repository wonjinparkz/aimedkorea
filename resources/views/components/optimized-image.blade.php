@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'lazy' => true,
    'placeholder' => true
])

@php
    // Generate WebP version URL if it's a local image
    $webpSrc = null;
    $webpPath = null;
    if ($src && !str_starts_with($src, 'http')) {
        $pathInfo = pathinfo($src);
        if (in_array(strtolower($pathInfo['extension'] ?? ''), ['jpg', 'jpeg', 'png'])) {
            $webpSrc = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            // For storage URLs, we need to check the actual storage path
            if (str_contains($src, '/storage/')) {
                $relativePath = str_replace('/storage/', '', $webpSrc);
                $webpPath = storage_path('app/public/' . $relativePath);
            } else {
                $webpPath = public_path($webpSrc);
            }
        }
    }
    
    // Generate responsive sizes
    $sizes = '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw';
    
    // Create srcset for responsive images
    $srcset = '';
    if ($src && !str_starts_with($src, 'http')) {
        $basePath = pathinfo($src, PATHINFO_DIRNAME) . '/' . pathinfo($src, PATHINFO_FILENAME);
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        
        // Check if responsive versions exist
        $responsiveSizes = [400, 800, 1200];
        $srcsetArray = [];
        foreach ($responsiveSizes as $size) {
            $responsivePath = "{$basePath}-{$size}w.{$ext}";
            if (file_exists(public_path($responsivePath))) {
                $srcsetArray[] = "{$responsivePath} {$size}w";
            }
        }
        if (!empty($srcsetArray)) {
            $srcset = implode(', ', $srcsetArray);
        }
    }
@endphp

<picture>
    @if($webpSrc && $webpPath && file_exists($webpPath))
        <source type="image/webp" 
                srcset="{{ $webpSrc }}"
                @if($srcset) data-srcset="{{ str_replace('.jpg', '.webp', str_replace('.png', '.webp', $srcset)) }}" @endif>
    @endif
    
    @if($srcset)
        <source srcset="{{ $srcset }}" sizes="{{ $sizes }}">
    @endif
    
    <img 
        @if($lazy)
            loading="lazy"
            data-src="{{ $src }}"
            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {{ $width ?? 1 }} {{ $height ?? 1 }}'%3E%3Crect width='100%25' height='100%25' fill='%23f3f4f6'/%3E%3C/svg%3E"
        @else
            src="{{ $src }}"
        @endif
        alt="{{ $alt }}"
        class="{{ $class }} @if($placeholder) skeleton @endif"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($lazy) 
            onload="this.classList.remove('skeleton'); this.src = this.dataset.src;"
            onerror="this.classList.remove('skeleton'); this.src='/images/placeholder.jpg';"
        @endif
    />
</picture>

@if($lazy)
<script>
    // Intersection Observer for lazy loading
    document.addEventListener('DOMContentLoaded', function() {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        img.classList.remove('skeleton');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            lazyImages.forEach(function(img) {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            lazyImages.forEach(function(img) {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.remove('skeleton');
                }
            });
        }
    });
</script>
@endif