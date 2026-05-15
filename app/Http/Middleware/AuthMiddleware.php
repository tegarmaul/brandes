<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\User;

/**
 * Middleware untuk memastikan pengguna telah terautentikasi (Login).
 * Serta memverifikasi status keaktifan akun secara real-time dari database.
 */
class AuthMiddleware
{
    /**
     * Menangani permintaan (request) yang masuk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        // 1. Verifikasi Keberadaan Sesi (Memastikan pengguna sudah login)
        $userId = session('user.id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        // 2. Verifikasi Status Keaktifan Akun Secara Real-time
        // Memastikan akun masih tersedia di database dan dalam status 'aktif'
        $user = User::find($userId);
        
        if (!$user || !$user->aktif) {
            // Hancurkan sesi secara paksa jika akun dinonaktifkan oleh administrator
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.'
            ]);
        }

        // 3. Verifikasi Hak Akses (Role) Jika Parameter Role Ditentukan
        if ($role && session('user.role') !== $role) {
            // Redirect otomatis ke dashboard yang sesuai jika mencoba melompati role lain
            if (session('user.role') === 'admin') {
                return redirect()->route('dashboard.admin');
            }

            return redirect()->route('dashboard.user');
        }

        // 4. Lanjutkan Permintaan jika seluruh verifikasi lolos
        return $next($request);
    }
}
