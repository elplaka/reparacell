<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipo;
use App\Models\FallaEquipoTaller;
use App\Models\EstatusEquipo;
use App\Models\CobroTaller;
use App\Models\User;
use App\Models\AnotacionEquipoTaller;


class EquipoTaller extends Model
{
    use HasFactory;

    protected $table = "equipos_taller";

    public $timestamps = false;

    protected $dates = ['fecha_entrada', 'fecha_actualizacion'];

    protected $primaryKey = 'num_orden'; 

    protected $fillable = [
        'num_orden',
        'id_equipo', 
        'id_usuario_recibio', 
        'id_estatus',
        'observaciones',
        'fecha_entrada',
        'fecha_actualizacion'
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'id_equipo');
    }

    public function fallas()
    {
        return $this->hasMany(FallaEquipoTaller::class, 'num_orden', 'num_orden');
    }

    public function estatus()
    {
        return $this->belongsTo(EstatusEquipo::class, 'id_estatus');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_recibio');
    }

    public function cobroTaller()
    {
        return $this->hasOne(CobroTaller::class, 'num_orden');
    }

    public function anotacionEquipoTaller()
    {
        return $this->hasOne(AnotacionEquipoTaller::class, 'num_orden', 'num_orden');
    }
}
