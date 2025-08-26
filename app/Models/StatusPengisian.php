<?php
// File: app/Models/StatusPengisian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPengisian extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_pengisians';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'kuesioner_id',
        'status',
    ];

    /**
     * Mendefinisikan relasi "many-to-one":
     * Satu Status Pengisian ini milik satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi "many-to-one":
     * Satu Status Pengisian ini milik satu Kuesioner.
     */
    public function kuesioner()
    {
        return $this->belongsTo(Kuesioner::class);
    }
}
