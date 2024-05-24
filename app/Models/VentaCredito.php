<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Venta;
use App\Models\EstatusVentaCredito;

class VentaCredito extends Model
{
    use HasFactory;

    protected $table = "ventas_credito";

    protected $fillable = [
        'id',
        'id_estatus',
    ];

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id');
    }

    public function estatus()
    {
        return $this->belongsTo(EstatusVentaCredito::class, 'id_estatus');
    }

}
