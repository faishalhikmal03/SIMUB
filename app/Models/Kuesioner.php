<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuesioner extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'target_user',
        'status',
        'bisa_diisi_ulang',
    ];

    /**
     * Mengatur tipe data atribut untuk casting otomatis.
     */
    protected $casts = [
        'bisa_diisi_ulang' => 'boolean',
    ];

    /**
     * Mendefinisikan relasi "one-to-many":
     * Satu Kuesioner memiliki banyak Section.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Mendefinisikan relasi "has-many-through":
     * Satu Kuesioner memiliki banyak Pertanyaan melalui model Section.
     */
   public function pertanyaans()
    {
        return $this->hasManyThrough(Pertanyaan::class, Section::class);
    } 
    
    // --- PENAMBAHAN FUNGSI BARU ---
    
    /**
     * Mendefinisikan relasi "one-to-many" ke StatusPengisian.
     * Satu Kuesioner bisa memiliki banyak status pengisian dari banyak pengguna.
     * Ini dibutuhkan oleh controller untuk memeriksa kuesioner mana yang sudah diisi.
     */
    public function statusPengisian()
    {
        return $this->hasMany(StatusPengisian::class);
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class);
    }
}
