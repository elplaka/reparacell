<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\VentaCredito;
use App\Models\VentaCreditoDetalle;
use App\Models\EstatusVentaCredito;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VentaCreditoLw extends Component
{
    use WithPagination;

    protected $listeners = [
        'lisLiquidarVentaCredito' => 'liquidarVentaCredito',
        'lisBorraAbono' => 'borraAbono',
    ]; 

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $muestraDivAbono, $detallesCredito;
    public $datosCargados, $muestraDivAgregaBono;
    public $sumaAbonos, $montoLiquidar;

    public $busquedaCreditos =
    [
        'fechaVentaInicio' => null,
        'fechaVentaFin' => null,
        'idEstatus' => null,
        'nombreCliente' => null,
    ];

    public $ventaCredito = 
    [
        'nombreCliente' => null,
        'id' => null,
        'idEstatus' => null,
        'estatus' => null,
        'monto' => null,
        'abono' => null,
        'idAbonoSeleccionado' => null
    ];

    public function render()
    {
        $creditosQuery = VentaCredito::query();

        $creditosQuery->whereDate('created_at', '>=', $this->busquedaCreditos['fechaVentaInicio'])
        ->whereDate('created_at', '<=', $this->busquedaCreditos['fechaVentaFin']);

        if (!is_null($this->ventaCredito['id']))
        {
            $this->detallesCredito = VentaCreditoDetalle::where('id', $this->ventaCredito['id'])
            ->where('id_abono', '>', 0)->get();
        }

        if (!is_null($this->busquedaCreditos['nombreCliente'] && $this->busquedaCreditos['nombreCliente'] != ''))
        {
            $nombreCliente = $this->busquedaCreditos['nombreCliente'];
            $creditosQuery->whereHas('venta.cliente', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'like', "%$nombreCliente%");
            });
        }

        if ($this->busquedaCreditos['idEstatus'] != 0)
        {
            $creditosQuery->where('id_estatus', $this->busquedaCreditos['idEstatus']);
        }

        $creditos = $creditosQuery->paginate(10);

        $estatus = EstatusVentaCredito::all();

        return view('livewire.ventas.creditos', compact('creditos', 'estatus'));
    }

    public function preguntaBorraAbono($idVenta, $idAbono)
    {
        $this->ventaCredito['idAbonoSeleccionado'] = $idAbono;
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas eliminar el abono seleccionado?', 'lisBorraAbono');
    }

    public function liquidaCredito()
    {
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas liquidar el crédito?', 'lisLiquidarVentaCredito');
    }

    public function liquidarVentaCredito()
    {
       $idVenta = $this->ventaCredito['id'];

       try 
       {
           DB::transaction(function () use ($idVenta) 
           {
               $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)->get();
               $ultimoIdAbono = $this->detallesCredito->max('id_abono');
               $this->ventaCredito['monto'] = $this->detallesCredito->first()->ventaCredito->venta->total;
               $this->sumaAbonos = $this->detallesCredito->sum('abono');
               $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;
       
               $ventaCreditoDetalles = new VentaCreditoDetalle();
               $ventaCreditoDetalles->id = $idVenta;
               $ventaCreditoDetalles->id_abono = $ultimoIdAbono + 1;
               $ventaCreditoDetalles->abono = $this->montoLiquidar;
               $ventaCreditoDetalles->id_usuario_venta = Auth::id();
               $ventaCreditoDetalles->save();

                VentaCredito::where('id', $idVenta)->update(['id_estatus' => 2]);
                $this->ventaCredito['estatus'] = $ventaCreditoDetalles->first()->ventaCredito->estatus->descripcion;
                $this->ventaCredito['idEstatus'] = 2;

                $this->muestraDivAbono = false;
                $this->ventaCredito['abono'] = null;

                $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)->get();

                session()->flash('success', 'El crédito ha sido LIQUIDADO exitosamente.');
           });
       } catch (\Exception $e)
       {
               // Manejo de errores si ocurre una excepción
               dd($e);
       }
    }

    public function borraAbono()
    {
        $idVenta = $this->ventaCredito['id'];
        $idAbono = $this->ventaCredito['idAbonoSeleccionado'];

        $detalleCredito =  VentaCreditoDetalle::where('id', $idVenta)
        ->where('id_abono', $idAbono)
        ->delete();

        if ($detalleCredito)
        {
            $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                                 ->where('id_abono', '>', 0)->get();

            $this->sumaAbonos = $this->detallesCredito->sum('abono');
            $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

            VentaCredito::where('id', $idVenta)->update(['id_estatus' => 1]);

            $this->ventaCredito['idEstatus'] = 1;
            $this->ventaCredito['estatus'] = "SIN LIQUIDAR";

            session()->flash('success', 'El ABONO se ha ELIMINADO con éxito.');
        }
        else
        {
            $this->addError('abono', 'El abono seleccionado no existe o hubo problemas con la base de datos.');
        }
    }

    public function abreVentaCredito($id)
    {
        $creditoVenta = VentaCredito::find($id);

        $this->ventaCredito['nombreCliente'] = $creditoVenta->venta->cliente->nombre;
        $this->ventaCredito['id'] = $creditoVenta->id;
        $this->ventaCredito['idEstatus'] = $creditoVenta->id_estatus;
        $this->ventaCredito['estatus'] = $creditoVenta->estatus->descripcion;
        $this->ventaCredito['monto'] = $creditoVenta->venta->total;

        $this->detallesCredito = VentaCreditoDetalle::where('id', $id)
                                 ->where('id_abono', '>', 0)->get();

        $this->sumaAbonos = $this->detallesCredito->sum('abono');
        $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

        $this->datosCargados = true;

        $this->muestraDivAbono = false;

        $this->showModalErrors = true;
        $this->showMainErrors = !$this->showModalErrors;
    }

    public function cierraVentaCreditoModal()
    {
        $this->datosCargados = false;
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

    public function muestraDivAgregaAbono()
    {
        $this->muestraDivAbono = true;
    }

    public function agregaAbono()
    {
        if (floatval($this->ventaCredito['abono']) > 0)  //Si el abono es mayor que cero
        {
            //Para saber si se sobrepasa el monto a pagar
            $acumulado =$this->sumaAbonos + $this->ventaCredito['abono'];  
            $idVenta = $this->ventaCredito['id'];

            if ($acumulado > $this->ventaCredito['monto'])
            {
                $this->muestraDivAbono = false;
                $this->ventaCredito['abono'] = null;
                $this->addError('abono', 'Debes capturar un monto menor en el abono.');
                $this->dispatch('muestraBotonAgregarPago');
            }
            else
            {
                try 
                {
                    DB::transaction(function () use ($idVenta, $acumulado) 
                    {
                        $ultimoIdAbono = VentaCreditoDetalle::where('id', $idVenta)->max('id_abono');

                        $ventaCreditoDetalles = new VentaCreditoDetalle();
                        $ventaCreditoDetalles->id = $idVenta;
                        $ventaCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        $ventaCreditoDetalles->abono = $this->ventaCredito['abono'];
                        $ventaCreditoDetalles->id_usuario_venta = Auth::id();
                        $ventaCreditoDetalles->save();

                        $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                        ->where('id_abono', '>', 0)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

                        if ($acumulado == $this->ventaCredito['monto'])
                        {
                            VentaCredito::where('id', $idVenta)->update(['id_estatus' => 2]);
                            $this->ventaCredito['estatus'] = $ventaCreditoDetalles->first()->ventaCredito->estatus->descripcion;
                            $this->ventaCredito['idEstatus'] = 2;
                        }
                    });
                } catch (\Exception $e)
                {
                        // Manejo de errores si ocurre una excepción
                        dd($e);
                }
                $this->muestraDivAbono = false;
                $this->ventaCredito['abono'] = null;

                session()->flash('success', 'El ABONO ha sido agregado exitosamente.');
            }
        }
        else
        {
            if (strlen(trim($this->ventaCredito['abono'])) == 0)
            {
                $this->addError('abono', 'Debes capturar el abono.');
            }
            else
            {
                $this->addError('abono', 'El abono debe ser mayor que cero.');
            }
        }
    }
}
