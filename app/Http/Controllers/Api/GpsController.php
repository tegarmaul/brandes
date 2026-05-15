<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LokasiBrankas;
use App\Models\LokasiHistory;

/**
 * Controller untuk menangani API data GPS dari perangkat IoT (ESP32).
 */
class GpsController extends Controller
{
    /**
     * Menyimpan data koordinat dan metrik GPS dari perangkat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Verifikasi Token Perangkat (Security Layer)
        $expectedToken = config('app.gps_device_token', env('GPS_DEVICE_TOKEN'));

        if ($expectedToken && $request->header('X-Device-Token') !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized device'], 401);
        }

        // 2. Validasi Data Input
        $validated = $request->validate([
            'kode_brankas' => 'required|string|exists:lokasi_brankas,kode_brankas',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
            'altitude'     => 'nullable|numeric',
            'hdop'         => 'nullable|numeric|min:0',
            'satellites'   => 'nullable|integer|min:0|max:12',
            'fix_quality'  => 'nullable|integer|in:0,1,2',
            'speed_kmh'    => 'nullable|numeric|min:0',
            'getaran'      => 'nullable|numeric|min:0',
            'raw_nmea'     => 'nullable|string|max:500',
        ]);

        // 3. Ambil Master Data Brankas
        $brankas = LokasiBrankas::where('kode_brankas', $validated['kode_brankas'])->firstOrFail();

        // 4. Analisis Status Keamanan Berdasarkan Getaran & Kecepatan
        $status = LokasiHistory::determineStatus(
            $validated['getaran'] ?? null,
            $validated['speed_kmh'] ?? null
        );

        // 5. Simpan ke Riwayat (LokasiHistory)
        $history = LokasiHistory::create([
            'lokasi_brankas_id' => $brankas->id,
            'latitude'          => $validated['latitude'],
            'longitude'         => $validated['longitude'],
            'altitude'          => $validated['altitude'] ?? null,
            'hdop'              => $validated['hdop'] ?? null,
            'satellites'        => $validated['satellites'] ?? null,
            'fix_quality'       => $validated['fix_quality'] ?? 0,
            'speed_kmh'         => $validated['speed_kmh'] ?? null,
            'getaran'           => $validated['getaran'] ?? null,
            'status'            => $status,
            'raw_nmea'          => $validated['raw_nmea'] ?? null,
            'recorded_at'       => now(),
        ]);

        // 6. Integrasi Reverse Geocoding (Nominatim OpenStreetMap)
        // Berfungsi untuk menerjemahkan koordinat menjadi alamat jalan secara otomatis
        $alamatOtomatis = $brankas->lokasi; // Default menggunakan alamat lama jika proses gagal
        
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$validated['latitude']}&lon={$validated['longitude']}&zoom=18&addressdetails=1";
            
            // Menggunakan context stream karena Nominatim mewajibkan User-Agent
            $options = [
                'http' => [
                    'header' => "User-Agent: BrandesMonitoringApp/1.0\r\n"
                ]
            ];
            $context  = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            
            if ($response) {
                $dataAlamat = json_decode($response, true);
                if (isset($dataAlamat['display_name'])) {
                    $alamatOtomatis = $dataAlamat['display_name'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal melakukan Reverse Geocoding: ' . $e->getMessage());
        }

        // 7. Perbarui Data Terkini di Tabel Master Brankas
        $brankas->update([
            'latitude'        => $validated['latitude'],
            'longitude'       => $validated['longitude'],
            'lokasi'          => $alamatOtomatis,
            'altitude'        => $validated['altitude'] ?? $brankas->altitude,
            'hdop'            => $validated['hdop'] ?? $brankas->hdop,
            'satellites'      => $validated['satellites'] ?? $brankas->satellites,
            'fix_quality'     => $validated['fix_quality'] ?? 0,
            'speed_kmh'       => $validated['speed_kmh'] ?? null,
            'last_gps_update' => now(),
            'status'          => match ($status) {
                'bahaya'  => 'peringatan',
                'waspada' => 'peringatan',
                default   => 'aman',
            },
        ]);

        // 8. Log Aktivitas untuk Debugging
        Log::info('GPS data received', [
            'brankas'    => $brankas->kode_brankas,
            'lat'        => $validated['latitude'],
            'lng'        => $validated['longitude'],
            'satellites' => $validated['satellites'] ?? null,
            'hdop'       => $validated['hdop'] ?? null,
            'status'     => $status,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Data GPS berhasil disimpan',
            'history_id' => $history->id,
            'status'     => $status,
        ], 201);
    }

    /**
     * Mendapatkan data posisi terakhir dan metrik GPS dari spesifik brankas.
     *
     * @param  string  $kodeBrankas
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(string $kodeBrankas)
    {
        // 1. Ambil Data Brankas Berdasarkan Kode
        $brankas = LokasiBrankas::where('kode_brankas', $kodeBrankas)
            ->where('aktif', true)
            ->firstOrFail();

        // 2. Kembalikan Response JSON yang Lengkap
        return response()->json([
            'kode_brankas'    => $brankas->kode_brankas,
            'nama_brankas'    => $brankas->nama_brankas,
            'latitude'        => $brankas->latitude,
            'longitude'       => $brankas->longitude,
            'altitude'        => $brankas->altitude,
            'hdop'            => $brankas->hdop,
            'hdop_label'      => $brankas->hdopLabel(),
            'satellites'      => $brankas->satellites,
            'fix_quality'     => $brankas->fix_quality,
            'gps_valid'       => $brankas->isGpsValid(),
            'speed_kmh'       => $brankas->speed_kmh,
            'is_moving'       => $brankas->isMoving(),
            'status'          => $brankas->status,
            'last_gps_update' => $brankas->last_gps_update?->toIso8601String(),
        ]);
    }

    /**
     * Mendapatkan status koneksi, pintu, dan lokasi secara real-time.
     * Termasuk logika deteksi offline otomatis.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusRealtime()
    {
        // 1. Ambil Data Brankas (Asumsi brankas utama)
        $brankas = LokasiBrankas::first();

        if ($brankas) {
            // 2. Logika Deteksi Offline Otomatis: 
            // Jika status tercatat 'Online' tapi sudah >= 2 menit tidak ada komunikasi, set menjadi Offline
            if ($brankas->is_online && $brankas->last_seen && $brankas->last_seen->diffInMinutes(now()) >= 2) {
                $brankas->update(['is_online' => false]);
            }

            return response()->json([
                'success'      => true,
                'is_online'    => (bool) $brankas->is_online,
                'status_pintu' => $brankas->status_pintu ?? 'TERKUNCI',
                'latitude'     => $brankas->latitude,
                'longitude'    => $brankas->longitude,
                'nama_brankas' => $brankas->nama_brankas,
                'lokasi'       => $brankas->lokasi,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Data brankas tidak ditemukan'], 404);
    }
}
