<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LokasiBrankas;
use App\Models\LokasiHistory;

/**
 * Controller untuk menerima data GPS Neo-6M dari ESP32.
 *
 * ESP32 mengirim HTTP POST ke endpoint ini setiap interval tertentu
 * (misalnya setiap 30 detik) dengan payload JSON berisi data NMEA
 * yang sudah diparse.
 *
 * Contoh payload dari ESP32:
 * {
 *   "kode_brankas": "BRK-001",
 *   "latitude": -6.91050000,
 *   "longitude": 109.14790000,
 *   "altitude": 15.20,
 *   "hdop": 1.20,
 *   "satellites": 7,
 *   "fix_quality": 1,
 *   "speed_kmh": 0.00,
 *   "getaran": 0.10,
 *   "raw_nmea": "$GPGGA,..."
 * }
 */
class GpsController extends Controller
{
    /**
     * Menerima data GPS dari ESP32 dan menyimpannya ke database.
     *
     * Endpoint: POST /api/gps
     * Header  : X-Device-Token: {token dari .env GPS_DEVICE_TOKEN}
     */
    public function store(Request $request)
    {
        // Verifikasi token perangkat ESP32
        $expectedToken = config('app.gps_device_token', env('GPS_DEVICE_TOKEN'));
        if ($expectedToken && $request->header('X-Device-Token') !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized device'], 401);
        }

        // Validasi payload
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

        // Cari brankas berdasarkan kode
        $brankas = LokasiBrankas::where('kode_brankas', $validated['kode_brankas'])->firstOrFail();

        // Tentukan status berdasarkan getaran dan kecepatan
        $status = LokasiHistory::determineStatus(
            $validated['getaran'] ?? null,
            $validated['speed_kmh'] ?? null
        );

        // Simpan ke history
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

        // Update data GPS terkini di tabel brankas
        $brankas->update([
            'latitude'        => $validated['latitude'],
            'longitude'       => $validated['longitude'],
            'altitude'        => $validated['altitude'] ?? $brankas->altitude,
            'hdop'            => $validated['hdop'] ?? $brankas->hdop,
            'satellites'      => $validated['satellites'] ?? $brankas->satellites,
            'fix_quality'     => $validated['fix_quality'] ?? 0,
            'speed_kmh'       => $validated['speed_kmh'] ?? null,
            'last_gps_update' => now(),
            // Update status brankas jika ada kondisi berbahaya
            'status'          => match($status) {
                'bahaya'  => 'peringatan',
                'waspada' => 'peringatan',
                default   => 'aman',
            },
        ]);

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
     * Ambil data GPS terbaru untuk brankas tertentu (polling dari frontend).
     *
     * Endpoint: GET /api/gps/{kode_brankas}
     */
    public function latest(string $kodeBrankas)
    {
        $brankas = LokasiBrankas::where('kode_brankas', $kodeBrankas)
            ->where('aktif', true)
            ->firstOrFail();

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
}
