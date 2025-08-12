import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/report_checkbox.js',
                'resources/js/confirm_week_closure.js',
            ],
            refresh: true,
        }),
    ],
});
