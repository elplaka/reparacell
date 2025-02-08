<?php

namespace App\Traits;

use App\Models\TipoMovimientoCaja;
use App\Models\MovimientoCaja;

trait MovimientoCajaTrait
{
    public function regresaReferencia($idTipo, $idRef)  //FALTA AGREGAR EL ID QUE VA PEGADO CON LA INICIAL
    {
        $tipoMovimiento = TipoMovimientoCaja::findOrFail($idTipo);
        
        if($idTipo == 1  || $idTipo == 2  || $idTipo == 3 || $idTipo == 7 || $idTipo == 8)
        {
            $referencia = $tipoMovimiento->inicial . $idRef;
        }
        elseif ($idTipo >= 4  && $idTipo <= 6)
        {
            $referencia = $tipoMovimiento->inicial . "0000";
        } 

        return $referencia;
    }

    public function calculaMonto($idTipo, $monto)
    {
        $ultimoMovimiento = MovimientoCaja::orderBy('fecha', 'desc')->first();
        $saldoCaja = $ultimoMovimiento ? $ultimoMovimiento->saldo_caja : 0;

        $nuevoMonto = $monto;

        if ($idTipo == 4)  //INICIALIZACIÓN se calcula el MONTO
        {
            $nuevoMonto = $monto - $saldoCaja;
        }
        elseif ($idTipo == 6 || $idTipo == 7 || $idTipo == 8)  //En SALIDAS se pone negativo
        {
            $nuevoMonto = $nuevoMonto * (-1);
        }
        
        return $nuevoMonto;
    }

    public function calculaSaldoCaja($idTipo, $monto)
    {
        $ultimoMovimiento = MovimientoCaja::orderBy('fecha', 'desc')->first();
        $saldoCaja = $ultimoMovimiento ? $ultimoMovimiento->saldo_caja : 0;

        $nuevoSaldoCaja = $monto;

        if ($idTipo == 1 || $idTipo == 2 || $idTipo == 3 || $idTipo == 5)  //ENTRADAS
        {
            $nuevoSaldoCaja = $saldoCaja + $monto;
        }
        elseif ($idTipo == 6 || $idTipo == 7 || $idTipo == 8)  //SALIDAS
        {
            $nuevoSaldoCaja = $saldoCaja - $monto;
        }

        if ($nuevoSaldoCaja < 0)
        {
            $this->dispatch('mostrarToastError', 'No hay SUFICIENTE EFECTIVO en la caja para realizar esta operación!!!');
        }
        
        return (float) $nuevoSaldoCaja; 
    }
}
