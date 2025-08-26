<?php
// File: app/Models/PilihanJawaban.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanJawaban extends Model
{
    use HasFactory;

    // Nama tabel harus didefinisikan jika tidak mengikuti konvensi jamak
    protected $table = 'pilihan_jawabans';

    // Nonaktifkan timestamps jika tabel tidak memiliki kolom created_at/updated_at
    public $timestamps = false;
    
    // Pastikan semua kolom ini ada, terutama 'pilihan'
    protected $fillable = [
        'pertanyaan_id',
        'pilihan',
    ];

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }
}