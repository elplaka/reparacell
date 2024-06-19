<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;
use App\Models\TipoMovimientoInventario;
use App\Models\User;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'id_tipo_movimiento',
        'codigo_producto',
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

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'codigo_producto');
    }

    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimientoInventario::class, 'id_tipo_movimiento');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_movimiento');
    }
}
