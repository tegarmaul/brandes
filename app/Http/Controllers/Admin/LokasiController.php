<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\LokasiBrankas;
use App\Models\LokasiHistory;

/**
 * Controller untuk mengelola data lokasi dan riwayat pergerakan brankas.
 */
class LokasiController extends Controller
{
    /**
     * Menampilkan halaman monitoring lokasi brankas.
     * Mengambil data brankas aktif dan riwayat pergerakannya.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // 1. Verifikasi Autentikasi (Hanya sesi user yang diizinkan mengakses)
        if (!session('user')) {
            return redirect()->route('login');
        }

        // 2. Inisialisasi Variabel Default
        $brankases = collect();
        $brankas   = null;

        // 3. Ambil Data Brankas yang Berstatus Aktif
        try {
            $brankases = LokasiBrankas::where('aktif', true)->get();
            $brankas   = $brankases->first();
        } catch (\Exception $e) {
            // Penanganan error jika data brankas gagal diambil
        }

        // 4. Ambil Data Riwayat Lokasi (Jika tabel tersedia di database)
        $histories = collect();

        if (Schema::hasTable('lokasi_history')) {
            try {
                $histories = LokasiHistory::with('brankas')
                    ->when($brankas, fn($q) => $q->where('lokasi_brankas_id', $brankas->id))
                    ->orderByDesc('recorded_at')
                    ->limit(50)
                    ->get();
            } catch (\Exception $e) {
                // Penanganan error jika riwayat gagal diambil
            }
        }

        // 5. Render Tampilan dengan Data Pendukung
        return view('admin.lokasi', compact('brankases', 'brankas', 'histories'));
    }
}
