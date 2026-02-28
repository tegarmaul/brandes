<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('pin');                                        // bcrypt hash
            $table->unsignedSmallInteger('fingerprint_id')->unique()->nullable(); // Slot ID dari sensor biometrik ESP32 (1–127)
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};