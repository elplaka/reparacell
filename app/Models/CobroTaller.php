<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipoTaller;
use App\Models\CobroTallerCredito;

class CobroTaller extends Model
{
    use HasFactory;

    protected $table = "cobros_taller";

    protected $primaryKey = 'num_orden'; 

    protected $fillable = [
        'num_orden',
        'fecha',
        'cobro_estimado',
        'cobro_realizado',
        'cancelado'
    ];

    public function equipoTaller()
    {
        return $this->belongsTo(EquipoTaller::class, 'num_orden');
    }

    public function credito()
    {
        return $this->hasOne(CobroTallerCredito::class, 'num_orden');
    }
}
