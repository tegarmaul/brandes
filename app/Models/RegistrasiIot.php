<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan tabel 'registrasi_iot'.
 * Digunakan sebagai antrean data pendaftaran (sidik jari & PIN) yang dikirim oleh perangkat IoT.
 */
class RegistrasiIot extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model ini.
     *
     * @var string
     */
    protected $table = 'registrasi_iot';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'fingerprint_id',
        'pin',
        'is_used',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu (casting).
     *
     * @var array
     */
    protected $casts = [
        'is_used' => 'boolean',
    ];
}
