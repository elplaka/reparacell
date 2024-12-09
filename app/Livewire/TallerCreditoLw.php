<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CobroTallerCredito;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\EstatusCobroTallerCredito;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\WithPagination;
use Livewire\Attributes\On; 

class TallerCreditoLw extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $datosCargados;
    public $showModalErrors, $showMainErrors;
    public $muestraDivAbono;
    public $detallesCredito;
    public $sumaAbonos, $montoLiquidar;

    protected $listeners = [
        'lisLiquidarCobroTallerCredito' => 'liquidarCobroTallerCredito',
        'lisBorraAbono' => 'borraAbono',
    ]; 

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
        'abono' => 0,
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

    public function liquidaCredito()
    {
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas liquidar el crédito?', 'lisLiquidarCobroTallerCredito');
    }

    public function preguntaBorraAbono($numOrden, $idAbono)
    {
        $this->cobroACredito['idAbonoSeleccionado'] = $idAbono;
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas eliminar el abono seleccionado?', 'lisBorraAbono');
    }

    public function liquidarCobroTallerCredito()
    {
       $numOrden = $this->cobroACredito['numOrden'];

       try 
       {
           DB::transaction(function () use ($numOrden) 
           {
               $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();
               $ultimoIdAbono = $this->detallesCredito->max('id_abono');
               $this->cobroACredito['monto'] = $this->detallesCredito->first()->cobroCredito->cobroTaller->cobro_realizado;
               $this->sumaAbonos = $this->detallesCredito->sum('abono');
               $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;
       
               $cobroACreditoDetalles = new CobroTallerCreditoDetalle();
               $cobroACreditoDetalles->num_orden = $numOrden;
               $cobroACreditoDetalles->id_abono = $ultimoIdAbono + 1;
               $cobroACreditoDetalles->abono = $this->montoLiquidar;
               $cobroACreditoDetalles->id_usuario_cobro = Auth::id();
               $cobroACreditoDetalles->save();

                CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 2]);
                $this->cobroACredito['estatus'] = $cobroACreditoDetalles->first()->cobroCredito->estatus->descripcion;
                $this->cobroACredito['idEstatus'] = 2;

                $this->muestraDivAbono = false;
                $this->cobroACredito['abono'] = null;

                $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

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
        $numOrden = $this->cobroACredito['numOrden'];
        $idAbono = $this->cobroACredito['idAbonoSeleccionado'];

        try 
        {
            DB::transaction(function () use ($numOrden, $idAbono) 
            {
                $detalleCredito =  CobroTallerCreditoDetalle::where('num_orden', $numOrden)
                ->where('id_abono', $idAbono)
                ->delete();

                if ($detalleCredito)
                {
                    $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)
                                        ->where('id_abono', '>', 0)->get();

                    $this->sumaAbonos = $this->detallesCredito->sum('abono');
                    $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

                    CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 1]);

                    $this->cobroACredito['idEstatus'] = 1;
                    $this->cobroACredito['estatus'] = "SIN LIQUIDAR";

                    session()->flash('success', 'El ABONO se ha ELIMINADO con éxito.');
                }
                else
                {
                    $this->addError('abono', 'El abono seleccionado no existe o hubo problemas con la base de datos.');
                }
            });
        } catch (\Exception $e)
        {
                // Manejo de errores si ocurre una excepción
                dd($e);
        }
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

                        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

                        if ($acumulado == $this->cobroACredito['monto'])
                        {
                            CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 2]);
                            $this->cobroACredito['estatus'] = $cobroCreditoDetalles->first()->cobroCredito->estatus->descripcion;
                            $this->cobroACredito['idEstatus'] = 2;
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
        $this->cobroACredito['monto'] = $creditoTaller->cobroTaller ? $creditoTaller->cobroTaller->cobro_realizado: 0;
        if($creditoTaller->equipoTaller->equipo->marca->id_tipo_equipo === $creditoTaller->equipoTaller->equipo->id_tipo)
        {
            $nombreMarca = $creditoTaller->equipoTaller->equipo->marca->nombre;
        }
        else
        {
            $nombreMarca = "*****";
        }

        if($creditoTaller->equipoTaller->equipo->modelo->id_marca === $creditoTaller->equipoTaller->equipo->marca->id)
        {
            $nombreModelo = $creditoTaller->equipoTaller->equipo->modelo->nombre;
        }
        else
        {
            $nombreModelo = "*****";
        }
        $this->cobroACredito['tipoEquipo'] = $creditoTaller->equipoTaller->equipo->tipo_equipo->nombre;
        $this->cobroACredito['marcaEquipo'] = $nombreMarca;
        $this->cobroACredito['modeloEquipo'] = $nombreModelo;

        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

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

        if ($this->busquedaCreditos['idEstatus'] > 0)
        { 
            if ($this->busquedaCreditos['idEstatus'] <= 2)
            {
                $creditosQuery->where('id_estatus', $this->busquedaCreditos['idEstatus'])
                ->has('cobroTaller');
            }
            else
            {
                $creditosQuery->doesntHave('cobroTaller');
            }
        }

        $creditos = $creditosQuery->paginate(10);

        $estatus = EstatusCobroTallerCredito::all();

        return view('livewire.taller.creditos', compact('creditos', 'estatus'));
    }

    public function mount()
    {
        $nombreCliente = session('nombreClienteCredito', '');
        $fechaInicioCredito = session('fechaInicioCredito', now()->subDays(30));
        $fechaFinCredito = session('fechaFinCredito', now());

        $fechaInicioCredito = $fechaInicioCredito->toDateString();
        $fechaFinCredito = $fechaFinCredito->toDateString();

        $this->busquedaCreditos = [
            'fechaVentaInicio' => $fechaInicioCredito,
            'fechaVentaFin' => $fechaFinCredito,
            'nombreCliente' => $nombreCliente,
            'idEstatus' => 0
        ];

        $this->datosCargados = false;

        $this->muestraDivAbono = false;

        $abreModalCreditoTaller = session('sesAbreModalCobroCreditoTaller', false);

        if ($abreModalCreditoTaller)
        {
            $numOrden = session('numOrden', null);
            $this->abreTallerCredito($numOrden);
            $this->dispatch('abreCobroCreditoTallerModal2');
        }
    }

    
    #[On('lisCreditosTallerCliente')] 
    public function abreOrden($numOrden)
    {
        $credito = CobroTallerCredito::where('num_orden', $numOrden)->first();

        if ($credito) {
            $nombreClienteCredito = $credito->equipoTaller->equipo->cliente->nombre;
            $fechaInicioCredito = $credito->created_at;
            $fechaFinCredito = $credito->created_at;

            session()->flash('numOrden', $numOrden);
            session()->flash('nombreClienteCredito', $nombreClienteCredito);
            session()->flash('fechaInicioCredito', $fechaInicioCredito);
            session()->flash('fechaFinCredito', $fechaFinCredito);
            session()->flash('sesAbreModalCobroCreditoTaller', true);

            return redirect()->route('taller.creditos');
        }
    }
}
