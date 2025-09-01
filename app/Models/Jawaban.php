<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'submission_uuid',
        'user_id',
        'kuesioner_id',
        'section_id',
        'pertanyaan_id',
        'jawaban_text',       // Untuk jawaban tipe teks/paragraf
        'pilihan_jawaban_id', // Untuk jawaban tipe single_option/checkbox/pilihan_dosen
        'dosen_id',           // <-- MODIFIKASI: Menyimpan ID dosen yang sedang dinilai
    ];

    // --- Relasi yang Sudah Ada (Tidak Berubah) ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kuesioner()
    {
        return $this->belongsTo(Kuesioner::class);
    }
    
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }

    /**
     * Relasi ke 'kamus' pilihan jawaban.
     * Menghubungkan kolom 'pilihan_jawaban_id' ke tabel 'pilihan_jawabans'.
     */
    public function pilihanJawaban()
    {
        return $this->belongsTo(PilihanJawaban::class);
    }

    // --- RELASI BARU YANG PENTING ---

    /**
     * Relasi untuk mengambil data user (dosen) yang SEDANG DINILAI dalam sesi ini.
     * Menghubungkan kolom 'dosen_id' di tabel ini ke tabel 'users'.
     */
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}