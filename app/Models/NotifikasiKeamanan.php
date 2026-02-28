<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiKeamanan extends Model
{
    protected $table = 'notifikasi_keamanan';

    protected $fillable = [
        'judul',
        'pesan',
        'tipe',
        'dibaca',
        'user_id',
        'waktu',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'waktu'  => 'datetime',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
