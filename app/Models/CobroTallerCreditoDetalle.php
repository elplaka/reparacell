<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CobroTallerCredito;
use App\Models\User;
use App\Models\ModoPago;

class CobroTallerCreditoDetalle extends Model
{
    use HasFactory;

    protected $table = "cobros_taller_credito_detalles";

    protected $fillable = [
        'num_orden',
        'id_abono',
        'abono',
        'id_modo_pago',
        'id_usuario_cobro'
    ];

    protected $primaryKey = 'num_orden'; 

    public function cobroCredito()
    {
        return $this->belongsTo(CobroTallerCredito::class, 'num_orden');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_cobro');
    }

    public function modoPago()
    {
        return $this->belongsTo(ModoPago::class, 'id_modo_pago');
    }

}
