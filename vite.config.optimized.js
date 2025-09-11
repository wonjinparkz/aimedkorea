import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/performance.js',
                'resources/js/pwa/register.js'
            ],
            refresh: true,
        }),
        // Bundle analyzer (optional)
        process.env.ANALYZE && visualizer({
            filename: './public/build/stats.html',
            open: true,
            gzipSize: true,
            brotliSize: true,
        }),
    ],
    build: {
        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info'],
            },
        },
        // Code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'alpinejs',
                        'axios',
                    ],
                    'livewire': [
                        '@livewire',
                    ],
                },
                // Optimize chunk names
                chunkFileNames: (chunkInfo) => {
                    const facadeModuleId = chunkInfo.facadeModuleId ? chunkInfo.facadeModuleId.split('/').pop() : 'chunk';
                    return `js/${facadeModuleId}-[hash].js`;
                },
            },
        },
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 1000,
        // Generate source maps for production debugging
        sourcemap: false,
        // Asset optimization
        assetsInlineLimit: 4096, // 4kb
        // CSS code splitting
        cssCodeSplit: true,
        // Target modern browsers
        target: 'es2015',
    },
    // Server configuration
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    // Optimize dependencies
    optimizeDeps: {
        include: [
            'alpinejs',
            'axios',
        ],
        exclude: [
            '@livewire',
        ],
    },
});