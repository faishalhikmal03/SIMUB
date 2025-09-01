<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel pertanyaans yang lengkap.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->enum('tipe_jawaban', [
                'text_singkat', 
                'paragraf', 
                'single_option', 
                'checkbox', 
                'pilihan_dosen'
            ]);
            
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
        Schema::dropIfExists('pertanyaans');
    }
};