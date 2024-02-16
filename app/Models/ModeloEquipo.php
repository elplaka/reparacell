<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MarcaEquipo;


class ModeloEquipo extends Model
{
    use HasFactory;

    protected $table = 'modelos_equipos';

    protected $fillable = [
        'id_marca',
        'nombre',
        'disponible'
    ];

    public function marca()
    {
        return $this->belongsTo(MarcaEquipo::class, 'id_marca');
    }
}
