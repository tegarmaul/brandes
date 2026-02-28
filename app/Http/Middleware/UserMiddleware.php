<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    /**
     * Pastikan user yang mengakses adalah user biasa (bukan admin).
     * Gunakan: middleware('role.user')
     */
    public function handle(Request $request, Closure $next)
    {
        // Belum login
        if (!session('user.id')) {
            return redirect()->route('login');
        }

        // Sudah login tapi bukan user biasa
        if (session('user.role') !== 'user') {
            return redirect()->route('dashboard.admin')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
