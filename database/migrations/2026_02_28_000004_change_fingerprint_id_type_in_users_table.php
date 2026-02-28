<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mengubah tipe kolom fingerprint_id dari string (VARCHAR) menjadi
 * unsignedSmallInteger agar sesuai dengan slot ID integer yang dikirim
 * oleh sensor biometrik (AS608/R307) pada perangkat ESP32.
 *
 * Sensor biometrik menyimpan template sidik jari secara internal dan
 * mengembalikan nomor slot (1–127). Server hanya menyimpan nomor slot
 * tersebut, bukan data biometrik mentah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus unique constraint lama sebelum mengubah tipe kolom
            $table->dropUnique(['fingerprint_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('fingerprint_id')
                  ->unique()
                  ->nullable()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['fingerprint_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('fingerprint_id')
                  ->unique()
                  ->nullable()
                  ->change();
        });
    }
};
