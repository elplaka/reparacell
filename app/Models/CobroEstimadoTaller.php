<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipoTaller;

class CobroEstimadoTaller extends Model
{
    use HasFactory;

    protected $table = 'cobros_estimados_taller';

    protected $primaryKey = ['id', 'num_orden'];
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'num_orden',
        'cobro_estimado'
    ];

    public function equipoTaller()
    {
        return $this->belongsTo(EquipoTaller::class, 'num_orden');
    }
}
