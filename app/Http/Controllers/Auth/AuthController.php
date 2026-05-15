<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Controller untuk menangani proses autentikasi (Login & Logout) pengguna.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     * Jika pengguna sudah login, arahkan ke dashboard sesuai role masing-masing.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLogin()
    {
        // 1. Cek Sesi Admin (Auto-Redirect jika sudah login)
        if (session('user.role') === 'admin') {
            return redirect()->route('dashboard.admin');
        }

        // 2. Cek Sesi User (Auto-Redirect jika sudah login)
        if (session('user.role') === 'user') {
            return redirect()->route('dashboard.user');
        }

        // 3. Tampilkan View Login Utama
        return view('login.login');
    }

    /**
     * Memproses percobaan login pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Validasi Input Login
        $request->validate([
            'username' => 'required|string',
            'pin'      => 'required|string',
            'role'     => 'required|in:admin,user',
        ]);

        $username = trim($request->username);
        $role     = $request->role;
        $pin      = $request->pin;

        // 2. Cari User Berdasarkan Username (Case-Insensitive) dan Role
        $user = User::whereRaw('LOWER(username) = ?', [strtolower($username)])
            ->where('role', $role)
            ->first();

        // 3. Verifikasi Keberadaan User dalam Database
        if (!$user) {
            return back()
                ->withInput($request->only('username', 'role'))
                ->withErrors(['login' => 'Username atau role tidak ditemukan di sistem.']);
        }

        // 4. Verifikasi Status Keaktifan Akun
        if (!$user->aktif) {
            return back()
                ->withInput($request->only('username', 'role'))
                ->withErrors(['login' => 'Akun Anda tidak aktif. Hubungi administrator.']);
        }

        // 5. Verifikasi PIN — Mendukung dua format:
        //    a) AES Encryption (data baru): dekripsi lalu bandingkan
        //    b) Bcrypt Hash (data lama): gunakan Hash::check() sebagai fallback
        $pinValid = false;
        try {
            // Coba dekripsi (untuk PIN yang disimpan dengan Crypt::encryptString)
            $pinValid = (Crypt::decryptString($user->pin) === $pin);
        } catch (DecryptException $e) {
            // Jika dekripsi gagal, berarti PIN lama disimpan dengan bcrypt
            $pinValid = Hash::check($pin, $user->pin);
        }

        if (!$pinValid) {
            return back()
                ->withInput($request->only('username', 'role'))
                ->withErrors(['login' => 'PIN yang Anda masukkan salah.']);
        }

        // 6. Simpan Data Identitas ke Dalam Sesi
        session([
            'user.id'             => $user->id,
            'user.nama'           => $user->nama,
            'user.username'       => $user->username,
            'user.role'           => $user->role,
            'user.fingerprint_id' => $user->fingerprint_id,
            'user.is_super_admin' => $user->is_super_admin,
        ]);

        // 7. Redireksi ke Dashboard Sesuai Hak Akses (Role)
        return $user->role === 'admin'
            ? redirect()->route('dashboard.admin')
            : redirect()->route('dashboard.user');
    }

    /**
     * Memproses pengakhiran sesi (Logout) pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // 1. Hancurkan Data Sesi dan Perbarui Token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Kembali ke Halaman Login Utama
        return redirect('/login');
    }
}
