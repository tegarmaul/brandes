import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/**
 * Sistem ini tidak menggunakan Vite untuk bundling CSS/JS.
 * Semua CSS ditulis inline di masing-masing Blade view.
 * File JS publik berada di public/js/ dan di-load langsung via asset().
 *
 * Konfigurasi ini dibiarkan minimal agar tidak error jika `npm run dev` dijalankan.
 */
export default defineConfig({
    plugins: [
        laravel({
            input: [],
            refresh: false,
        }),
    ],
});
