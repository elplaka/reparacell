<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\VentaCredito;
use App\Models\VentaCreditoDetalle;
use App\Models\EstatusVentaCredito;
use App\Models\ModoPago;
use App\Models\MovimientoCaja;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use App\Traits\MovimientoCajaTrait;  //Funciones globales de MOVIMIENTOS EN CAJA

class VentaCreditoLw extends Component
{
    use MovimientoCajaTrait;
    use WithPagination;

    protected $listeners = [
        'lisLiquidarVentaCredito' => 'liquidarVentaCredito',
        'lisBorraAbono' => 'borraAbono',
    ]; 

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $muestraDivAbono, $detallesCredito;
    public $datosCargados, $muestraDivAgregaBono, $modosPagoModal, $ventaModal, $idModoPago;
    public $idVentaModal, $idAbonoModal;
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
        'idAbonoSeleccionado' => null,
        'idModoPago' => null
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
               $ventaCreditoDetalles->id_modo_pago = $this->ventaCredito['idModoPago'];
               $ventaCreditoDetalles->id_usuario_venta = Auth::id();
               $ventaCreditoDetalles->save();

               if ($this->ventaCredito['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
               { 
                   $idRef = $idVenta % 1000;
                   $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
                   $monto = $this->montoLiquidar;
                   $tipoMov = 3;

                   $movimiento = new MovimientoCaja();
                   $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                   $movimiento->id_tipo = $tipoMov;
                   $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                   $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                   $movimiento->id_usuario = Auth::id();
                   $movimiento->save();
               }

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

        try 
        {
            DB::transaction(function () use ($idVenta, $idAbono) 
            {
                $detCredito =  VentaCreditoDetalle::where('id', $idVenta)
                ->where('id_abono', $idAbono)
                ->first();
                
                $detalleCredito =  VentaCreditoDetalle::where('id', $idVenta)
                ->where('id_abono', $idAbono)
                ->delete();

                if ($detalleCredito)
                {
                    $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                                        ->where('id_abono', '>', 0)->get();

                    $this->sumaAbonos = $this->detallesCredito->sum('abono');
                    $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

                    if ($this->ventaCredito['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                    { 
                        $idRef = $idVenta % 1000;
                        $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
                        $monto = $detCredito->abono;
                        $tipoMov = 7;
     
                        $movimiento = new MovimientoCaja();
                        $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                        $movimiento->id_tipo = $tipoMov;
                        $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                        $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                        
                        // Verifica si el saldo_caja es menor que 0
                        if ($movimiento->saldo_caja < 0) {
                            DB::rollBack();
                            $this->dispatch('mostrarToastError', 'No hay SUFICIENTE EFECTIVO en la caja para realizar esta operación!!!');
                        }

                        $movimiento->id_usuario = Auth::id();
                        $movimiento->save();
                    }

                    VentaCredito::where('id', $idVenta)->update(['id_estatus' => 1]);

                    $this->ventaCredito['idEstatus'] = 1;
                    $this->ventaCredito['estatus'] = "SIN LIQUIDAR";

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

    public function cierraModalActualizarModoPago()
    {
        
    }

    public function abrirEditarModoPagoModal($idVenta, $idAbono)
    {
        $this->idVentaModal = $idVenta;
        $this->idAbonoModal = $idAbono;

        $this->ventaModal = VentaCreditoDetalle::where('id', $idVenta)->where('id_abono', $idAbono)->firstOrFail();

        $this->idModoPago =  $this->ventaModal->id_modo_pago;

        $this->dispatch('abreModalEditaModoPagoVentaCredito');
    }

    public function actualizarModoPago()
    {
        $ventaCreditoDetalle = VentaCreditoDetalle::where('id', $this->idVentaModal)
        ->where('id_abono', $this->idAbonoModal)
        ->first();

        $idModoPago = $ventaCreditoDetalle->id_modo_pago;
        $abono = $ventaCreditoDetalle->abono;

        VentaCreditoDetalle::where('id', $this->idVentaModal)
        ->where('id_abono', $this->idAbonoModal)
        ->update(['id_modo_pago' => $this->idModoPago]);

        if ($idModoPago == 1 && $this->idModoPago == 2)  //Si el MODO DE PAGO era EFECTIVO y se cambia a TRANSF.
        {
            $idRef = $this->idVentaModal % 1000;
            $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

            $movimiento = new MovimientoCaja();
            $movimiento->referencia = $this->regresaReferencia(8, $idRef);
            $movimiento->id_tipo = 8;
            $movimiento->monto = $this->calculaMonto(8, $abono);
            $movimiento->saldo_caja = $this->calculaSaldoCaja(8, $abono); // Asegura que el saldo_caja sea un número decimal
            $movimiento->id_usuario = Auth::id();
            $movimiento->save();
        }
        elseif ($idModoPago == 2 && $this->idModoPago == 1) //Si el MODO DE PAGO era TRANSF. y se cambia a EFECTIVO
        {
            $idRef = $this->idVentaModal % 1000;
            $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

            $movimiento = new MovimientoCaja();
            $movimiento->referencia = $this->regresaReferencia(3, $idRef);
            $movimiento->id_tipo = 3;
            $movimiento->monto = $this->calculaMonto(3, $abono);
            $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $abono); // Asegura que el saldo_caja sea un número decimal
            $movimiento->id_usuario = Auth::id();
            $movimiento->save();
        }

        $this->dispatch('cierraModalEditaModoPagoVentaCredito');
        $this->dispatch('mostrarToast', 'Modo de pago actualizado con éxito!!!');
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

        $abreModalCreditoVentas = session('sesAbreModalCreditoVentas', false);

        if ($abreModalCreditoVentas)
        {
            $idVenta = session('idVenta', null);
            $this->abreVentaCredito($idVenta);
            $this->dispatch('abreCobroCreditoVentasModal2');
        }

        $this->ventaCredito = [
            'nombreCliente' => null,
            'id' => null,
            'idEstatus' => null,
            'estatus' => null,
            'monto' => null,
            'abono' => null,
            'idAbonoSeleccionado' => null,
            'idModoPago' => 1
        ];

        $this->modosPagoModal = ModoPago::where('id', '>', 0)->get();
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
                        $ventaCreditoDetalles->id_modo_pago = $this->ventaCredito['idModoPago'];
                        $ventaCreditoDetalles->id_usuario_venta = Auth::id();
                        $ventaCreditoDetalles->save();

                        $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                        ->where('id_abono', '>', 0)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

                        if ($this->ventaCredito['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                        { 
                            $idRef = $idVenta % 1000;
                            $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
                            $monto = $this->ventaCredito['abono'];
                            $tipoMov = 3;
    
                            $movimiento = new MovimientoCaja();
                            $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                            $movimiento->id_tipo = $tipoMov;
                            $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                            $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                            $movimiento->id_usuario = Auth::id();
                            $movimiento->save();
                        }

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
            if (strlen(trim((string) $this->ventaCredito['abono'])) == 0)
            {
                $this->addError('abono', 'Debes capturar el abono.');
            }
            else
            {
                $this->addError('abono', 'El abono debe ser mayor que cero.');
            }
        }
    }

    #[On('lisCreditosVentasCliente')] 
    public function abreVenta($idVenta)
    {
        $credito = Venta::where('id', $idVenta)->first();

        $nombreClienteCredito = $credito->cliente->nombre;
        $fechaInicioCredito = $credito->created_at;
        $fechaFinCredito = $credito->created_at;

        session()->flash('idVenta', $idVenta);
        session()->flash('nombreClienteCredito', $nombreClienteCredito);
        session()->flash('fechaInicioCredito', $fechaInicioCredito);
        session()->flash('fechaFinCredito', $fechaFinCredito);
        session()->flash('sesAbreModalCreditoVentas', true);

        return redirect()->route('ventas.creditos');
    }
}
