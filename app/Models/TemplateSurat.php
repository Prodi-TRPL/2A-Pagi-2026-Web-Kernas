<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    protected $table = 'template_surat';
    
    protected $guarded = ['id'];

    public function pembuat()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function masukan()
    {
        return $this->belongsToMany(Masukan::class, 'template_masukan', 'id_template_surat', 'id_masukan');
    }
}
