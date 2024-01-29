<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipoTaller;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\TipoEquipo;
use App\Models\Cliente;


class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'telefono_cliente', 
        'id_tipo', 
        'id_marca',
        'id_modelo',
    ];

    public function equiposTaller()
    {
        return $this->hasMany(EquipoTaller::class, 'id_equipo');
    }

    public function marca()
    {
        return $this->belongsTo(MarcaEquipo::class, 'id_marca');
    }

    public function modelo()
    {
        return $this->belongsTo(ModeloEquipo::class, 'id_modelo');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function tipo_equipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'id_tipo');
    }
}
