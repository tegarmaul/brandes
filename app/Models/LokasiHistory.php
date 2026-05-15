<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan tabel 'lokasi_history'.
 * Mencatat riwayat log GPS dan metrik sensor getaran yang dikirim oleh perangkat IoT.
 * 
 * Informasi NMEA yang diproses:
 * - GPGGA: Koordinat, Ketinggian, Kualitas Fix, Satelit, HDOP.
 * - GPRMC: Kecepatan (Speed), Heading.
 */
class LokasiHistory extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model ini.
     *
     * @var string
     */
    protected $table = 'lokasi_history';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
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

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu (casting).
     *
     * @var array
     */
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
     * Mendapatkan data master brankas yang terkait dengan riwayat ini.
     * Relasi: Many-to-One (BelongsTo)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brankas()
    {
        return $this->belongsTo(LokasiBrankas::class, 'lokasi_brankas_id');
    }

    /**
     * Mengonversi kode status sistem ke label Bahasa Indonesia yang ramah pengguna.
     *
     * @return string
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'waspada' => 'Waspada',
            'bahaya'  => 'Bahaya',
            default   => 'Normal',
        };
    }

    /**
     * Logika bisnis otomatis untuk menentukan status keamanan berdasarkan metrik sensor.
     * Aturan Deteksi:
     * - Bahaya  : Getaran > 3.0 G ATAU Kecepatan > 5.0 km/h (Indikasi brankas dibawa lari).
     * - Waspada : Getaran > 1.0 G ATAU Kecepatan > 1.0 km/h (Indikasi guncangan/perpindahan pelan).
     * - Normal  : Kondisi diam dan tenang.
     *
     * @param  float|null  $getaran
     * @param  float|null  $speedKmh
     * @return string
     */
    public static function determineStatus(?float $getaran, ?float $speedKmh): string
    {
        // 1. Cek Kondisi Bahaya (Prioritas Tertinggi)
        if (($getaran !== null && $getaran > 3.0) || ($speedKmh !== null && $speedKmh > 5.0)) {
            return 'bahaya';
        }

        // 2. Cek Kondisi Waspada
        if (($getaran !== null && $getaran > 1.0) || ($speedKmh !== null && $speedKmh > 1.0)) {
            return 'waspada';
        }

        // 3. Kondisi Aman/Normal
        return 'normal';
    }
}
