<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoMovimientoCaja;
use App\Models\User;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    // Clave primaria compuesta definida como null
    protected $primaryKey = null;
    public $timestamps = false;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'referencia',
        'fecha',
        'id_tipo',
        'monto',
        'saldo_caja',
        'id_usuario'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoMovimientoCaja::class, 'id_tipo');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function getKeyName()
    {
        return 'referencia';  // Retorna una sola clave primaria para evitar confusiones
    }

    protected function setKeysForSaveQuery($query)
    {
        $query->where('referencia', '=', $this->getAttribute('referencia'))
              ->where('fecha', '=', $this->getAttribute('fecha'));

        return $query;
    }
}

