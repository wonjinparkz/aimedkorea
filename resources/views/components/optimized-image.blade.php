@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'loading' => 'lazy',
    'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw',
    'fetchpriority' => 'auto'
])

@php
    $webpSrc = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $src);
    $hasWebp = file_exists(public_path($webpSrc));
@endphp

<picture>
    @if($hasWebp)
    <source type="image/webp" srcset="{{ $webpSrc }}" sizes="{{ $sizes }}">
    @endif
    <img 
        src="{{ $src }}" 
        alt="{{ $alt }}" 
        class="{{ $class }}"
        loading="{{ $loading }}"
        fetchpriority="{{ $fetchpriority }}"
        decoding="async"
        @if($loading === 'lazy')
        data-src="{{ $src }}"
        @endif
    >
</picture>