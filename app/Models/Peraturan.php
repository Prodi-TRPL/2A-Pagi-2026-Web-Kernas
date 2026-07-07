<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peraturan extends Model
{
    use HasFactory;

    protected $table = 'peraturan';
    protected $fillable = ['id_pengguna', 'kode', 'judul', 'jenis', 'tahun', 'keterangan'];

    // ini untuk mendefinisikan relasi 'belongsTo' antara Peraturan dan Pengguna (pengupload peraturan)
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
