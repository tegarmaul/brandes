<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        // Data dummy — nanti diganti dari database/IoT
        $histories = [
            [
                'nama'       => 'Ikhwanudin F',
                'aktivitas'  => 'Membuka Brankas',
                'waktu'      => '2026-01-31 14:25:30',
                'waktu_lalu' => '5 Menit yang lalu',
                'total_akses'=> 1,
                'status'     => 'Berhasil',
            ],
            [
                'nama'       => 'Sri Endang',
                'aktivitas'  => 'Membuka Brankas',
                'waktu'      => '2026-01-31 14:15:12',
                'waktu_lalu' => '15 Menit yang lalu',
                'total_akses'=> 1,
                'status'     => 'Berhasil',
            ],
            [
                'nama'       => 'Unknown User',
                'aktivitas'  => 'Percobaan Akses Gagal',
                'waktu'      => '2026-01-31 13:30:45',
                'waktu_lalu' => '25 Menit yang lalu',
                'total_akses'=> 5,
                'status'     => 'Gagal',
            ],
            [
                'nama'       => 'Akhmad Sodikin',
                'aktivitas'  => 'Membuka Brankas',
                'waktu'      => '2026-01-31 12:45:20',
                'waktu_lalu' => '35 Menit yang lalu',
                'total_akses'=> 1,
                'status'     => 'Berhasil',
            ],
            [
                'nama'       => 'Ikhwanudin F',
                'aktivitas'  => 'Membuka Brankas',
                'waktu'      => '2026-01-31 11:30:15',
                'waktu_lalu' => '45 Menit yang lalu',
                'total_akses'=> 1,
                'status'     => 'Berhasil',
            ],
            [
                'nama'       => 'M Wakhidin',
                'aktivitas'  => 'Percobaan Akses Gagal',
                'waktu'      => '2026-01-31 13:30:45',
                'waktu_lalu' => '25 Menit yang lalu',
                'total_akses'=> 2,
                'status'     => 'Gagal',
            ],
        ];

        $totalAkses    = count($histories);
        $aksesberhasil = collect($histories)->where('status', 'Berhasil')->count();
        $aksesGagal    = collect($histories)->where('status', 'Gagal')->count();

        return view('history.akses', compact('histories', 'totalAkses', 'aksesberhasil', 'aksesGagal'));
    }
}