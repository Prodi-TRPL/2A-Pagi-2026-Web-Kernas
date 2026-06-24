<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    
    protected $guarded = ['id'];

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'DRAFT' => 'Draf',
            'POSTED' => 'Diproses Admin',
            'REVIEWED' => 'Menunggu Verifikasi',
            'REJECTED' => 'Ditolak Admin',
            'REJECTED_BY_VERIFIER' => 'Revisi',
            'PUBLISHED' => 'Diterbitkan',
            default => $this->status
        };
    }

    public function pengaju()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function grupVerifikator()
    {
        return $this->belongsTo(GrupVerifikasi::class, 'id_grup_verifikasi_verifikator');
    }

    public function anggotaPengajuan()
    {
        return $this->belongsToMany(Pengguna::class, 'anggota_pengajuan', 'id_pengajuan', 'id_pengguna');
    }

    public function grupVerifikasiPengajuan()
    {
        return $this->belongsToMany(GrupVerifikasi::class, 'grup_verifikasi_pengajuan', 'id_pengajuan', 'id_grup_verifikasi');
    }
}
