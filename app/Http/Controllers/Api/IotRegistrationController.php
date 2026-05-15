<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegistrasiIot;

/**
 * Controller untuk menangani proses pendaftaran sidik jari dan PIN baru dari perangkat IoT.
 * Data disimpan sementara di tabel 'registrasi_iot' sebelum dikaitkan ke akun User.
 */
class IotRegistrationController extends Controller
{
    /**
     * Menerima dan menyimpan data pendaftaran sidik jari & PIN dari perangkat ESP32.
     * Endpoint: POST /api/iot/pendaftaran
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Input dari Perangkat (ESP32)
        $validated = $request->validate([
            'fingerprint_id' => 'required|string',
            'pin'            => 'required|string|size:6'
        ]);

        // 2. Simpan Data ke Tabel Antrean Pendaftaran (registrasi_iot)
        $reg = RegistrasiIot::create([
            'fingerprint_id' => $validated['fingerprint_id'],
            'pin'            => $validated['pin'],
            'is_used'        => false // Default: belum digunakan/dikaitkan ke User
        ]);

        // 3. Kembalikan Response Berhasil
        return response()->json([
            'success' => true,
            'message' => 'Data pendaftaran berhasil diterima',
            'data'    => $reg
        ], 201);
    }

    /**
     * Mendapatkan data pendaftaran terbaru yang belum diproses/dikaitkan ke User.
     * Digunakan oleh Dashboard Admin untuk proses sinkronisasi user baru secara real-time.
     * Endpoint: GET /api/iot/latest-registration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        // 1. Ambil Data Terakhir yang Statusnya Belum Digunakan (is_used = false)
        $latest = RegistrasiIot::where('is_used', false)
            ->latest()
            ->first();

        // 2. Jika Data Ditemukan, Kirim Response Sukses
        if ($latest) {
            return response()->json([
                'success' => true,
                'data'    => $latest
            ]);
        }

        // 3. Jika Tidak Ada Antrean Pendaftaran Baru
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrean pendaftaran baru'
        ]);
    }
}
