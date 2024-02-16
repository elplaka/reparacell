<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\User;

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

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
