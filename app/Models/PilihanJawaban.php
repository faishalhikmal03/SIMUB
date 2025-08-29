<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pilihan_jawabans';

    /**
     * Menunjukkan jika model harus diberi stempel waktu.
     * Diatur ke false karena tabel ini tidak memiliki kolom created_at dan updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pertanyaan_id',
        'pilihan',
        'next_section_id', // Kolom penting untuk fungsionalitas pertanyaan kondisional
    ];

    /**
     * Mendapatkan pertanyaan yang memiliki pilihan jawaban ini.
     */
    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }

    /**
     * Mendapatkan section berikutnya yang dituju oleh pilihan ini (jika ada).
     */
    public function nextSection()
    {
        return $this->belongsTo(Section::class, 'next_section_id');
    }
}

