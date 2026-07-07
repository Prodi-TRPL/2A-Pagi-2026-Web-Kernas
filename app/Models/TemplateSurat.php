<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    protected $table = 'template_surat';
    
    protected $guarded = ['id'];

    // ini untuk mendefinisikan relasi 'belongsTo' antara TemplateSurat dan Pengguna (pembuat template)
    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
