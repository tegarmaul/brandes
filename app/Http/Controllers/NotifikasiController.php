<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        $notifikasi = [
            [
                'tipe'      => 'peringatan',
                'judul'     => 'Percobaan Akses',
                'deskripsi' => 'Percobaan paksa membuka brankas terdeteksi. Sistem keamanan aktif.',
                'meta'      => ['FP-XXX-XXXX', 'Unknown user', '3x percobaan'],
                'waktu'     => '2026-01-31 13:30:45',
                'dibaca'    => false,
            ],
            [
                'tipe'      => 'kritis',
                'judul'     => 'Percobaan Pembobolan',
                'deskripsi' => 'Percobaan pembobolan brankas terdeteksi. Sistem keamanan aktif.',
                'meta'      => [],
                'waktu'     => '2026-01-31 13:30:45',
                'dibaca'    => false,
            ],
            [
                'tipe'      => 'akses',
                'judul'     => 'Akses Brankas',
                'deskripsi' => 'Akses brankas berhasil',
                'meta'      => ['FP-001-A3F3', 'Sodikin'],
                'waktu'     => '2026-01-31 12:45:20',
                'dibaca'    => true,
            ],
        ];

        $totalNotifikasi = count($notifikasi);
        $totalPeringatan = collect($notifikasi)->where('tipe', 'peringatan')->count();
        $totalKritis     = collect($notifikasi)->where('tipe', 'kritis')->count();
        $totalAkses      = collect($notifikasi)->where('tipe', 'akses')->count();

        return view('notifikasi.keamanan', compact(
            'notifikasi',
            'totalNotifikasi',
            'totalPeringatan',
            'totalKritis',
            'totalAkses'
        ));
    }
}