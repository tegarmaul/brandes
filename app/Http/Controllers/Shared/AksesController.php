<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HistoryAkses;
use App\Models\NotifikasiKeamanan;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk menangani upaya akses brankas (Autentikasi Perangkat ke Web).
 * Mengatur riwayat akses, sistem notifikasi, dan keamanan pemblokiran akun otomatis.
 */
class AksesController extends Controller
{
    /**
     * Menangani upaya (attempt) akses dari perangkat IoT.
     * Menerima payload dari hardware dan menentukan apakah akses tersebut berhasil atau gagal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleAttempt(Request $request)
    {
        // 1. Ekstraksi Input Request
        $userId = $request->input('user_id');
        $method = $request->input('method'); // 'fingerprint' atau 'pin'
        $status = $request->input('status'); // 'success' atau 'fail'
        
        // 2. Cari Identitas User
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // 3. Percabangan Logika Berdasarkan Status Akses
        if ($status === 'success') {
            return $this->handleSuccess($user, $method);
        } else {
            return $this->handleFailure($user, $method);
        }
    }

    /**
     * Logika penanganan ketika autentikasi berhasil.
     *
     * @param  \App\Models\User  $user
     * @param  string  $method
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleSuccess($user, $method)
    {
        // 1. Catat ke Riwayat Akses (HistoryAkses)
        HistoryAkses::create([
            'user_id' => $user->id,
            'metode'  => ucfirst($method),
            'status'  => 'Berhasil',
            'waktu'   => now(),
        ]);

        // 2. Buat Notifikasi Keberhasilan (Tipe: Success/Green)
        NotifikasiKeamanan::create([
            'user_id' => $user->id,
            'judul'   => 'Akses Brankas',
            'pesan'   => 'Anda berhasil membuka Brankas menggunakan autentikasi ' . ucfirst($method) . '.',
            'tipe'    => 'success',
            'waktu'   => now(),
        ]);

        return response()->json(['message' => 'Access authorized']);
    }

    /**
     * Logika penanganan ketika autentikasi gagal.
     * Termasuk proteksi pemblokiran akun jika gagal 3 kali berturut-turut.
     *
     * @param  \App\Models\User  $user
     * @param  string  $method
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleFailure($user, $method)
    {
        // 1. Catat ke Riwayat Akses (HistoryAkses)
        HistoryAkses::create([
            'user_id' => $user->id,
            'metode'  => ucfirst($method),
            'status'  => 'Gagal',
            'waktu'   => now(),
        ]);

        // 2. Buat Notifikasi Peringatan (Tipe: Warning/Yellow)
        $judul = ($method === 'fingerprint') ? 'Autentikasi - Fingerprints gagal' : 'Autentikasi - Keypad PIN gagal';
        $pesan = ($method === 'fingerprint') 
            ? 'Percobaan akses tidak dikenali. Pastikan jari Anda bersih dan kering saat melakukan autentikasi.'
            : 'Percobaan akses tidak valid. Pastikan Anda memasukkan PIN 6 digit yang benar.';

        NotifikasiKeamanan::create([
            'user_id' => $user->id,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => 'warning',
            'waktu'   => now(),
        ]);

        // 3. Proteksi Keamanan: Cek ambang batas kegagalan (3x berturut-turut)
        $lastThree = HistoryAkses::where('user_id', $user->id)
            ->orderBy('waktu', 'desc')
            ->take(3)
            ->get();

        if ($lastThree->count() >= 3 && $lastThree->where('status', 'Gagal')->count() >= 3) {
            
            // A. Nonaktifkan Akun User Secara Otomatis
            $user->update(['aktif' => false]);

            // B. Notifikasi Kritis untuk User (Tipe: Danger/Red)
            NotifikasiKeamanan::create([
                'user_id' => $user->id,
                'judul'   => 'Akses Dashboard Keamanan',
                'pesan'   => 'Percobaan akses gagal sebanyak 3 kali berturut-turut. Akun Anda telah dinonaktifkan demi alasan keamanan. Hubungi Admin.',
                'tipe'    => 'danger',
                'waktu'   => now(),
            ]);

            // C. Notifikasi Sistem untuk Admin (Pemantauan Keamanan Global)
            NotifikasiKeamanan::create([
                'user_id' => null, // Ditujukan untuk Admin/Sistem
                'judul'   => 'Peringatan Keamanan - User Diblokir',
                'pesan'   => 'User ' . $user->nama . ' (ID: ' . $user->id . ') telah dinonaktifkan otomatis karena 3x gagal akses.',
                'tipe'    => 'danger',
                'waktu'   => now(),
            ]);
        }

        return response()->json(['message' => 'Access denied']);
    }
}
