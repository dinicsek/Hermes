import {defineConfig} from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin'

export default defineConfig({
    server: {
        host: '0.0.0.0'
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css', 'resources/css/filament/manager/theme.css'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'app/Filament/**',
                'app/Providers/Filament/**',
                'resources/css/filament/**',
                'resources/views/**'
            ],
        }),
    ],
});
