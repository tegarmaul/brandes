<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryAkses extends Model
{
    protected $table = 'history_akses';

    protected $fillable = [
        'user_id',
        'nama',
        'aktivitas',
        'status',
        'fingerprint_id',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
