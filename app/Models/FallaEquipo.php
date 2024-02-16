<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoEquipo;
use App\Models\FallaEquipoTaller;

class FallaEquipo extends Model
{
    use HasFactory;

    protected $table = 'fallas_equipos';

    public function fallasEquiposTaller()
    {
        return $this->hasMany(FallaEquipoTaller::class, 'id_falla', 'id');
    }

    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'id_tipo_equipo');
    }

    
}
