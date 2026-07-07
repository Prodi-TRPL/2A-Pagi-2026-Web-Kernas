<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';
    
    protected $guarded = ['id'];

    // ini untuk mendefinisikan relasi 'belongsTo' antara Dokumen dan NomorDokumen
    public function nomorDokumen()
    {
        return $this->belongsTo(NomorDokumen::class, 'id_nomor_dokumen');
    }

    // ini untuk mendefinisikan relasi 'belongsTo' antara Dokumen dan Pengguna (pembuat dokumen)
    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    // ini untuk mendefinisikan relasi 'belongsToMany' antara Dokumen dan Pengguna (anggota terkait dokumen)
    public function anggotaDokumen()
    {
        return $this->belongsToMany(Pengguna::class, 'anggota_dokumen', 'id_dokumen', 'id_pengguna');
    }

    // ini untuk mendefinisikan relasi 'belongsToMany' antara Dokumen dan Pengguna (grup verifikasi terkait dokumen)
    public function grupVerifikasiDokumen()
    {
        return $this->belongsToMany(Pengguna::class, 'grup_verifikasi_dokumen', 'id_dokumen', 'id_pengguna');
    }
}
