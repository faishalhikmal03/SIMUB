<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            // Kita mengubah kolom 'tipe_jawaban' untuk menambahkan 'pilihan_dosen'
            // Pastikan semua opsi lama tetap ada
            $table->enum('tipe_jawaban', [
                'text_singkat', 
                'paragraf', 
                'single_option', 
                'checkbox', 
                'pilihan_dosen' // <-- Opsi baru ditambahkan di sini
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertanyaans', function (Blueprint $table) {
            // Logika untuk mengembalikan jika migrasi dibatalkan
            $table->enum('tipe_jawaban', [
                'text_singkat', 
                'paragraf', 
                'single_option', 
                'checkbox'
            ])->change();
        });
    }
};