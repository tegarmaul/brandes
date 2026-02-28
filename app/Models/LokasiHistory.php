<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk riwayat posisi GPS yang dikirim ESP32 + GPS Neo-6M.
 *
 * Setiap pengiriman data dari ESP32 menghasilkan satu record baru.
 * Data NMEA yang diparse dari GPS Neo-6M:
 * - GPGGA: latitude, longitude, altitude, fix_quality, satellites, hdop
 * - GPRMC: speed_kmh, heading
 */
class LokasiHistory extends Model
{
    protected $table = 'lokasi_history';

    protected $fillable = [
        'lokasi_brankas_id',
        'latitude',
        'longitude',
        'altitude',
        'hdop',
        'satellites',
        'fix_quality',
        'speed_kmh',
        'getaran',
        'status',
        'raw_nmea',
        'recorded_at',
    ];

    protected $casts = [
        'latitude'    => 'decimal:8',
        'longitude'   => 'decimal:8',
        'altitude'    => 'decimal:2',
        'hdop'        => 'decimal:2',
        'satellites'  => 'integer',
        'fix_quality' => 'integer',
        'speed_kmh'   => 'decimal:2',
        'getaran'     => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /**
     * Relasi ke brankas
     */
    public function brankas()
    {
        return $this->belongsTo(LokasiBrankas::class, 'lokasi_brankas_id');
    }

    /**
     * Label status dalam Bahasa Indonesia
     */
    public function statusLabel(): string
    {
        return match($this->status) {
            'waspada' => 'Waspada',
            'bahaya'  => 'Bahaya',
            default   => 'Normal',
        };
    }

    /**
     * Tentukan status otomatis berdasarkan nilai getaran dan kecepatan
     * - bahaya  : getaran > 3.0 G atau kecepatan > 5 km/h
     * - waspada : getaran > 1.0 G atau kecepatan > 1 km/h
     * - normal  : lainnya
     */
    public static function determineStatus(?float $getaran, ?float $speedKmh): string
    {
        if (($getaran !== null && $getaran > 3.0) || ($speedKmh !== null && $speedKmh > 5.0)) {
            return 'bahaya';
        }
        if (($getaran !== null && $getaran > 1.0) || ($speedKmh !== null && $speedKmh > 1.0)) {
            return 'waspada';
        }
        return 'normal';
    }
}
