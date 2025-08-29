<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuesioner extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'target_user',
        'status',
        'bisa_diisi_ulang', // Menambahkan field dari fungsionalitas baru
    ];

    /**
     * Mengatur tipe data atribut untuk casting otomatis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bisa_diisi_ulang' => 'boolean', // Praktik terbaik untuk memastikan tipe data
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
}
