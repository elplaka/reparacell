<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoEquipo;
use App\Models\ModeloEquipo;

class MarcaEquipo extends Model
{
    use HasFactory;

    protected $table = 'marcas_equipos';

    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'id_tipo_equipo');
    }

    public function modelos()
    {
        return $this->hasMany(ModeloEquipo::class, 'id', 'id_modelo');
    }
}
