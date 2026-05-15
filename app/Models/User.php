<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model untuk merepresentasikan tabel 'users'.
 * Mengelola data autentikasi, profil pengguna, dan hak akses (Role).
 */
class User extends Authenticatable
{
    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'username',
        'pin',
        'fingerprint_id',
        'role',
        'aktif',
        'is_super_admin',
        'security_index',
    ];

    /**
     * Atribut yang harus disembunyikan dalam serialisasi JSON (hidden).
     *
     * @var array
     */
    protected $hidden = [
        'pin',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data tertentu (casting).
     *
     * @var array
     */
    protected $casts = [
        'aktif' => 'boolean',
        'is_super_admin' => 'boolean',
        'security_index' => 'integer',
    ];

    /**
     * Mendapatkan riwayat upaya akses brankas yang dilakukan oleh pengguna ini.
     * Relasi: One-to-Many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories()
    {
        return $this->hasMany(HistoryAkses::class, 'user_id');
    }

    /**
     * Mendapatkan daftar notifikasi keamanan yang ditujukan untuk pengguna ini.
     * Relasi: One-to-Many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifikasi()
    {
        return $this->hasMany(NotifikasiKeamanan::class, 'user_id');
    }

    /**
     * Mendapatkan PIN asli pengguna.
     * Mendukung dua format penyimpanan secara otomatis:
     * - AES Encryption (data baru): PIN didekripsi dan dikembalikan apa adanya
     * - Bcrypt Hash (data lama): Mengembalikan placeholder karena tidak bisa dibalik
     *
     * @return string
     */
    public function getPinAsliAttribute(): string
    {
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($this->pin);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return '[PIN Lama]'; // Tidak bisa didekripsi (format bcrypt)
        }
    }

    /**
     * Mengecek apakah pengguna memiliki hak akses sebagai Administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Mengecek apakah pengguna memiliki hak akses sebagai Pengguna Biasa (User).
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
