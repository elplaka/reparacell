<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VentaCredito;
use App\Models\User;

class VentaCreditoDetalle extends Model
{
    use HasFactory;

    protected $table = "ventas_credito_detalles";

    protected $fillable = [
        'id',
        'id_abono',
        'abono',
        'id_usuario_venta'
    ];

    public function ventaCredito()
    {
        return $this->belongsTo(VentaCredito::class, 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_venta');
    }

}
