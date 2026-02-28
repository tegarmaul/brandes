<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_brankas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_brankas');
            $table->string('lokasi');
            $table->string('kode_brankas')->unique();
            $table->enum('status', ['aman', 'terbuka', 'peringatan'])->default('aman');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_brankas');
    }
};
