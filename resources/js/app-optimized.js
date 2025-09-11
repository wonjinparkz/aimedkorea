// Lazy load axios only when needed
let axiosInstance = null;

function getAxios() {
    if (!axiosInstance) {
        return import('axios').then(module => {
            axiosInstance = module.default;
            axiosInstance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            
            // Add CSRF token if available
            const token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                axiosInstance.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
            }
            
            window.axios = axiosInstance;
            return axiosInstance;
        });
    }
    return Promise.resolve(axiosInstance);
}

// Expose axios getter globally
window.getAxios = getAxios;

// Import performance optimizations
import { initPerformanceOptimizations } from './performance';

// Initialize performance optimizations
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPerformanceOptimizations);
} else {
    initPerformanceOptimizations();
}

// Lazy load Alpine.js for interactive components
if (document.querySelector('[x-data]')) {
    import('alpinejs').then(module => {
        window.Alpine = module.default;
        window.Alpine.start();
    });
}

// Progressive enhancement for forms
document.addEventListener('DOMContentLoaded', () => {
    // Add loading states to forms
    const forms = document.querySelectorAll('form[data-loading]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-wait');
                
                const originalText = submitButton.textContent;
                submitButton.textContent = form.dataset.loading || 'Processing...';
                
                // Re-enable after timeout (fallback)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-wait');
                    submitButton.textContent = originalText;
                }, 30000);
            }
        });
    });
});

// Error handling
window.addEventListener('error', (e) => {
    // Log errors in development
    if (import.meta.env.DEV) {
        console.error('Global error:', e);
    }
    
    // Send errors to monitoring service in production
    if (import.meta.env.PROD && window.errorReporter) {
        window.errorReporter.log(e);
    }
});

// Performance monitoring
if ('PerformanceObserver' in window) {
    // Monitor Largest Contentful Paint
    try {
        const lcpObserver = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            const lastEntry = entries[entries.length - 1];
            
            // Log LCP for monitoring
            if (import.meta.env.DEV) {
                console.log('LCP:', lastEntry.renderTime || lastEntry.loadTime);
            }
        });
        lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });
    } catch (e) {
        // PerformanceObserver not supported for this entry type
    }
    
    // Monitor Cumulative Layout Shift
    try {
        let clsValue = 0;
        const clsObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            
            // Log CLS for monitoring
            if (import.meta.env.DEV) {
                console.log('CLS:', clsValue);
            }
        });
        clsObserver.observe({ type: 'layout-shift', buffered: true });
    } catch (e) {
        // PerformanceObserver not supported for this entry type
    }
}