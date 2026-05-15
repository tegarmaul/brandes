<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotifikasiKeamanan;
use Illuminate\Support\Facades\Session;

/**
 * Controller untuk mengelola daftar notifikasi keamanan bagi Admin dan User.
 * Menangani filter data berdasarkan role dan mapping tipe notifikasi ke format UI.
 */
class NotifikasiController extends Controller
{
    /**
     * Menampilkan halaman daftar notifikasi.
     * Mengambil data dari database, memproses mapping tipe, dan menghitung rekapitulasi statistik.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // 1. Verifikasi Autentikasi & Hak Akses
        $role = session('user.role');
        $userId = session('user.id');

        if (!in_array($role, ['admin', 'user'])) {
            return redirect()->route('login');
        }

        // 2. Build Query (Dikosongkan sementara sampai IoT terhubung)
        // $query = NotifikasiKeamanan::orderBy('waktu', 'desc');

        // if ($role === 'user') {
        //     $query->where(function ($q) use ($userId) {
        //         $q->where('user_id', $userId)
        //           ->orWhereNull('user_id'); // Notifikasi sistem umum (global)
        //     });
        // }

        $rawNotifikasi = collect([]); // $query->get();

        // 3. Mapping Data (Tipe database ke Tipe Antarmuka/UI)
        // mapping: warning -> peringatan, danger -> kritis, success -> akses
        $notifikasi = $rawNotifikasi->map(function ($notif) {
            $uiType = 'akses';

            if ($notif->tipe === 'warning') {
                $uiType = 'peringatan';
            } elseif ($notif->tipe === 'danger') {
                $uiType = 'kritis';
            } elseif ($notif->tipe === 'success') {
                $uiType = 'akses';
            }

            return [
                'id' => $notif->id,
                'tipe' => $uiType,
                'judul' => $notif->judul,
                'deskripsi' => $notif->pesan,
                'meta' => [], // Placeholder jika di masa depan ada metadata tambahan
                'waktu' => $notif->waktu->format('Y-m-d H:i:s'),
                'dibaca' => $notif->dibaca,
            ];
        });

        // 4. Kalkulasi Statistik Notifikasi untuk Filter Tab di View
        $totalNotifikasi = $notifikasi->count();
        $totalPeringatan = $notifikasi->where('tipe', 'peringatan')->count();
        $totalKritis = $notifikasi->where('tipe', 'kritis')->count();
        $totalAkses = $notifikasi->where('tipe', 'akses')->count();

        // 5. Tentukan View Berdasarkan Role & Render Tampilan
        $view = ($role === 'admin') ? 'admin.notifikasi' : 'user.notifikasi';

        return view($view, compact(
            'notifikasi',
            'totalNotifikasi',
            'totalPeringatan',
            'totalKritis',
            'totalAkses'
        ));
    }

    /**
     * Mengunduh rekap notifikasi dalam format CSV.
     */
    public function download()
    {
        $role   = session('user.role');
        $userId = session('user.id');

        $filename = "rekap-notifikasi-" . date('Ymd-His') . ".csv";
        $headers  = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Header Kolom CSV
        $columns = ['ID', 'Tipe', 'Judul', 'Pesan', 'Waktu'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Dummy Data untuk pengujian (bisa diganti query database nanti)
            fputcsv($file, ['1', 'Akses', 'Simulasi Rekap', 'Data riwayat akses berhasil diunduh dari sistem Brandes.', date('Y-m-d H:i:s')]);
            fputcsv($file, ['2', 'Peringatan', 'Keamanan Sistem', 'Pencatatan aktivitas sistem terpantau normal.', date('Y-m-d H:i:s')]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
