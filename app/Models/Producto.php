<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DepartamentoProducto;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'codigo';
    public $timestamps = false;

    protected $casts = [
        'codigo' => 'string',
    ];

    protected $fillable = [
        'descripcion',
        'precio_costo',
        'precio_venta',
        'precio_mayoreo',
        'inventario',
        'inventario_minimo',
        'id_departamento'
    ];

    public function departamento()
    {
        return $this->belongsTo(DepartamentoProducto::class, 'id_departamento');
    }

}
