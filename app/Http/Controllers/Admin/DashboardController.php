<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models & Libraries
use App\Models\LokasiBrankas;
use App\Models\HistoryAkses;
use App\Models\NotifikasiKeamanan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama Dashboard Admin.
     * 
     * Berfungsi untuk menghitung dan merangkum statistik penggunaan harian, 
     * status brankas real-time, serta menampilkan log aktivitas terbaru.
     */
    public function index()
    {
        // 1. Dapatkan Data Master Brankas
        $brankas = LokasiBrankas::first();

        // 2. Kalkulasi Statistik Aktivitas (Perbandingan Hari Ini vs Kemarin)
        $aksesHariIni = HistoryAkses::whereDate('waktu', Carbon::today())->count();
        $aksesKemarin = HistoryAkses::whereDate('waktu', Carbon::yesterday())->count();
        $diffAkses = $aksesHariIni - $aksesKemarin;

        $statAkses = [
            'total' => $aksesHariIni,
            'class' => $diffAkses >= 0 ? 'up' : 'down',
            'trend' => $aksesKemarin > 0 ? round((abs($diffAkses) / $aksesKemarin) * 100) : ($aksesHariIni > 0 ? 100 : 0),
            'label' => $aksesHariIni > 0 ? ($diffAkses >= 0 ? 'naik aksesnya' : 'turun aksesnya') : 'tidak ada akses hari ini'
        ];

        // 3. Kalkulasi Statistik Notifikasi Keamanan (Perbandingan Hari Ini vs Kemarin)
        $notifHariIni = NotifikasiKeamanan::whereDate('created_at', Carbon::today())->count();
        $notifKemarin = NotifikasiKeamanan::whereDate('created_at', Carbon::yesterday())->count();
        $diffNotif = $notifHariIni - $notifKemarin;

        $statNotif = [
            'total' => $notifHariIni,
            'class' => $diffNotif >= 0 ? 'up' : 'down',
            'trend' => $notifKemarin > 0 ? round((abs($diffNotif) / $notifKemarin) * 100) : ($notifHariIni > 0 ? 100 : 0),
            'label' => $notifHariIni > 0 ? ($diffNotif >= 0 ? 'naik notifikasinya' : 'turun notifikasinya') : 'tidak ada notifikasi akses hari ini'
        ];

        // 4. Identifikasi Aktivitas Terakhir (Siapa dan Kapan)
        $terakhir = HistoryAkses::with('user')->latest('waktu')->whereDate('waktu', Carbon::today())->first();
        $lastAkses = [
            'name' => $terakhir ? ($terakhir->user->nama ?? ($terakhir->nama ?? 'Unknown')) : '-',
            'label' => $terakhir ? (method_exists($terakhir->waktu, 'diffForHumans') ? $terakhir->waktu->diffForHumans() : Carbon::parse($terakhir->waktu)->diffForHumans()) : 'belum ada peng akses'
        ];

        // 5. Muat Data Riwayat & Notifikasi Terbaru (Dikosongkan sementara sampai IoT terhubung)
        $histories = collect([]); // HistoryAkses::with('user')->latest('waktu')->limit(5)->get();
        
        $rawNotifications = collect([]); // NotifikasiKeamanan::with('user')->latest('waktu')->limit(5)->get();
        $notifications = $rawNotifications->map(function ($notif) {
            $uiType = 'akses';
            if ($notif->tipe === 'warning') {
                $uiType = 'peringatan';
            } elseif ($notif->tipe === 'danger') {
                $uiType = 'kritis';
            } elseif ($notif->tipe === 'success') {
                $uiType = 'akses';
            }

            return [
                'tipe'      => $uiType,
                'judul'     => $notif->judul,
                'deskripsi' => $notif->pesan,
                'waktu'     => $notif->waktu,
                'meta'      => $notif->user_id ? [
                    $notif->user->fingerprint_id ?? 'FP-XXX',
                    $notif->user->pin ?? 'XXXXXX',
                    $notif->user->nama ?? 'Unknown'
                ] : []
            ];
        });

        // 6. Render Tampilan
        return view('admin.dashboard', compact(
            'brankas',
            'statAkses',
            'statNotif',
            'lastAkses',
            'histories',
            'notifications'
        ));
    }
}
