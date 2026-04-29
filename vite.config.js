import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Чистые sourcemaps для продакшна (не раскрывают исходный код)
        sourcemap: false,
        // Оптимизация для продакшна
        minify: 'esbuild',
    },
});
