<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoEquipo;

class MarcaEquipo extends Model
{
    use HasFactory;

    protected $table = 'marcas_equipos';

    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'id_tipo_equipo');
    }
}
