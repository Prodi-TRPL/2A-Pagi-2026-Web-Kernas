<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';
    
    protected $guarded = ['id'];
    
    protected $hidden = [
        'password',
    ];

    // ini untuk mendefinisikan relasi 'belongsToMany' ke entitas GrupVerifikasi
    public function grupVerifikasi()
    {
        return $this->belongsToMany(
            GrupVerifikasi::class,
            'anggota_grup_verifikasi',
            'id_pengguna',
            'id_grup_verifikasi'
        );
    }

    // ini untuk memeriksa apakah pengguna saat ini termasuk dalam grup verifikator
    public function isVerifikator()
    {
        return $this->grupVerifikasi()->exists();
    }

    // ini untuk mengambil array ID grup verifikasi yang terkait dengan pengguna
    public function getGrupIds()
    {
        return $this->grupVerifikasi()->pluck('grup_verifikasi.id')->toArray();
    }
}
