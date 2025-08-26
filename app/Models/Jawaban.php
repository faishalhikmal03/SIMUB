<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'kuesioner_id',
        'pertanyaan_id',
        'jawaban_text',
        'pilihan_jawaban_id',
    ];

    /**
     * Mendefinisikan relasi "many-to-one":
     * Satu Jawaban ini milik satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi "many-to-one":
     * Satu Jawaban ini milik satu Kuesioner.
     */
    public function kuesioner()
    {
        return $this->belongsTo(Kuesioner::class);
    }

    /**
     * Mendefinisikan relasi "many-to-one":
     * Satu Jawaban ini milik satu Pertanyaan.
     */
    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }
    
    /**
     * Mendefinisikan relasi "many-to-one":
     * Satu Jawaban bisa jadi milik satu Pilihan Jawaban.
     */
    public function pilihanJawaban()
    {
        return $this->belongsTo(PilihanJawaban::class);
    }
}
