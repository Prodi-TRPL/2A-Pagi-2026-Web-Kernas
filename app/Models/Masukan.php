<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masukan extends Model
{
    protected $table = 'masukan';
    
    // masukan tabel tidak punya created_at updated_at
    public $timestamps = false;

    protected $guarded = ['id'];

    public function templateSurat()
    {
        return $this->belongsToMany(TemplateSurat::class, 'template_masukan', 'id_masukan', 'id_template_surat');
    }
}
