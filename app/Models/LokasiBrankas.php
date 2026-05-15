<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan tabel 'lokasi_brankas'.
 * Mengelola data master brankas, status koneksi IoT, dan metrik GPS Neo-6M.
 *
 * Kolom GPS Utama:
 * - latitude / longitude  : Koordinat posisi terkini.
 * - altitude              : Ketinggian dari permukaan laut (meter).
 * - hdop                  : Horizontal Dilution of Precision (Akurasi horizontal).
 * - satellites            : Jumlah satelit yang terhubung.
 * - speed_kmh             : Kecepatan pergerakan brankas (km/h).
 * - fix_quality           : Kualitas sinyal GPS (0=invalid, 1=fix, 2=DGPS).
 */
class LokasiBrankas extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model ini.
     *
     * @var string
     */
    protected $table = 'lokasi_brankas';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'nama_brankas',
        'lokasi',
        'kode_brankas',
        'status',
        'status_pintu',
        'is_online',
        'latitude',
        'longitude',
        'altitude',
        'hdop',
        'satellites',
        'speed_kmh',
        'fix_quality',
        'last_gps_update',
        'last_seen',
        'keterangan',
        'aktif',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu (casting).
     *
     * @var array
     */
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
        'last_seen'       => 'datetime',
    ];

    /**
     * Mendapatkan riwayat posisi GPS yang terkait dengan brankas ini.
     * Relasi: One-to-Many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        return $this->hasMany(LokasiHistory::class, 'lokasi_brankas_id');
    }

    /**
     * Mendapatkan data riwayat posisi GPS terbaru.
     * Relasi: One-to-One (Latest of Many)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestHistory()
    {
        return $this->hasOne(LokasiHistory::class, 'lokasi_brankas_id')
            ->latestOfMany('recorded_at');
    }

    /**
     * Mengecek apakah sinyal GPS saat ini valid.
     * Kriteria: fix_quality >= 1 dan minimal 4 satelit terkunci.
     *
     * @return bool
     */
    public function isGpsValid(): bool
    {
        return $this->fix_quality >= 1 && $this->satellites >= 4;
    }

    /**
     * Mengecek apakah brankas terdeteksi sedang bergerak.
     * Kriteria: kecepatan > 0.5 km/h.
     *
     * @return bool
     */
    public function isMoving(): bool
    {
        return $this->speed_kmh !== null && $this->speed_kmh > 0.5;
    }

    /**
     * Mengonversi nilai HDOP menjadi label akurasi yang mudah dipahami manusia.
     *
     * @return string
     */
    public function hdopLabel(): string
    {
        if ($this->hdop === null) {
            return 'Tidak diketahui';
        }
        
        if ($this->hdop < 1) {
            return 'Sangat Baik';
        }
        
        if ($this->hdop < 2) {
            return 'Baik';
        }
        
        if ($this->hdop < 5) {
            return 'Cukup';
        }
        
        if ($this->hdop < 10) {
            return 'Buruk';
        }
        
        return 'Sangat Buruk';
    }
}
