// Performance optimization utilities

// Debounce function for scroll/resize events
export function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Lazy load images with IntersectionObserver
export function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    img.classList.remove('skeleton');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '100px 0px',
            threshold: 0.01
        });

        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            img.classList.remove('skeleton');
        });
    }
}

// Preload critical resources
export function preloadCriticalResources() {
    const criticalResources = [
        { href: '/images/hero-bg.jpg', as: 'image' },
        { href: '/css/critical.css', as: 'style' }
    ];

    criticalResources.forEach(resource => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.href = resource.href;
        link.as = resource.as;
        if (resource.as === 'font') {
            link.crossOrigin = 'anonymous';
        }
        document.head.appendChild(link);
    });
}

// Defer non-critical JavaScript
export function deferNonCriticalJS() {
    const scripts = [
        '/js/analytics.js',
        '/js/social-share.js',
        '/js/comments.js'
    ];

    window.addEventListener('load', () => {
        scripts.forEach(src => {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            document.body.appendChild(script);
        });
    });
}

// Resource hints for faster navigation
export function addResourceHints() {
    // Prefetch likely next pages
    const prefetchUrls = [
        '/about',
        '/blog',
        '/contact'
    ];

    if ('requestIdleCallback' in window) {
        requestIdleCallback(() => {
            prefetchUrls.forEach(url => {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = url;
                document.head.appendChild(link);
            });
        });
    }
}

// Initialize performance optimizations
export function initPerformanceOptimizations() {
    // Run critical optimizations immediately
    lazyLoadImages();
    
    // Defer non-critical optimizations
    if ('requestIdleCallback' in window) {
        requestIdleCallback(() => {
            preloadCriticalResources();
            deferNonCriticalJS();
            addResourceHints();
        });
    } else {
        setTimeout(() => {
            preloadCriticalResources();
            deferNonCriticalJS();
            addResourceHints();
        }, 1);
    }
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPerformanceOptimizations);
} else {
    initPerformanceOptimizations();
}