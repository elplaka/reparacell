<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\CobroEstimadoTaller;
use App\Models\EquipoTaller;
use App\Models\CobroTaller;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\EstatusCobroTallerCredito;


class CobroTallerCredito extends Model
{
    use HasFactory;

    protected $table = "cobros_taller_credito";

    protected $primaryKey = 'num_orden'; 

    protected $fillable = [
        'num_orden',
        'id_cliente',
        'id_estatus',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function detalles()
    {
        return $this->hasMany(CobroTallerCreditoDetalle::class, 'num_orden');
    }

    public function cobroEstimado()
    {
        return $this->hasOne(CobroEstimadoTaller::class, 'num_orden');
    }

    public function equipoTaller()
    {
        return $this->hasOne(EquipoTaller::class, 'num_orden');
    }

    public function cobroTaller()
    {
        return $this->hasOne(CobroTaller::class, 'num_orden');
    }

    public function estatus()
    {
        return $this->belongsTo(EstatusCobroTallerCredito::class, 'id_estatus');
    }


}
