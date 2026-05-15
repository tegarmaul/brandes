<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Vite;

/**
 * Service Provider Utama Aplikasi.
 * Digunakan untuk melakukan bootstrapping layanan dan konfigurasi global framework.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan (Register) layanan aplikasi apapun ke dalam Container.
     * Digunakan untuk binding interface ke implementasi.
     * 
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Inisialisasi (Bootstrap) layanan aplikasi apapun.
     * Dieksekusi setelah seluruh service provider lain terdaftar.
     * 
     * @return void
     */
    public function boot(): void
    {
        // 1. Konfigurasi Atribut Tag Style untuk Vite Asset Manager
        // Digunakan untuk memastikan pelacakan perubahan aset (Turbo Track) saat reload.
        Vite::useStyleTagAttributes([
            'data-turbo-track' => 'reload',
        ]);
    }
}
