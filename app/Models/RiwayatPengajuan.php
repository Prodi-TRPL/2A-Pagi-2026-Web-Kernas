<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPengajuan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pengajuan';
    public $timestamps = false;

    protected $fillable = [
        'id_pengajuan',
        'id_pengguna',
        'aksi',
        'catatan_aksi',
        'versi',
        'snapshot_konten',
        'created_at'
    ];

    protected $casts = [
        'snapshot_konten' => 'array',
        'created_at' => 'datetime'
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
