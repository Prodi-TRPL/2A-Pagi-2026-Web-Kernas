<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    
    protected $guarded = ['id'];



    // ini untuk mendefinisikan relasi 'belongsTo' ke entitas Pengguna sebagai pihak yang mengajukan
    public function pengaju()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    // ini untuk mendefinisikan relasi 'belongsTo' ke entitas GrupVerifikasi sebagai grup yang memverifikasi pengajuan
    public function grupVerifikator()
    {
        return $this->belongsTo(GrupVerifikasi::class, 'id_grup_verifikasi_verifikator');
    }

    // ini untuk mendefinisikan relasi 'belongsToMany' ke entitas Pengguna untuk anggota yang terlibat dalam pengajuan
    public function anggotaPengajuan()
    {
        return $this->belongsToMany(Pengguna::class, 'anggota_pengajuan', 'id_pengajuan', 'id_pengguna');
    }

    // ini untuk mendefinisikan relasi 'belongsToMany' ke entitas Pengguna untuk grup verifikasi yang terlibat dalam pengajuan
    public function grupVerifikasiPengajuan()
    {
        return $this->belongsToMany(Pengguna::class, 'grup_verifikasi_pengajuan', 'id_pengajuan', 'id_pengguna');
    }
}
