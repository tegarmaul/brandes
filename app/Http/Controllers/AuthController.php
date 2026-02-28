<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* ═══════════════════════════════════════
       TAMPILKAN FORM LOGIN
    ═══════════════════════════════════════ */
    public function showLogin()
    {
        if (session('user.role') === 'admin') {
            return redirect()->route('dashboard.admin');
        }
        if (session('user.role') === 'user') {
            return redirect()->route('dashboard.user');
        }

        return view('auth.login');
    }

    /* ═══════════════════════════════════════
       PROSES LOGIN
    ═══════════════════════════════════════ */
    public function login(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'pin'  => 'required|string',
            'role' => 'required|in:admin,user',
        ]);

        $nama = trim($request->nama);
        $role = $request->role;
        $pin  = $request->pin;

        // Cari user — nama case-insensitive, tidak filter aktif dulu
        $user = User::whereRaw('LOWER(nama) = ?', [strtolower($nama)])
                    ->where('role', $role)
                    ->first();

        // Tidak ditemukan
        if (!$user) {
            return back()
                ->withInput($request->only('nama', 'role'))
                ->withErrors(['login' => 'Nama atau role tidak ditemukan di sistem.']);
        }

        // Akun nonaktif
        if (!$user->aktif) {
            return back()
                ->withInput($request->only('nama', 'role'))
                ->withErrors(['login' => 'Akun Anda tidak aktif. Hubungi administrator.']);
        }

        // PIN salah
        if (!Hash::check($pin, $user->pin)) {
            return back()
                ->withInput($request->only('nama', 'role'))
                ->withErrors(['login' => 'PIN yang Anda masukkan salah.']);
        }

        // Simpan session
        session([
            'user.id'             => $user->id,
            'user.nama'           => $user->nama,
            'user.username'       => $user->username,
            'user.role'           => $user->role,
            'user.fingerprint_id' => $user->fingerprint_id,
        ]);

        // Redirect sesuai role
        return $user->role === 'admin'
            ? redirect()->route('dashboard.admin')
            : redirect()->route('dashboard.user');
    }

    /* ═══════════════════════════════════════
       LOGOUT
    ═══════════════════════════════════════ */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}