<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel kuesioners yang lengkap.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('kuesioners', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('target_user', ['mahasiswa', 'mahasiswa_baru', 'alumni', 'dosen']);
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->boolean('bisa_diisi_ulang')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Membatalkan migrasi.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('kuesioners');
    }
};