<?php
// 3. Perbarui file: app/Models/Pertanyaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasFactory;
    // Hapus 'kuesioner_id', tambahkan 'section_id'
    protected $fillable = ['section_id', 'pertanyaan', 'tipe_jawaban'];

    // Relasi baru: Satu Pertanyaan milik satu Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function pilihanJawabans()
    {
        return $this->hasMany(PilihanJawaban::class);
    }
}