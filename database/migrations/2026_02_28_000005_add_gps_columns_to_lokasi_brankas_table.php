<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom-kolom data GPS Neo-6M ke tabel lokasi_brankas.
 *
 * GPS Neo-6M mengirimkan data NMEA (GPGGA, GPRMC) yang mencakup:
 * - latitude / longitude  : koordinat posisi (sudah ada)
 * - altitude              : ketinggian dari permukaan laut (meter)
 * - hdop                  : Horizontal Dilution of Precision (akurasi horizontal)
 * - satellites            : jumlah satelit yang terkunci
 * - speed_kmh             : kecepatan (km/h) dari kalimat GPRMC
 * - fix_quality           : kualitas fix GPS (0=invalid, 1=GPS fix, 2=DGPS fix)
 * - last_gps_update       : timestamp terakhir data GPS diterima dari ESP32
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lokasi_brankas', function (Blueprint $table) {
            // Data GPS Neo-6M tambahan
            $table->decimal('altitude', 8, 2)->nullable()->after('longitude')
                  ->comment('Ketinggian dari permukaan laut (meter)');
            $table->decimal('hdop', 5, 2)->nullable()->after('altitude')
                  ->comment('Horizontal Dilution of Precision — semakin kecil semakin akurat');
            $table->unsignedTinyInteger('satellites')->nullable()->after('hdop')
                  ->comment('Jumlah satelit GPS yang terkunci (0–12)');
            $table->decimal('speed_kmh', 6, 2)->nullable()->after('satellites')
                  ->comment('Kecepatan brankas (km/h) dari GPRMC — deteksi pergerakan');
            $table->tinyInteger('fix_quality')->default(0)->after('speed_kmh')
                  ->comment('Kualitas fix GPS: 0=invalid, 1=GPS fix, 2=DGPS fix');
            $table->timestamp('last_gps_update')->nullable()->after('fix_quality')
                  ->comment('Timestamp terakhir data GPS diterima dari ESP32');
        });
    }

    public function down(): void
    {
        Schema::table('lokasi_brankas', function (Blueprint $table) {
            $table->dropColumn([
                'altitude',
                'hdop',
                'satellites',
                'speed_kmh',
                'fix_quality',
                'last_gps_update',
            ]);
        });
    }
};
