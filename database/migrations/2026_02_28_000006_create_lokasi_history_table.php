<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel riwayat posisi GPS yang dikirim oleh ESP32 + GPS Neo-6M.
 *
 * Setiap kali ESP32 mengirim data GPS ke endpoint API, satu baris
 * baru ditambahkan ke tabel ini. Data ini digunakan untuk:
 * - Menampilkan history pergerakan brankas
 * - Mendeteksi anomali (brankas berpindah tempat)
 * - Audit trail lokasi
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_brankas_id')
                  ->constrained('lokasi_brankas')
                  ->onDelete('cascade');

            // Koordinat GPS Neo-6M
            $table->decimal('latitude', 10, 8)
                  ->comment('Latitude dari GPS Neo-6M (format desimal, misal: -6.91050000)');
            $table->decimal('longitude', 11, 8)
                  ->comment('Longitude dari GPS Neo-6M (format desimal, misal: 109.14790000)');
            $table->decimal('altitude', 8, 2)->nullable()
                  ->comment('Ketinggian dari permukaan laut (meter)');

            // Kualitas sinyal GPS
            $table->decimal('hdop', 5, 2)->nullable()
                  ->comment('Horizontal Dilution of Precision');
            $table->unsignedTinyInteger('satellites')->nullable()
                  ->comment('Jumlah satelit yang terkunci');
            $table->tinyInteger('fix_quality')->default(0)
                  ->comment('0=invalid, 1=GPS fix, 2=DGPS fix');

            // Data tambahan dari sensor
            $table->decimal('speed_kmh', 6, 2)->nullable()
                  ->comment('Kecepatan (km/h) dari GPRMC — deteksi pergerakan brankas');
            $table->decimal('getaran', 5, 2)->nullable()
                  ->comment('Nilai getaran dari accelerometer (G)');
            $table->enum('status', ['normal', 'waspada', 'bahaya'])->default('normal')
                  ->comment('Status kondisi brankas saat data dikirim');

            // Raw NMEA sentence (opsional, untuk debugging)
            $table->text('raw_nmea')->nullable()
                  ->comment('Raw NMEA sentence dari GPS Neo-6M (untuk debugging)');

            $table->timestamp('recorded_at')->useCurrent()
                  ->comment('Waktu data GPS diterima server');
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['lokasi_brankas_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_history');
    }
};
