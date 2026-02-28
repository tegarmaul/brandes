<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom fingerprint sudah ada di create_users_table migration.
     * Migration ini diabaikan agar tidak terjadi duplikasi kolom.
     */
    public function up(): void
    {
        // Kolom fingerprint_id sudah didefinisikan di 0001_01_01_000000_create_users_table.php
        // Tidak perlu menambahkan kolom lagi
    }

    public function down(): void
    {
        // Tidak ada yang perlu di-rollback
    }
};