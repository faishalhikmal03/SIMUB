<?php
// File: database/migrations/0001_01_01_000000_create_users_table.php
// (MODIFIKASI FILE BAWAAN LARAVEL INI)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('npm')->unique()->nullable();
            $table->string('nidn')->unique()->nullable(); // Ditambahkan untuk dosen
            $table->string('nama')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('tanggal_yudisium')->nullable(); // Menggantikan angkatan
            $table->enum('role', ['mahasiswa', 'mahasiswa_baru', 'alumni', 'admin', 'dosen']); // Menambahkan role dosen
            $table->string('foto_profile')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};