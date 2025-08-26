<?php
// 1. Buat file baru: app/Models/Section.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'kuesioner_id',
        'judul',
        'deskripsi',
        'urutan',
    ];

    public function kuesioner()
    {
        return $this->belongsTo(Kuesioner::class);
    }

    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class);
    }
}