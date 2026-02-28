<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_akses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama')->nullable();           // nama user saat akses (snapshot)
            $table->string('aktivitas');                  // 'Membuka Brankas', 'Percobaan Akses Gagal', dll
            $table->enum('status', ['Berhasil', 'Gagal'])->default('Berhasil');
            $table->string('fingerprint_id')->nullable(); // ID fingerprint dari ESP32
            $table->timestamp('waktu')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_akses');
    }
};
