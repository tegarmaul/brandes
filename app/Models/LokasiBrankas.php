<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'aktif'     => 'boolean',
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}
