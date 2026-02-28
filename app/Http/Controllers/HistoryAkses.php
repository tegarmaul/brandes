<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryAkses extends Model
{
    protected $table = 'history_akses';

    protected $fillable = [
        'user_id',
        'aktivitas',
        'status',
        'fingerprint_id',
        'waktu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}