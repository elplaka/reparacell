<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FallaEquipo extends Model
{
    use HasFactory;

    protected $table = 'fallas_equipos';

    public function fallasEquiposTaller()
    {
        return $this->hasMany(FallaEquipoTaller::class, 'id_falla', 'id');
    }

    
}
