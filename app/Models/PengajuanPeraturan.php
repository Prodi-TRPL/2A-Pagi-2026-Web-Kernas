<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPeraturan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_peraturan';
    protected $fillable = ['id_pengajuan', 'id_peraturan', 'created_at'];
    public $timestamps = false; // Only has created_at
}
