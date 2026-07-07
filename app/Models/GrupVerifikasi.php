<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupVerifikasi extends Model
{
    protected $table = 'grup_verifikasi';
    
    protected $guarded = ['id'];
    
    // ini untuk mengambil pengguna berdasarkan grup verifikasi
    public function pengguna()
    {
        return $this->belongsToMany(
            Pengguna::class,
            'anggota_grup_verifikasi',
            'id_grup_verifikasi',
            'id_pengguna'
        );
    }
}
