<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'nama',
        'username',
        'pin',
        'fingerprint_id',
        'role',
        'aktif',
    ];

    protected $hidden = ['pin'];

    public function histories()
    {
        return $this->hasMany(HistoryAkses::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}