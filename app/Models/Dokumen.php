<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';
    
    protected $guarded = ['id'];

    public function nomorDokumen()
    {
        return $this->belongsTo(NomorDokumen::class, 'id_nomor_dokumen');
    }

    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function anggotaDokumen()
    {
        return $this->belongsToMany(Pengguna::class, 'anggota_dokumen', 'id_dokumen', 'id_pengguna');
    }

    public function grupVerifikasiDokumen()
    {
        return $this->belongsToMany(GrupVerifikasi::class, 'grup_verifikasi_dokumen', 'id_dokumen', 'id_grup_verifikasi');
    }
}
