<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    /**
     * Cek apakah user sudah login.
     * Gunakan: middleware('auth.brandes')
     */
    public function handle(Request $request, Closure $next, string $role = null)
    {
        // Belum login
        if (!session('user.id')) {
            return redirect()->route('login');
        }

        // Kalau ada role yang diminta, cek role-nya
        if ($role && session('user.role') !== $role) {
            // Redirect ke dashboard sesuai role yang dimiliki
            if (session('user.role') === 'admin') {
                return redirect()->route('dashboard.admin');
            }
            return redirect()->route('dashboard.user');
        }

        return $next($request);
    }
}