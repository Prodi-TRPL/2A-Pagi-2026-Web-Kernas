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

    public function grupVerifikasi()
    {
        return $this->belongsToMany(
            GrupVerifikasi::class,
            'anggota_grup_verifikasi',
            'id_pengguna',
            'id_grup_verifikasi'
        );
    }

    /**
     *  check untuk verifikator (ini solusi sementara tolong perbaiki sebelum deploy pls) (nah man)
     *
     * @return bool
     */
    public function isVerifikator()
    {
        return $this->grupVerifikasi()->exists();
    }

    /**
     * mengambil id dari tabel
     *
     * @return array
     */
    public function getGrupIds()
    {
        return $this->grupVerifikasi()->pluck('grup_verifikasi.id')->toArray();
    }
}
