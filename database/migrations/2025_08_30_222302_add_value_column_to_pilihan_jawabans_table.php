<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fungsi up() dijalankan saat Anda menjalankan 'php artisan migrate'.
     * Ini akan MENAMBAHKAN kolom 'value' ke tabel Anda.
     */
    public function up(): void
    {
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            $table->string('value')->nullable()->after('pilihan'); 
        });
    }

    /**
     * Reverse the migrations.
     * Fungsi down() dijalankan jika Anda perlu membatalkan migrasi (rollback).
     * Ini akan MENGHAPUS kolom 'value' untuk mengembalikan skema ke kondisi semula.
     */
    public function down(): void
    {
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
};
