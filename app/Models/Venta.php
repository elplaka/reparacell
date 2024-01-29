<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VentaDetalle;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_cliente', 
        'fecha', 
        'total',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta');
    }
}
