<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola tampilan riwayat akses brankas bagi Admin dan User.
 */
class HistoryController extends Controller
{
    /**
     * Menampilkan halaman riwayat akses.
     * Mengambil rekapitulasi data akses (berhasil/gagal) dan merender view sesuai role pengguna.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // 1. Verifikasi Autentikasi Berdasarkan Hak Akses (Role)
        $role = session('user.role');
        
        if (!in_array($role, ['admin', 'user'])) {
            return redirect()->route('login');
        }

        // 2. Persiapan Data (Placeholder: Data realtime akan diintegrasikan di tahap selanjutnya)
        $histories = [];

        // 3. Kalkulasi Rekapitulasi Statistik Aktivitas Akses
        $totalAkses    = count($histories);
        $aksesBerhasil = collect($histories)->where('status', 'Berhasil')->count();
        $aksesGagal    = collect($histories)->where('status', 'Gagal')->count();

        // 4. Render Tampilan Sesuai dengan Role Pengguna (Dashboard User atau Admin)
        if ($role === 'user') {
            return view('user.history', compact('histories', 'totalAkses', 'aksesBerhasil', 'aksesGagal'));
        }

        return view('admin.history', compact('histories', 'totalAkses', 'aksesBerhasil', 'aksesGagal'));
    }
}