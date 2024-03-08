<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'id_tipo_movimiento',
        'id_producto',
        'existencia_anterior',
        'existencia_movimiento',
        'existencia_minima_anterior',
        'existencia_minima_movimiento',
        'precio_costo_anterior',
        'precio_costo_movimiento',
        'precio_venta_anterior',
        'precio_venta_movimiento',
        'precio_mayoreo_anterior',
        'precio_mayoreo_movimiento',
    ];
}
