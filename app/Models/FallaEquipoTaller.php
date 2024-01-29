<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FallaEquipo;

class FallaEquipoTaller extends Model
{
    use HasFactory;

    protected $table = 'fallas_equipos_taller';
    protected $primaryKey = ['num_orden', 'id_falla'];

    public $incrementing = false;

    public function falla()
    {
        return $this->belongsTo(FallaEquipo::class, 'id_falla', 'id');
    }

    public function equiposTaller()
    {
        return $this->hasMany(EquipoTaller::class, 'num_orden', 'num_orden');

    }

}

