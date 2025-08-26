<?php
// 2. Perbarui file: app/Models/Kuesioner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuesioner extends Model
{
    use HasFactory;
    protected $fillable = ['judul', 'deskripsi', 'target_user', 'status'];

    // Relasi baru: Satu Kuesioner memiliki banyak Section
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // Relasi lama 'pertanyaans' tidak lagi valid secara langsung
    // Kita bisa menggunakan hasManyThrough jika diperlukan
    public function pertanyaans()
    {
        return $this->hasManyThrough(Pertanyaan::class, Section::class);
    }
}