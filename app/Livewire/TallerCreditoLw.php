<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CobroTallerCredito;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\EstatusCobroTallerCredito;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TallerCreditoLw extends Component
{
    public $datosCargados;
    public $showModalErrors, $showMainErrors;
    public $muestraDivAbono;
    public $detallesCredito;
    public $sumaAbonos, $montoLiquidar;

    public $busquedaCreditos =
    [
        'fechaVentaInicio' => null,
        'fechaVentaFin' => null,
        'idEstatus' => null,
        'nombreCliente' => null,
    ];

    public $cobroACredito = 
    [
        'nombreCliente' => null,
        'numOrden' => null,
        'tipoEquipo' => null,
        'marcaEquipo' => null,
        'modeloEquipo' => null,
        'idEstatus' => null,
        'estatus' => null,
        'monto' => null,
        'abono' => null,
        'idAbonoSeleccionado' => null
    ];

    public function cierraCobroCreditoTallerModal()
    {
        $this->datosCargados = false;
    }

    public function muestraDivAgregaAbono()
    {
        $this->muestraDivAbono = true;
    }

    public function agregaAbono()
    {
        if (floatval($this->cobroACredito['abono']) > 0)  //Si el abono es mayor que cero
        {
            //Para saber si se sobrepasa el monto a pagar
            $acumulado =$this->sumaAbonos + $this->cobroACredito['abono'];  
            $numOrden = $this->cobroACredito['numOrden'];

            if ($acumulado > $this->cobroACredito['monto'])
            {
                $this->muestraDivAbono = false;
                $this->cobroACredito['abono'] = null;
                $this->addError('abono', 'Debes capturar un monto menor en el abono.');
                $this->dispatch('muestraBotonAgregarPago');
            }
            else
            {
                try 
                {
                    DB::transaction(function () use ($numOrden, $acumulado) 
                    {
                        $ultimoIdAbono = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->max('id_abono');

                        $cobroCreditoDetalles = new CobroTallerCreditoDetalle();
                        $cobroCreditoDetalles->num_orden = $numOrden;
                        $cobroCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        $cobroCreditoDetalles->abono = $this->cobroACredito['abono'];
                        $cobroCreditoDetalles->id_usuario_cobro = Auth::id();
                        $cobroCreditoDetalles->save();

                        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)
                        ->where('id_abono', '>', 0)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

                        if ($acumulado == $this->cobroACredito['monto'])
                        {
                            CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 2]);
                            $this->cobroACredito['estatus'] = $ventaCreditoDetalles->first()->ventaCredito->estatus->descripcion;
                            $this->ventaCredito['idEstatus'] = 2;
                        }
                    });
                } catch (\Exception $e)
                {
                        // Manejo de errores si ocurre una excepción
                        dd($e);
                }
                $this->muestraDivAbono = false;
                $this->cobroACredito['abono'] = null;

                session()->flash('success', 'El ABONO ha sido agregado exitosamente.');
            }
        }
        else
        {
            if (strlen(trim($this->cobroACredito['abono'])) == 0)
            {
                $this->addError('abono', 'Debes capturar el abono.');
            }
            else
            {
                $this->addError('abono', 'El abono debe ser mayor que cero.');
            }
        }
    }

    public function abreTallerCredito($numOrden)
    {
        $this->cobroACredito['numOrden'] = $numOrden;

        $creditoTaller = CobroTallerCredito::where('num_orden', $this->cobroACredito['numOrden'])->first();

        $this->cobroACredito['nombreCliente'] = $creditoTaller->equipoTaller->equipo->cliente->nombre;
        $this->cobroACredito['id'] = $creditoTaller->id;
        $this->cobroACredito['idEstatus'] = $creditoTaller->id_estatus;
        $this->cobroACredito['estatus'] = $creditoTaller->estatus->descripcion;
        $this->cobroACredito['monto'] = $creditoTaller->cobroTaller->cobro_realizado;
        $this->cobroACredito['tipoEquipo'] = $creditoTaller->equipoTaller->equipo->tipo_equipo->nombre;
        $this->cobroACredito['marcaEquipo'] = $creditoTaller->equipoTaller->equipo->marca->nombre;
        $this->cobroACredito['modeloEquipo'] = $creditoTaller->equipoTaller->equipo->modelo->nombre;

        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)
                                 ->where('id_abono', '>', 0)->get();

        $this->sumaAbonos = $this->detallesCredito->sum('abono');
        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

        $this->datosCargados = true;

        $this->muestraDivAbono = false;

        $this->showModalErrors = true;
        $this->showMainErrors = !$this->showModalErrors;
    }

    public function render()
    {
        $creditosQuery = CobroTallerCredito::query();

        $creditosQuery->whereDate('created_at', '>=', $this->busquedaCreditos['fechaVentaInicio'])
        ->whereDate('created_at', '<=', $this->busquedaCreditos['fechaVentaFin']);

        if (!is_null($this->busquedaCreditos['nombreCliente'] && $this->busquedaCreditos['nombreCliente'] != ''))
        {
            $nombreCliente = $this->busquedaCreditos['nombreCliente'];
            $creditosQuery->whereHas('equipoTaller.equipo.cliente', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'like', "%$nombreCliente%");
            });
        }

        if ($this->busquedaCreditos['idEstatus'] != 0)
        {
            $creditosQuery->where('id_estatus', $this->busquedaCreditos['idEstatus']);
        }
        
        $creditos = $creditosQuery->paginate(10);

        $estatus = EstatusCobroTallerCredito::all();

        return view('livewire.taller.creditos', compact('creditos', 'estatus'));
    }

    public function mount()
    {
        $this->busquedaCreditos = [
            'fechaVentaInicio' => now()->subDays(30)->toDateString(),
            'fechaVentaFin' => now()->toDateString(),
            'nombreCliente' => '',
            'idEstatus' => 0
        ];

        $this->datosCargados = false;

        $this->muestraDivAbono = false;
    }
}
