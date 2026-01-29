import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/dashboard-responsive.css',
                'resources/js/app.js'
            ],
            refresh: true,
            buildDirectory: 'build', // Build directly to 'build' folder (not 'public/build')
        }),
    ],
    build: {
        manifest: 'manifest.json',
        outDir: 'build', // Changed from 'public/build' to 'build'
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
