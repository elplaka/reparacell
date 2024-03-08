<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipoTaller;

class AnotacionEquipoTaller extends Model
{
    use HasFactory;

    protected $table = 'anotaciones_equipos_taller';

    protected $primaryKey = 'num_orden'; // Si num_orden es tu clave primaria

    protected $fillable = [
        'num_orden',
        'contenido',
    ];

    // Relación con la tabla equipos_taller
    public function equipoTaller()
    {
        return $this->belongsTo(EquipoTaller::class, 'num_orden', 'num_orden');
    }
}
