<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk membuat tabel jawabans yang lengkap.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('submission_uuid')->comment('ID unik untuk setiap sesi pengisian');
            $table->foreignId('kuesioner_id')->constrained('kuesioners')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('pertanyaan_id')->constrained('pertanyaans')->onDelete('cascade');
            $table->foreignId('dosen_id')->nullable()->constrained('users')->onDelete('set null')->comment('Konteks: Dosen yang sedang dinilai di sesi ini');
            $table->text('jawaban_text')->nullable()->comment('Untuk jawaban tipe teks/paragraf');
            $table->foreignId('pilihan_jawaban_id')->nullable()->constrained('pilihan_jawabans')->onDelete('set null')->comment('Untuk jawaban tipe pilihan');
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
        Schema::dropIfExists('jawabans');
    }
};