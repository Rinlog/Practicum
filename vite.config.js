import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import jQuery from 'jquery';
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/scss/app.scss', 
                'resources/js/app.js', 
                'resources/css/daterangepicker.css',
                'resources/js/daterangepicker.js',
                'resources/css/home.css',
                'resources/css/navigation.css',
                'resources/js/ComponentJS/navigation.js',
                'resources/js/ComponentJS/alertJS.js',
                'resources/js/ComponentJS/FilterJS.js',
                'resources/js/ComponentJS/usercontrolnav.js',

            ],
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
