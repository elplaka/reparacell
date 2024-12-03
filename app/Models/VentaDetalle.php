<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Venta;
use App\Models\VentaProductoComun;
use App\Models\Producto;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'ventas_detalles';

    protected $fillable = [
        'id_venta',
        'codigo_producto' ,
        'cantidad', 
        'importe',
    ];


    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'codigo_producto');
    }

    public function productoComun()
    {
        return $this->hasOne(VentaProductoComun::class, 'id_venta', 'id_venta')
                    ->where('codigo_producto', $this->codigo_producto);
    }


}
