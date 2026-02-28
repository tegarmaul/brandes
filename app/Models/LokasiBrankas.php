<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk brankas yang dilengkapi GPS Neo-6M via ESP32.
 *
 * Kolom GPS yang relevan:
 * - latitude / longitude  : koordinat posisi terkini
 * - altitude              : ketinggian (meter)
 * - hdop                  : akurasi horizontal (< 1 = sangat baik, < 2 = baik)
 * - satellites            : jumlah satelit terkunci (≥ 4 untuk fix 3D)
 * - speed_kmh             : kecepatan (km/h) — deteksi pergerakan brankas
 * - fix_quality           : 0=invalid, 1=GPS fix, 2=DGPS fix
 * - last_gps_update       : timestamp terakhir data GPS diterima
 */
class LokasiBrankas extends Model
{
    protected $table = 'lokasi_brankas';

    protected $fillable = [
        'nama_brankas',
        'lokasi',
        'kode_brankas',
        'status',
        'latitude',
        'longitude',
        'altitude',
        'hdop',
        'satellites',
        'speed_kmh',
        'fix_quality',
        'last_gps_update',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'aktif'           => 'boolean',
        'latitude'        => 'decimal:8',
        'longitude'       => 'decimal:8',
        'altitude'        => 'decimal:2',
        'hdop'            => 'decimal:2',
        'satellites'      => 'integer',
        'speed_kmh'       => 'decimal:2',
        'fix_quality'     => 'integer',
        'last_gps_update' => 'datetime',
    ];

    /**
     * Relasi ke riwayat posisi GPS
     */
    public function histories()
    {
        return $this->hasMany(LokasiHistory::class, 'lokasi_brankas_id');
    }

    /**
     * Ambil data GPS terbaru
     */
    public function latestHistory()
    {
        return $this->hasOne(LokasiHistory::class, 'lokasi_brankas_id')
                    ->latestOfMany('recorded_at');
    }

    /**
     * Cek apakah GPS fix valid (fix_quality >= 1 dan ada satelit)
     */
    public function isGpsValid(): bool
    {
        return $this->fix_quality >= 1 && $this->satellites >= 4;
    }

    /**
     * Cek apakah brankas bergerak (speed > 0.5 km/h)
     */
    public function isMoving(): bool
    {
        return $this->speed_kmh !== null && $this->speed_kmh > 0.5;
    }

    /**
     * Label akurasi GPS berdasarkan HDOP
     */
    public function hdopLabel(): string
    {
        if ($this->hdop === null) return 'Tidak diketahui';
        if ($this->hdop < 1)  return 'Sangat Baik';
        if ($this->hdop < 2)  return 'Baik';
        if ($this->hdop < 5)  return 'Cukup';
        if ($this->hdop < 10) return 'Buruk';
        return 'Sangat Buruk';
    }
}
