<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'nama',
        'username',
        'pin',
        'fingerprint_id',
        'role',
        'aktif',
    ];

    protected $hidden = [
        'pin',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke history akses
     */
    public function histories()
    {
        return $this->hasMany(HistoryAkses::class, 'user_id');
    }

    /**
     * Relasi ke notifikasi keamanan
     */
    public function notifikasi()
    {
        return $this->hasMany(NotifikasiKeamanan::class, 'user_id');
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah user biasa
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
