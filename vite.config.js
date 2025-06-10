import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import jQuery from 'jquery';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias:{
            '$': jQuery,
        }
    },
    content: [
        "/app/Livewire/**/*.php",
        "/app/Livewire/Home.php",
        "/resources/views/navigation.blade.php"
    ],
});
