<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaProductoComun extends Model
{
    use HasFactory;

    protected $table = 'ventas_productos_comun';

    protected $fillable = [
        'id_venta',
        'codigo_producto', 
        'descripcion_producto', 
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'codigo_producto');
    }
}
