import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';


export default defineConfig({
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json', // создаст manifest.json в public/build
    },
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
});

