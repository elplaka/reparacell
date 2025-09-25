<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EquipoTaller;
use App\Models\TipoEquipo;
use App\Models\EstatusEquipo;
use App\Models\ModoPago;
use App\Models\CobroTaller;
use App\Models\CobroEstimadoTaller;
use App\Models\AnotacionEquipoTaller;
use App\Models\CobroTallerCredito;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\MovimientoCaja;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VentaCreditoDetalle;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Traits\MovimientoCajaTrait;  //Funciones globales de MOVIMIENTOS EN CAJA
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Taller extends Component
{
    use MovimientoCajaTrait;
    use WithPagination;

    protected $listeners = [
        'f2-pressed' => 'cobroLiquidar',
        'f3-pressed' => 'cobroCredito',
        'f4-pressed' => 'cobrarSinTicket',
        'f6-pressed' => 'cobrarConTicket',
        'f10-pressed' => 'abrirCorteCaja', 
        'lisLiquidarCobroCredito' => 'liquidarCobroCredito',
        'lisBorraAbono' => 'borraAbono',
        'guardaCambioEstatusEquipo'
    ]; 

    public $muestraDivAgregaEquipo;
    public $numberOfPaginatorsRendered = [];
    public $estatusEquipos;
    public $datosCobroCargados;
    public $detallesCredito;
    public $modalCobroFinalAbierta;
    public $modalCobroCreditoTallerAbierta;
    public $muestraDivAbono;
    public $showMainErrors, $showModalErrors, $usuariosModal, $modosPagoModal;
    public $sumaAbonos, $montoLiquidar;
    public $datosCargados;
    public $abreModalAnotaciones, $equipoTallerModal, $modalCorteCajaAbierta;
    public $estatusModalCambiaEstatus, $modalCambiarEstatusEquipoAbierta;
    public $numOrdenModal, $idAbonoModal, $cobroModal, $idModoPago;

    public function rules()
    {
        return [
            'cobroFinal.cobroRealizado' => 'required|numeric',
        ];
    }

    public $busquedaEquipos =
    [
        'fechaEntradaInicio' => null,
        'fechaEntradaFin' => null,
        'idEstatus' => null,
        'idTipo' => null,
        'entregados' => [],
        'nombreCliente' => null
    ];

    public $cobro = 
    [
        'fechaEntrada' => null,
        'tipoEquipo' => null,
        'marcaEquipo' => null,
        'modeloEquipo' => null,
        'totalEstimado' => null
    ];

    public $cobro2 =
    [
        'fallasEquipo' => null,
    ];

    public $cobroFinal = 
    [
        'numOrden' => null,
        'cliente' => null,
        'fecha' => null,
        'tipoEquipo' => null,
        'marcaEquipo' => null,
        'modeloEquipo' => null,
        'cobroEstimado' => null,
        'cobroRealizado' => 0,
        'fallasEquipo' => [],
        'idEstatusEquipo' => null,
        'anticipo' => null,
        'montoAbonado' => null,
        'restante' => null,
        'publicoGeneral' => null,
        'idModoPago' => 1
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
        'idAbonoSeleccionado' => null,
        'conCobroEstimado' => false,
        'idModoPago' => 1
    ];

    public $anotacionesMod =
    [
        'numOrden' => null,
        'marcaEquipo' => null,
        'modeloEquipo' => null,
        'clienteEquipo' => null,
        'contenido' => '',
        'estatusEquipo' => null,
    ];

    public $corteCaja = [
        'fechaInicial',
        'fechaFinal',
        'cajero',
        'idUsuario',
        'incluyeCredito',
        'incluyeVentas',
        'idModoPago',
        'chkAgrupar'
    ];

    public function abrirWhatsApp($numeroTelefono)
    {
        // Genera la URL de WhatsApp para abrir en el navegador
        $urlWhatsAppWeb = "https://web.whatsapp.com/send?phone={$numeroTelefono}";

        // Redirige al usuario a la URL de WhatsApp para abrir en el navegador
        return redirect()->away($urlWhatsAppWeb);
    }

    public function updated($propertyName, $value)
    {
        if ($this->modalCambiarEstatusEquipoAbierta)
        {
            $this->resetErrorBag('estatusModalCambiaEstatus');
        }
        else if ($this->modalCobroFinalAbierta || $this->modalCobroCreditoTallerAbierta)
        {

        }
        else
        {
            list($property, $index) = explode('.', $propertyName);

            if ($property === 'busquedaEquipos' && $index === 'entregados') 
            {
                $this->busquedaEquipos['idEstatus'] = [];

                if (in_array('entregados', $this->busquedaEquipos['entregados'])) {
                    $this->busquedaEquipos['idEstatus'] = [5, 6];
                }

                if (in_array('no_entregados', $this->busquedaEquipos['entregados'])) {
                    $this->busquedaEquipos['idEstatus'] = array_merge($this->busquedaEquipos['idEstatus'], [1, 2, 3, 4]);
                }

                foreach (range(1, 6) as $i) {
                    if (in_array((string)$i, $this->busquedaEquipos['entregados'])) {
                        $this->busquedaEquipos['idEstatus'][] = $i;
                    }
                }

                $this->busquedaEquipos['idEstatus'] = array_unique($this->busquedaEquipos['idEstatus']);
            } 
        }  
    }


    public function updatedCobroFinalCobroRealizado()
    {
        if (strlen(trim($this->cobroFinal['cobroRealizado'])) == 0) 
        {
            if (isset($this->cobroFinal['anticipo']))
            {
                $this->cobroFinal['restante'] = 0 - $this->cobroFinal['anticipo'];
            }
            else if (isset($this->cobroFinal['montoAbonado']))
            {
                $this->cobroFinal['restante'] = 0 - $this->cobroFinal['montoAbonado'];
            }
            else
            {
                $this->cobroFinal['restante'] = $this->cobroFinal['cobroEstimado'];
            }
        }
        else
        {
            if (isset($this->cobroFinal['anticipo']))
            { 
                $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
            }
            else if (isset($this->cobroFinal['montoAbonado']))
            {
                $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['montoAbonado'];
            }
            else
            {
                $this->cobroFinal['restante'] = $this->cobroFinal['cobroEstimado'];
            }
        }
    }

    public function cierraCobroFinalModal()
    {
        $this->modalCobroFinalAbierta = false;

        // dd($this->muestraDivAgregaEquipo, 
        //          $this->modalCobroFinalAbierta, 
        //          $this->abreModalAnotaciones, 
        //          $this->modalCambiarEstatusEquipoAbierta,
        //          $this->modalCorteCajaAbierta,
        //          $this->modalCobroCreditoTallerAbierta);
    }
    
    public function cobroFinalEquipoTaller($numOrden)
    {
        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

        if ($equipoTaller->id_estatus < 5)
        {
            $cobro = CobroEstimadoTaller::where('num_orden', $numOrden)
            ->orderBy('id', 'desc')
            ->first();

            $this->estatusEquipos = EstatusEquipo::whereIn('id', [5, 6])->get();

            $this->cobroFinal['numOrden'] = $numOrden;

            if ($cobro->equipoTaller->equipo->cliente->disponible)
            {
                $this->cobroFinal['cliente'] = $cobro->equipoTaller->equipo->cliente->nombre;
            }
            else
            {
                $this->cobroFinal['cliente'] = $cobro->equipoTaller->equipo->cliente->nombre . "*";
            }
            $this->cobroFinal['fecha'] = now();

            $this->cobroFinal['tipoEquipo'] =  $cobro->equipoTaller->equipo->tipo_equipo->disponible? $cobro->equipoTaller->equipo->tipo_equipo->nombre : $cobro->equipoTaller->equipo->tipo_equipo->nombre . "*";
            if ($cobro->equipoTaller->equipo->marca->disponible)
            {
                if($cobro->equipoTaller->equipo->marca->id_tipo_equipo === $cobro->equipoTaller->equipo->id_tipo)
                {
                    $this->cobroFinal['marcaEquipo'] = $cobro->equipoTaller->equipo->marca->nombre;
                }
                else
                {
                    $this->cobroFinal['marcaEquipo'] = "*****";
                }
            }
            else
            {
                $this->cobroFinal['marcaEquipo'] = $cobro->equipoTaller->equipo->marca->nombre . "*";
            }

            if ($cobro->equipoTaller->equipo->modelo->disponible)
            {
                if ($cobro->equipoTaller->equipo->modelo->id_marca === $cobro->equipoTaller->equipo->marca->id)
                {
                    $this->cobroFinal['modeloEquipo'] = $cobro->equipoTaller->equipo->modelo->nombre;
                }
                else
                {
                    $this->cobroFinal['modeloEquipo'] = "*****";
                }
            }
            else
            {
                $this->cobroFinal['modeloEquipo'] = $cobro->equipoTaller->equipo->modelo->nombre . "*";
            }

            $this->cobroFinal['cobroEstimado'] = $cobro->cobro_estimado;
            $this->cobroFinal['cobroRealizado'] = $cobro->cobro_estimado;
            $this->cobroFinal['idEstatusEquipo'] = 5;
            $this->cobroFinal['fallasEquipo'][] = null;
            $this->cobroFinal['fallasEquipo'] = $cobro->equipoTaller->fallas;
            $this->cobroFinal['montoAbonado'] = null;
            $this->cobroFinal['anticipo'] = null;
            $this->cobroFinal['idModoPago'] = 1;

            $telefonoContacto = $cobro->equipoTaller->equipo->cliente->telefono_contacto;        
            if ($telefonoContacto == "0000000000")
            {
                $this->cobroFinal['publicoGeneral'] = true;
            }

            $this->cobroFinal['fallasEquipo'] = $cobro->equipoTaller->fallas->map(function ($falla) {
                return [
                    'descripcion' => $falla->falla->descripcion,
                ];
            })->toArray();

            if ($cobro->credito) {
                $detalles = $cobro->credito->detalles->where('num_orden', $numOrden);
                $detallesConIdAbono = $detalles->where('id_abono', '>=', 0);
                $detalleSinIdAbono = $detalles->where('id_abono', 0)->first();

                if ($detallesConIdAbono->count() >= 1) 
                {
                    $this->cobroFinal['montoAbonado'] = $detallesConIdAbono->sum('abono');
                    $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['montoAbonado'];
                } 
                elseif ($detalleSinIdAbono) 
                {
                    $this->cobroFinal['anticipo'] = $detalleSinIdAbono->abono;
                    $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
                }
            }  
            
            $this->dispatch('lanzaCobroModal');  //Abre la ventana modal con Javascript en el layout.main

            $this->datosCobroCargados = true;
            $this->modalCobroFinalAbierta = true;
        }
        else
        {
            session()->flash('error', 'El equipo NO SE PUEDE COBRAR porque ya ha sido cobrado anteriormente. Intenta con otro.');
        }
    }

    public function invierteCobroEquipoTaller($numOrden)
    {
        $cobro = CobroTaller::where('num_orden', $numOrden)
        ->first();

        $cobro->cancelado = !$cobro->cancelado;
        $cobro->save();
    }

    public function cobroCredito($numOrden)
    {
         if ((isset($this->cobroFinal['anticipo']) 
            || isset($this->cobroFinal['montoAbonado'])))
        {
            if ($this->cobroFinal['restante'] <= 0)
            {
                $this->dispatch('mostrarToastError', 'No es posible generar el CRÉDITO debido a que ya está cubierto el TOTAL COBRADO. Aumenta el TOTAL COBRADO si quieres generarlo.');
                return false;
            }
        }
        
        if ($this->modalCobroFinalAbierta)
        {         
            $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

            if ($equipoTaller->id_estatus < 5)  //Si todavía no ha sido cobrado lo cobra
            {
                try 
                {
                    DB::transaction(function () use ($numOrden) 
                    {
                        $cobroTaller = CobroTaller::create([
                            'num_orden' => $numOrden,
                            'fecha' => now(),
                            'cobro_estimado' => $this->cobroFinal['cobroEstimado'],
                            'cobro_realizado' => $this->cobroFinal['cobroRealizado'],
                            'id_modo_pago' => 0,
                            'id_usuario_cobro' => Auth::id()
                        ]);

                        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                        $equipoTaller->fecha_salida = now();
                        $equipoTaller->id_estatus = $this->cobroFinal['idEstatusEquipo'];
                        $equipoTaller->save();

                        $idCliente = $equipoTaller->equipo->cliente->id;

                        $conCobroEstimado = false;
                        $this->cobroACredito['conCobroEstimado'] = false;

                        if (is_null($this->cobroFinal['anticipo']))
                        {
                            if ($equipoTaller->cobroTallerCredito)
                            {
                                $cobroTallerCredito = CobroTallerCredito::where('num_orden', $numOrden)->first();
                                $cobroTallerCredito->id_estatus = 1;
                                $cobroTallerCredito->save();

                                $conCobroEstimado = true;
                                // $this->cobroACredito['conCobroEstimado'] = true;
                            }
                            else
                            {
                                $cobroTallerCredito = new CobroTallerCredito();
                                $cobroTallerCredito->num_orden = $numOrden;
                                $cobroTallerCredito->id_cliente = $idCliente;
                                $cobroTallerCredito->id_estatus = 1;
                                $cobroTallerCredito->save();

                                //Inserta un abono de $0 en el id_abono 0 para indicar que es CRÉDITO
                                $cobroTallerCreditoDetalle = new CobroTallerCreditoDetalle();
                                $cobroTallerCreditoDetalle->num_orden = $numOrden;
                                $cobroTallerCreditoDetalle->abono = 0;
                                $cobroTallerCreditoDetalle->id_usuario_cobro = Auth::id();

                                $cobroTallerCreditoDetalle->save();
                            }
                        }

                        $this->cobroACredito['numOrden'] = $numOrden;

                        $this->cobroACredito['nombreCliente'] = $equipoTaller->equipo->cliente->disponible ? $equipoTaller->equipo->cliente->nombre : $equipoTaller->equipo->cliente->nombre . "*";
                        $this->cobroACredito['tipoEquipo'] =  $equipoTaller->equipo->tipo_equipo->disponible? $equipoTaller->equipo->tipo_equipo->nombre : $equipoTaller->equipo->tipo_equipo->nombre . "*";
                        if ($equipoTaller->equipo->marca->disponible)
                        {
                            if($equipoTaller->equipo->marca->id_tipo_equipo === $equipoTaller->equipo->id_tipo)
                            {
                                $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre;
                            }
                            else
                            {
                                $this->cobroACredito['marcaEquipo'] = "*****";
                            }
                        }
                        else
                        {
                            $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre . "*";
                        }

                        if ($equipoTaller->equipo->modelo->disponible)
                        {
                            if($equipoTaller->equipo->modelo->id_marca === $equipoTaller->equipo->marca->id)
                            {
                                $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre;
                            }
                            else
                            {
                                $this->cobroACredito['modeloEquipo'] = "*****";
                            }
                        }
                        else
                        {
                            $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre . "*";
                        }

                        $this->cobroACredito['nombreCliente'] = $equipoTaller->equipo->cliente->disponible ? $equipoTaller->equipo->cliente->nombre : $equipoTaller->equipo->cliente->nombre . "*";
                        $this->cobroACredito['tipoEquipo'] =  $equipoTaller->equipo->tipo_equipo->disponible? $equipoTaller->equipo->tipo_equipo->nombre : $equipoTaller->equipo->tipo_equipo->nombre . "*";
                        if ($equipoTaller->equipo->marca->disponible)
                        {
                            if($equipoTaller->equipo->marca->id_tipo_equipo === $equipoTaller->equipo->id_tipo)
                            {
                                $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre;
                            }
                            else
                            {
                                $this->cobroACredito['marcaEquipo'] = "*****";
                            }
                        }
                        else
                        {
                            $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre . "*";
                        }

                        if ($equipoTaller->equipo->modelo->disponible)
                        {
                            if($equipoTaller->equipo->modelo->id_marca === $equipoTaller->equipo->marca->id)
                            {
                                $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre;
                            }
                            else
                            {
                                $this->cobroACredito['modeloEquipo'] = "*****";
                            }
                        }
                        else
                        {
                            $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre . "*";
                        }

                        $this->cobroACredito['idEstatus'] = 1;
                        $this->cobroACredito['estatus'] = "SIN LIQUIDAR";

                        if ($equipoTaller->cobroTallerCredito && !$conCobroEstimado)
                        {
                            $this->cobroACredito['idEstatus'] = $equipoTaller->cobroTallerCredito->estatus->id;
                            $this->cobroACredito['estatus'] = $equipoTaller->cobroTallerCredito->estatus->descripcion;
                        }

                        if ($equipoTaller->cobroTaller)
                        {
                            $this->cobroACredito['monto'] = $equipoTaller->cobroTaller->cobro_realizado;
                        }

                        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->where('abono', '>', 0)->get();

                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;
                
                        $this->modalCobroCreditoTallerAbierta = true;
                        $this->modalCobroFinalAbierta = false;
                
                        $this->muestraDivAbono = false;
                
                        $this->cobroACredito['abono'] = null;
                
                        $this->showModalErrors = true;
                        $this->showMainErrors = false;

                        $this->dispatch('cierraCobroModal');
                        $this->dispatch('abreCobroCreditoTallerModal');
                    });
                } catch (\Exception $e)
                {
                        // Manejo de errores si ocurre una excepción
                        // Puedes agregar logs o notificaciones aquí
                        dd($e);
                }
            }
            else
            {
                $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

                $idCliente = $equipoTaller->equipo->cliente->id;

                $this->cobroACredito['numOrden'] = $numOrden;

                // $this->cobroACredito['nombreCliente'] = $equipoTaller->equipo->cliente->nombre;
                // $this->cobroACredito['tipoEquipo'] = $equipoTaller->equipo->tipo_equipo->nombre;
                // $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre;
                // $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre;

                $this->cobroACredito['idEstatus'] = $equipoTaller->cobroTallerCredito->estatus->id;
                $this->cobroACredito['estatus'] = $equipoTaller->cobroTallerCredito->estatus->descripcion;
                if ($equipoTaller->cobroTaller)
                {
                    $this->cobroACredito['monto'] = $equipoTaller->cobroTaller->cobro_realizado;
                }

                $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

                $this->dispatch('abreCobroCreditoTallerModal');
            }
        }
        else
        {
            dd('OPERACIÓN INVÁLIDA');
        }
    }

    public function abreModalCorteCaja()
    {
        $this->modalCorteCajaAbierta = true;

        $this->corteCaja['idModoPago'] = 1;
    }

    public function cierraModalCorteCaja()
    {
        $this->modalCorteCajaAbierta = false;
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
                       // Busca el registro de crédito del taller
                        $cobroTallerCredito = CobroTallerCredito::where('num_orden', $numOrden)->first();

                        if ($cobroTallerCredito) 
                        {
                            // Si ya existe un registro de crédito, busca el último ID de abono
                            // En este caso, CobroTallerCD (Cobro Taller Credito Detalle) es el que contiene los abonos
                            $ultimoIdAbono = CobroTallerCreditoDetalle::where('id_cobro_taller_credito', $cobroTallerCredito->id)->max('id_abono');
                            // Si no hay abonos, el resultado de max() será null, podemos usar el operador de coalescencia
                            $ultimoIdAbono = $ultimoIdAbono ?? 0;

                        } 
                        else 
                        {
                            // No existe un registro de crédito, se crea uno nuevo
                            $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

                            // Verificamos si existe el equipo y su relación con el cliente
                            if ($equipoTaller && $equipoTaller->equipo && $equipoTaller->equipo->cliente) {
                                $idCliente = $equipoTaller->equipo->cliente->id;

                                $cobroTallerCredito = new CobroTallerCredito();
                                $cobroTallerCredito->num_orden = $numOrden;
                                $cobroTallerCredito->id_cliente = $idCliente;
                                $cobroTallerCredito->id_estatus = 1; // Asumiendo que 1 es el estatus inicial
                                $cobroTallerCredito->save();

                                $ultimoIdAbono = 0;
                            } else {
                                // Manejar el caso donde no se encuentra el equipo o el cliente
                                // Esto previene errores si los datos no están bien relacionados
                                // Puedes lanzar una excepción, registrar un error o simplemente no hacer nada
                                $ultimoIdAbono = 0;
                            }
                        }
                        
                        $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
                        $cobroTallerCreditoDetalles->num_orden = $numOrden;
                        $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        $cobroTallerCreditoDetalles->abono = $this->cobroACredito['abono'];
                        $cobroTallerCreditoDetalles->id_modo_pago = $this->cobroACredito['idModoPago'];
                        $cobroTallerCreditoDetalles->id_usuario_cobro = Auth::id();
                        $cobroTallerCreditoDetalles->save();                        

                        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

                        if ($acumulado == $this->cobroACredito['monto'])
                        {
                            CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 2]);
                            $this->cobroACredito['estatus'] = $cobroTallerCreditoDetalles->first()->cobroCredito->estatus->descripcion;
                            $this->cobroACredito['idEstatus'] = 2;
                        }

                        if ($this->cobroACredito['idModoPago'] == 1)
                        {
                            $idRef = $numOrden % 1000;
                            $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
            
                            $movimiento = new MovimientoCaja();
                            $movimiento->referencia = $this->regresaReferencia(3, $idRef);
                            $movimiento->id_tipo = 3;
                            $movimiento->monto = $this->calculaMonto(3, $this->cobroACredito['abono']);
                            $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $this->cobroACredito['abono']); // Asegura que el saldo_caja sea un número decimal
                            $movimiento->id_usuario = Auth::id();
                            $movimiento->save();
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

    public function muestraDivAgregaAbono()
    {
        $this->muestraDivAbono = true;
    }

 

    public function abreCobroCredito($numOrden, $esEstimado = false)
    {
        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

        $this->cobroACredito['nombreCliente'] = $equipoTaller->equipo->cliente->disponible ? $equipoTaller->equipo->cliente->nombre : $equipoTaller->equipo->cliente->nombre . "*";
        $this->cobroACredito['numOrden'] = $numOrden;
        
        $this->cobroACredito['tipoEquipo'] = $equipoTaller->equipo->tipo_equipo->disponible ? $equipoTaller->equipo->tipo_equipo->nombre : $equipoTaller->equipo->tipo_equipo->nombre . "*";

        if ($equipoTaller->equipo->marca->disponible)
        {
            if($equipoTaller->equipo->marca->id_tipo_equipo === $equipoTaller->equipo->id_tipo)
            {
                $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre;
            }
            else
            {
                $this->cobroACredito['marcaEquipo'] = "*****";
            }
        }
        else
        {
            $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre . "*";
        }

        if ($equipoTaller->equipo->modelo->disponible)
        {
            if($equipoTaller->equipo->modelo->id_marca === $equipoTaller->equipo->marca->id)
            {
                $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre;
            }
            else
            {
                $this->cobroACredito['modeloEquipo'] = "*****";
            }
        }
        else
        {
            $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre . "*";
        }

        $this->cobroACredito['idEstatus'] = $equipoTaller->cobroTallerCredito->estatus->id;
        $this->cobroACredito['estatus'] = $equipoTaller->cobroTallerCredito->estatus->descripcion;

        if ($esEstimado) {
            // Obtener el cobro estimado más alto
            if ($equipoTaller->cobrosEstimados) {
                $cobroEstimado = $equipoTaller->cobrosEstimados()->orderBy('id', 'desc')->first();
                if ($cobroEstimado) {
                    $this->cobroACredito['monto'] = $cobroEstimado->cobro_estimado;
                }
                $this->cobroACredito['conCobroEstimado'] = true;
            }
        } else {
            // Obtener el cobro realizado
            if ($equipoTaller->cobroTaller) {
                $this->cobroACredito['monto'] = $equipoTaller->cobroTaller->cobro_realizado;
            }
            $this->cobroACredito['conCobroEstimado'] = false;
        }

        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->where('abono', '>', 0)->get();
        $this->sumaAbonos = $this->detallesCredito->sum('abono');
        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

        $this->modalCobroCreditoTallerAbierta = true;
        $this->muestraDivAbono = false;
        $this->cobroACredito['abono'] = null;
        $this->cobroACredito['idModoPago'] = 1;
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->dispatch('abreCobroCreditoTallerModal');
    }

    public function cierraCobroCreditoTallerModal()
    {
        $this->modalCobroCreditoTallerAbierta = false;

        $this->showModalErrors = false;
        $this->showMainErrors = false;
    }

    public function cobroLiquidar($numOrden)
    {
        if ($this->modalCobroFinalAbierta && ($this->cobroFinal['anticipo'] || $this->cobroFinal['montoAbonado']))
        {
            $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

            if ($equipoTaller->id_estatus < 5)  //Si todavía no ha sido cobrado lo cobra
            {
                try 
                {
                    DB::transaction(function () use ($numOrden) 
                    {
                        $cobroTaller = CobroTaller::create([
                            'num_orden' => $numOrden,
                            'fecha' => now(),
                            'cobro_estimado' => $this->cobroFinal['cobroEstimado'],
                            'cobro_realizado' => $this->cobroFinal['cobroRealizado'],
                            'id_modo_pago' => 0,
                            'id_usuario_cobro' => Auth::id()
                        ]);

                        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                        $equipoTaller->fecha_salida = now();
                        $equipoTaller->id_estatus = $this->cobroFinal['idEstatusEquipo'];
                        $equipoTaller->save();
                            
                        $ultimoIdAbono = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->max('id_abono');

                        $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
                        $cobroTallerCreditoDetalles->num_orden = $numOrden;
                        $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        if ($this->cobroFinal['anticipo'])
                        {
                            $abono = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
                        }
                        else 
                        {
                            $abono = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['montoAbonado'];
                        }
                        $cobroTallerCreditoDetalles->abono = $abono;
                        $cobroTallerCreditoDetalles->id_modo_pago = $this->cobroACredito['idModoPago'];
                        $cobroTallerCreditoDetalles->id_usuario_cobro = Auth::id();
                        $cobroTallerCreditoDetalles->save();

                        $cobroTallerCredito = CobroTallerCredito::where('num_orden', $numOrden)->first();
                        $cobroTallerCredito->id_estatus = 2;
                        $cobroTallerCredito->save();
                  
                        if ($this->cobroFinal['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                        { 
                            $idRef = $numOrden % 1000;
                            $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

                            $movimiento = new MovimientoCaja();
                            $movimiento->referencia = $this->regresaReferencia(3, $idRef);
                            $movimiento->id_tipo = 3;
                            $movimiento->monto = $this->calculaMonto(3, $abono);
                            $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $abono); // Asegura que el saldo_caja sea un número decimal
                            $movimiento->id_usuario = Auth::id();
                            $movimiento->save();
                        }

                        $this->modalCobroFinalAbierta = false;
                        $this->dispatch('cierraCobroModal');
                        $this->dispatch('mostrarToast', 'Cobro liquidado con éxito!!!');

                        // return redirect()->route('taller.print-final', $numOrden);
                    });
                } catch (\Exception $e)
                {
                        // Manejo de errores si ocurre una excepción
                        // Puedes agregar logs o notificaciones aquí
                        dd($e);
                }
            }
            else
            {
                $this->dispatch('cierraCobroModal');
            }
        }
    }

    public function liquidaCredito()
    {
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas liquidar el crédito?', 'lisLiquidarCobroCredito');
    }

    public function liquidarCobroCredito()
    {
       $numOrden = $this->cobroACredito['numOrden'];

       try 
       {
           DB::transaction(function () use ($numOrden) 
           {
               $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();
               $ultimoIdAbono = $this->detallesCredito->max('id_abono');
                if ($this->cobroACredito['conCobroEstimado'])
                { 
                    if ($this->detallesCredito->isEmpty())
                    {
                            $this->cobroACredito['monto'] = CobroEstimadoTaller::where('num_orden', $numOrden)->first()->cobro_estimado;
                    }
                    else
                    {
                            $this->cobroACredito['monto'] = $this->detallesCredito->first()->cobroCredito->cobroEstimado->cobro_estimado;
                    }
                }
                else
                {
                    if ($this->detallesCredito->isEmpty())
                    {
                            $this->cobroACredito['monto'] = CobroTaller::where('num_orden', $numOrden)->first()->cobro_realizado;
                    }
                    else
                    {
                            $this->cobroACredito['monto'] = $this->detallesCredito->first()->cobroCredito->cobroTaller->cobro_realizado;
                    }
                }

               $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
               $cobroTallerCreditoDetalles->num_orden = $numOrden;
               $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
               $cobroTallerCreditoDetalles->abono = $this->montoLiquidar;
               $cobroTallerCreditoDetalles->id_modo_pago = $this->cobroACredito['idModoPago'];
               $cobroTallerCreditoDetalles->id_usuario_cobro = Auth::id();
               $cobroTallerCreditoDetalles->save();

                CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 2]);
                $this->cobroACredito['estatus'] = $cobroTallerCreditoDetalles->first()->cobroCredito->estatus->descripcion;
                $this->cobroACredito['idEstatus'] = 2;

                $this->muestraDivAbono = false;
                $this->cobroACredito['abono'] = null;

                if ($this->cobroACredito['idModoPago'] == 1)
                {
                    $idRef = $numOrden % 1000;
                    $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
    
                    $movimiento = new MovimientoCaja();
                    $movimiento->referencia = $this->regresaReferencia(3, $idRef);
                    $movimiento->id_tipo = 3;
                    $movimiento->monto = $this->calculaMonto(3, $this->montoLiquidar);
                    $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $this->montoLiquidar); // Asegura que el saldo_caja sea un número decimal
                    $movimiento->id_usuario = Auth::id();
                    $movimiento->save();
                }

                $this->sumaAbonos = $this->detallesCredito->sum('abono');
                $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

                $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

                $this->sumaAbonos = $this->detallesCredito->sum('abono');
                $this->montoLiquidar = 0;

                session()->flash('success', 'El crédito ha sido LIQUIDADO exitosamente.');
           });
       } catch (\Exception $e)
       {
               // Manejo de errores si ocurre una excepción
               dd($e);
       }
    }

    public function abrirCaja()
    {
        // $printer_name = "Ticket";
        // $connector = new WindowsPrintConnector($printer_name);
        // $printer = new Printer($connector);

        // $printer->pulse();
        // $printer->close();

        return redirect()->route('caja.movimientos');
    }

    public function cobrarSinTicket($numOrden)
    {
        $this->cobrar($numOrden, false);
    }

    public function cobrarConTicket($numOrden)
    {
        $this->cobrar($numOrden, true);
    }


    public function cobrar($numOrden, $conTicket)
    {
        if ($this->modalCobroFinalAbierta && !$this->cobroFinal['anticipo'])
        {
            $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

            if ($equipoTaller->id_estatus < 5)  //Si todavía no ha sido cobrado lo cobra
            {
                try 
                {
                    DB::transaction(function () use ($numOrden, $conTicket) 
                    {
                        $cobroTaller = CobroTaller::create([
                            'num_orden' => $numOrden,
                            'fecha' => now(),
                            'cobro_estimado' => $this->cobroFinal['cobroEstimado'],
                            'cobro_realizado' => $this->cobroFinal['cobroRealizado'],
                            'id_modo_pago' => $this->cobroFinal['idModoPago'],
                            'id_usuario_cobro' => Auth::id()
                        ]);

                        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                        $equipoTaller->fecha_salida = now();
                        $equipoTaller->id_estatus = $this->cobroFinal['idEstatusEquipo'];
                        $equipoTaller->save();

                        $monto = 0;   //MONTO para el MOVIMIENTO DE CAJA
                        if ($this->cobroFinal['anticipo'])  //Si hay anticipo agrega el abono
                        {
                            $ultimoIdAbono = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->max('id_abono');

                            $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
                            $cobroTallerCreditoDetalles->num_orden = $numOrden;
                            $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                            $abono = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
                            $monto = $abono;
                            $cobroTallerCreditoDetalles->abono = $abono;
                            $cobroTallerCreditoDetalles->id_usuario_cobro = Auth::id();
                            $cobroTallerCreditoDetalles->save();

                            if ($abono < 0)  //Está liquidado el cobro
                            {
                                $cobroTallerCredito = CobroTallerCredito::where('num_orden', $numOrden)->first();
                                $cobroTallerCredito->id_estatus = 2;
                                $cobroTallerCredito->save();
                            }
                        }
                        else   //SI NO hay ANTICIPO
                        {
                            $monto = $this->cobroFinal['cobroRealizado'];
                        }

                        if ($this->cobroFinal['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                        { 
                            $idRef = $numOrden % 10000;
                            $idRef = str_pad($idRef, 4, '0', STR_PAD_LEFT);

                            $movimiento = new MovimientoCaja();
                            $movimiento->referencia = $this->regresaReferencia(2, $idRef);
                            $movimiento->id_tipo = 2;
                            $movimiento->monto = $this->calculaMonto(2, $monto);
                            $movimiento->saldo_caja = $this->calculaSaldoCaja(2, $monto); // Asegura que el saldo_caja sea un número decimal
                            $movimiento->id_usuario = Auth::id();
                            $movimiento->save();
                        }

                        $this->modalCobroFinalAbierta = false;

                        if ($conTicket && $this->cobroFinal['idModoPago'] == 1)  //Solo si es EFECTIVO se imprime ticket
                        {
                            $this->showMainErrors = true;

                            // $printer_name = "Ticket";
                            // $connector = new WindowsPrintConnector($printer_name);
                            // $printer = new Printer($connector);

                            // $printer->pulse();
                            // $printer->close();

                            return redirect()->route('taller.print-final', $numOrden, true); 
                        }

                        $this->dispatch('cierraCobroModal');
                        $this->dispatch('mostrarToast', 'Cobro realizado con éxito!!!');

                        // return redirect()->route('taller.print-final', $numOrden);
                    });
                } catch (\Exception $e)
                {
                        // Manejo de errores si ocurre una excepción
                        // Puedes agregar logs o notificaciones aquí
                        dd($e);
                }
            }
            else  //Si no ha sido cobrado
            {
                $this->dispatch('cierraCobroModal');
            }
        }
    }

    public function render()
    {
        $equipos_taller = EquipoTaller::query();

        if (isset($this->busquedaEquipos['fechaEntradaInicio']) && isset($this->busquedaEquipos['fechaEntradaFin']))
        {
            $fechaInicio = date('Y-m-d', strtotime($this->busquedaEquipos['fechaEntradaInicio']));
            $fechaFin = date('Y-m-d', strtotime($this->busquedaEquipos['fechaEntradaFin']));

            if ($fechaInicio == $fechaFin)
            {
                $equipos_taller->whereDate('fecha_entrada', '=', $fechaInicio);
            }
            else
            {
                $equipos_taller->whereDate('fecha_entrada', '>=', $fechaInicio)
                            ->whereDate('fecha_entrada', '<=', $fechaFin);
            }
        }

        if (isset($this->busquedaEquipos['idEstatus']) && $this->busquedaEquipos['idEstatus'] != [])
        {
            $equipos_taller->whereIn('id_estatus', $this->busquedaEquipos['idEstatus']);

            // dd($this->busquedaEquipos['idEstatus']);
         }

        if (isset($this->busquedaEquipos['idTipo']) && $this->busquedaEquipos['idTipo'] != [])
        {
            $equipos_taller->whereHas('equipo', function ($query) {
                $query->whereIn('id_tipo', $this->busquedaEquipos['idTipo']);
            });
        }

        if (isset($this->busquedaEquipos['nombreCliente']))
        {
            $nombreCliente = $this->busquedaEquipos['nombreCliente'];
            $equipos_taller->whereHas('equipo.cliente', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'like', "%$nombreCliente%");
            })
            ->get();
        
        }

        $equipos_taller = $equipos_taller->orderBy('fecha_entrada', 'asc')->paginate(10);

        $estatus_equipos = EstatusEquipo::all();
        $tipos_equipos = TipoEquipo::where('disponible', 1)->get();

        return view('livewire.taller', compact('equipos_taller', 'estatus_equipos', 'tipos_equipos'));
    }

    public function preguntaBorraAbono($numOrden, $idAbono)
    {
        $this->cobroACredito['idAbonoSeleccionado'] = $idAbono;
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas eliminar el abono seleccionado?', 'lisBorraAbono');
    }

    public function cierraModalActualizarModoPago()
    {

    }

    public function borraAbono()
    {
        $numOrden = $this->cobroACredito['numOrden'];
        $idAbono = $this->cobroACredito['idAbonoSeleccionado'];

        // Obtener el registro
        $detalleCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)
        ->where('id_abono', $idAbono)
        ->first();

        if ($detalleCredito)
        {
            DB::transaction(function () use ($numOrden, $idAbono, $detalleCredito) {
                if ($detalleCredito) {
                    $idModoPago = $detalleCredito->id_modo_pago;
                    $abono = $detalleCredito->abono;

                    // Borrar el registro
                    CobroTallerCreditoDetalle::where('num_orden', $numOrden)
                    ->where('id_abono', $idAbono)
                    ->delete();

                    // Actualizar detalles de crédito
                    $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

                    // Verifica si no hay registros en $detallesCredito
                    if ($this->detallesCredito->isEmpty()) 
                    {
                        // Elimina el CobroTallerCredito relacionado
                        CobroTallerCredito::where('num_orden', $numOrden)->delete();
                    }
                    else 
                    {
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;
    
                        // Actualizar el estatus del crédito
                        CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 1]);
    
                        $this->cobroACredito['idEstatus'] = 1;
                        $this->cobroACredito['estatus'] = "SIN LIQUIDAR";
                    }

                    // Registrar movimiento en caja si el modo de pago es 1
                    if ($idModoPago == 1) {
                        $idRef = $numOrden % 1000;
                        $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

                        $movimiento = new MovimientoCaja();
                        $movimiento->referencia = $this->regresaReferencia(7, $idRef);
                        $movimiento->id_tipo = 7;
                        $movimiento->monto = $this->calculaMonto(7, $abono);
                        $movimiento->saldo_caja = $this->calculaSaldoCaja(7, $abono); // Asegura que el saldo_caja sea un número decimal
                        $movimiento->id_usuario = Auth::id();
                        $movimiento->save();
                    }
                } 
            });
            session()->flash('success', 'El ABONO se ha ELIMINADO con éxito.');
        }
        else
        {
            $this->addError('abono', 'El abono seleccionado no existe o hubo problemas con la base de datos.');
        }
    }

    public function abrirEditarModoPagoModal($numOrden, $idAbono)
    {
        $this->numOrdenModal = $numOrden;
        $this->idAbonoModal = $idAbono;

        $this->cobroModal = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->where('id_abono', $idAbono)->firstOrFail();

        $this->idModoPago =  $this->cobroModal->id_modo_pago;

        $this->dispatch('abreModalEditaModoPagoTallerCredito');
    }

    public function actualizarModoPago()
    {
        $cobroTallerCreditoDetalle = CobroTallerCreditoDetalle::where('num_orden', $this->numOrdenModal)
        ->where('id_abono', $this->idAbonoModal)
        ->first();

        $idModoPago = $cobroTallerCreditoDetalle->id_modo_pago;
        $abono = $cobroTallerCreditoDetalle->abono;

        if ($cobroTallerCreditoDetalle) {
            CobroTallerCreditoDetalle::where('num_orden', $this->numOrdenModal)
            ->where('id_abono', $this->idAbonoModal)->update(['id_modo_pago' => $this->idModoPago]);
        } 

        if ($idModoPago == 1 && $this->idModoPago == 2)  //Si el MODO DE PAGO era EFECTIVO y se cambia a TRANSF.
        {
            $idRef = $this->numOrdenModal % 1000;
            $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

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
            $idRef = $this->numOrdenModal % 1000;
            $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

            $movimiento = new MovimientoCaja();
            $movimiento->referencia = $this->regresaReferencia(3, $idRef);
            $movimiento->id_tipo = 3;
            $movimiento->monto = $this->calculaMonto(3, $abono);
            $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $abono); // Asegura que el saldo_caja sea un número decimal
            $movimiento->id_usuario = Auth::id();
            $movimiento->save();
        }

        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $this->numOrdenModal)
        ->where('abono', '>', 0)->get();

        $this->dispatch('cierraModalEditaModoPagoTallerCredito');
        $this->dispatch('mostrarToast', 'Modo de pago actualizado con éxito!!!');
    }

    public function generaCorteCajaPDF()
    {
        $this->corteCaja = Session::get('corteCaja');
        $cajeroSeleccionado = false;

        $fechaInicial = Carbon::parse($this->corteCaja['fechaInicial'])->startOfDay();
        $fechaFinal = Carbon::parse($this->corteCaja['fechaFinal'])->endOfDay();
        $idModoPago = $this->corteCaja['idModoPago'];

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL DÍA :: ' .  $this->formatearFecha($this->corteCaja['fechaInicial']);
        }
        else
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL ' .  $this->formatearFecha($this->corteCaja['fechaInicial']) . ' AL ' . $this->formatearFecha($this->corteCaja['fechaFinal']);
        }

        $cajeroSeleccionado = $this->corteCaja['idUsuario'] != 0 ? true : false;

        $movimientoCaja = MovimientoCaja::
        whereBetween('fecha', [$fechaInicial, $fechaFinal])
        ->where('id_tipo', 4)
        ->orderByDesc('fecha')
        ->first();

        // $esMismaFecha = Carbon::parse($fechaInicial)->toDateString() === Carbon::parse($fechaFinal)->toDateString();

        // $consulta = MovimientoCaja::whereBetween('fecha', [$fechaInicial, $fechaFinal])
        //     ->where('id_tipo', 4);

        // $movimientoCaja = $esMismaFecha
        //     ? $consulta->orderByDesc('fecha')->first() // más reciente
        //     : $consulta->orderBy('fecha')->first();    // más antiguo

        $entradasManuales = MovimientoCaja::
        whereBetween('fecha', [$fechaInicial, $fechaFinal])
        ->where('id_tipo', 5)
        ->get();

        $salidasManuales = MovimientoCaja::
        whereBetween('fecha', [$fechaInicial, $fechaFinal])
        ->where('id_tipo', 6)
        ->get();

        $ventas = collect();
        if ($this->corteCaja['incluyeVentas'])
        {              
            if ($this->corteCaja['incluyeCredito'])
            {
                $ventas = Venta::with([
                    'cliente',
                    'usuario',
                    'ventaCredito.ventaCreditoDetalles' => function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                        $query->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                              ->where('abono', '>', 0)
                              ->where('id_modo_pago', $idModoPago);
                    },
                ])
                ->when($cajeroSeleccionado, function ($query) {
                    return $query->where('id_usuario', $this->corteCaja['idUsuario']);
                })
                ->where('cancelada', 0)
                ->where(function($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                    // Condición para ventas sin VentaCredito
                    $query->where(function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                        $query->whereDoesntHave('ventaCredito')
                              ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                              ->where('id_modo_pago', $idModoPago);            
                    })
                    // Condición para ventas con VentaCredito que tienen detalles válidos
                    ->orWhereHas('ventaCredito.ventaCreditoDetalles', function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                        $query->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                              ->where('abono', '>', 0)
                              ->where('id_modo_pago', $idModoPago);
                    });
                })
                ->orderBy('created_at')
                ->get()
                ->flatMap(function ($venta) use($movimientoCaja) {
                    $resultado = collect();
                
                    // Si no hay VentaCredito, incluir la venta
                    if (!$venta->ventaCredito) {
                        $resultado->push([
                            'id' => $venta->id,
                            'created_at' => $venta->created_at,
                            'nombre_cliente' => $venta->cliente->nombre,
                            'monto' => $venta->total,
                            'cajero' => $venta->usuario->name,
                            'tipo' => 'VENTA',
                            'id_modo_pago' => $venta->id_modo_pago,
                            'detalles' => $venta->detalles
                        ]);
                    }
                
                    // Si hay VentaCredito, incluir únicamente los detalles válidos
                    if ($venta->ventaCredito) {
                        $venta->ventaCredito->ventaCreditoDetalles
                            ->each(function ($detalle) use ($resultado, $venta, $movimientoCaja) {
                                $resultado->push([
                                    'id' => $detalle->id,
                                    'created_at' => $detalle->created_at,
                                    'nombre_cliente' => $venta->cliente->nombre,
                                    'monto' => $detalle->abono,
                                    'cajero' => $detalle->usuario->name ?? 'N/A',
                                    'tipo' => 'ABONO_VENTA',
                                    'id_modo_pago' => $detalle->id_modo_pago,
                                    'detalles' => $venta->ventaCredito->ventaCreditoDetalles
                                ]);
                            });
                    }
                    return $resultado;
                });
            }
            else
            {
                $ventas = Venta::with('cliente')
                    ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->when($cajeroSeleccionado, function ($query) {
                        return $query->where('id_usuario', $this->corteCaja['idUsuario']);
                    })
                    ->where('cancelada', 0)
                    ->where(function($query) {
                        $query->whereDoesntHave('ventaCredito')
                            ->orWhereHas('ventaCredito', function ($query) {
                                $query->where('id_estatus', 2);
                            });
                    })
                    ->get()
                    ->map(function($venta) use($movimientoCaja) {
                        return [
                            'id' => $venta->id,
                            'created_at' => $venta->created_at,
                            'nombre_cliente' => $venta->cliente->nombre,
                            'monto' => $venta->total,
                            'cajero' => $venta->usuario->name,
                            'tipo' => 'VENTA',
                            'id_modo_pago' => $venta->id_modo_pago,
                            'detalles' => $venta->detalles
                        ];
                    });
            } 
            
            if ($this->corteCaja['chkAgrupar'])
            {
                $idsVentas = collect($ventas)->pluck('id');
                $ventasCredito = Venta::with('ventaCredito') 
                    ->whereIn('id', $idsVentas)
                    ->get();

                list($ventasConCredito, $ventasSinCredito) = collect($ventasCredito)->partition(function ($venta) {
                    return $venta->ventaCredito !== null;
                });

                $idsVentasSinCredito = collect($ventasSinCredito)->pluck('id');

                // Obtén los detalles desde la base de datos
                $detalles = VentaDetalle::with('producto')
                    ->whereIn('id_venta', $idsVentasSinCredito)
                    ->get();            

                $productosAgrupados = $detalles
                    ->groupBy(function ($item) {
                        // Agrupa por descripción del productoComun si existe, si no por la del producto normal
                        if ($item->productoComun && $item->productoComun->descripcion_producto) {
                            return $item->productoComun->descripcion_producto;
                        }
                        return $item->codigo_producto;
                    })
                    ->map(function ($items) use ($movimientoCaja) {
                        $primerItem = $items->first();

                        // Verifica si tiene relación con productoComun y si tiene descripción
                        $descripcion = $primerItem->productoComun && $primerItem->productoComun->descripcion_producto
                            ? $primerItem->productoComun->descripcion_producto
                            : ($primerItem->producto->descripcion ?? '—');

                        return [
                            'cantidad' => $items->sum('cantidad'),
                            'prod_serv' => $descripcion,
                            'subtotal' => $items->sum('importe'),
                            'tipo' => 'VENTA_AGRUPADA',
                        ];
                    })
                    ->values();

                if ($this->corteCaja['incluyeCredito'])
                {
                    $idsVentasConCredito = collect($ventasConCredito)->pluck('id');

                    // Obtén los detalles desde la base de datos
                    $detallesCredito = VentaCreditoDetalle::
                    whereIn('id', $idsVentasConCredito)->where('abono', '>', 0)
                    ->get();
                        
                    if ($detallesCredito->count() > 0) {
                            $productosAgrupados->push([
                                'cantidad' => $detallesCredito->count(),
                                'prod_serv' => 'ABONO A VENTA',
                                'subtotal' => $detallesCredito->sum('abono'),
                                'tipo' => 'ABONOS_AGRUPADOS',
                            ]);
                        }
                }
                $ventas = $productosAgrupados;
            }
        }
        // Inicializar $cobrosTaller como una colección vacía 
        $cobrosTaller = collect();

        if ($this->corteCaja['incluyeCredito'])
        {   
            // 1. Obtener los detalles de CobroTallerCredito
            $cobrosTallerCredito = CobroTallerCredito::with([
                'detalles' => function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                    $query->where('abono', '>', 0)
                    ->where('id_modo_pago', $idModoPago)
                    ->whereBetween('created_at', [$fechaInicial, $fechaFinal]);
                }
            ])
            ->whereHas('detalles', function ($query) use ($fechaInicial, $fechaFinal, $cajeroSeleccionado, $idModoPago) {
                $query->where('abono', '>', 0)
                ->where('id_modo_pago', $idModoPago)
                ->whereBetween('created_at', [$fechaInicial, $fechaFinal]);
                if ($cajeroSeleccionado) 
                { 
                    $query->where('id_usuario_cobro', $this->corteCaja['idUsuario']); 
                }
            })
            ->get()
            ->flatMap(function ($credito) use ($movimientoCaja) {
                // Transformar los detalles válidos
                return $credito->detalles->map(function ($detalle) use ($credito, $movimientoCaja) {
                    return [
                        'id' => $detalle->num_orden,
                        'created_at' => $detalle->created_at,
                        'monto' => $detalle->abono,
                        'nombre_cliente' => $detalle->cobroCredito->cliente->nombre ?? "N/A",
                        'cajero' => $detalle->usuario->name ?? "N/A",
                        'credito_id' => $credito->num_orden,
                        'tipo' => 'ABONO_TALLER',
                        'id_modo_pago' => $detalle->id_modo_pago,
                    ];
                });
            });

            // 2. Obtener los registros de CobroTaller que no tienen CobroTallerCredito
            $cobrosTallerAux = CobroTaller::with([
                'equipoTaller.equipo.cliente',
                'equipoTaller.usuario',
            ])
            ->whereDoesntHave('credito') // Filtra los que no tienen CobroTallerCredito
            ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
            ->where('id_modo_pago', $idModoPago)
            ->where('cobro_realizado', '>', 0)
            ->when($cajeroSeleccionado, function ($query) {
                $query->where('id_usuario_cobro', $this->corteCaja['idUsuario']);
            })
            ->orderBy('created_at')
            ->get()
            ->map(function ($cobro) use($movimientoCaja) {
                // Transformar los registros de CobroTaller
                return [
                    'id' => $cobro->num_orden,
                    'created_at' => $cobro->created_at,
                    'monto' => $cobro->cobro_realizado,
                    'nombre_cliente' => $cobro->equipoTaller->equipo->cliente->nombre ?? "N/A",
                    'cajero' => $cobro->usuario->name ?? "N/A",
                    'credito_id' => $cobro->num_orden,
                    'tipo' => 'TALLER',
                    'id_modo_pago' => $cobro->id_modo_pago,
                ];
            });

            // 3. Combinar los resultados
            $cobrosTaller = collect($cobrosTallerAux)->merge($cobrosTallerCredito);

             if ($this->corteCaja['chkAgrupar'])
                {
                    $numerosOrden = collect($cobrosTaller)->pluck('id');

                    $cobrosCredito = CobroTaller::with('credito') 
                    ->whereIn('num_orden', $numerosOrden)
                    ->get();

                    list($cobrosConCredito, $cobrosSinCredito) = collect($cobrosCredito)->partition(function ($cobro) {
                    return $cobro->credito !== null;
                    });

                    $numerosOrdenSinCredito = collect($cobrosSinCredito)->pluck('num_orden');

                    $detalles = CobroTaller::
                        whereIn('num_orden', $numerosOrdenSinCredito)
                        ->get();

                    
                    $cobrosAgrupados = collect();
                    if ($detalles->count() > 0)
                    {
                        $cobrosAgrupados = collect([[
                            'cantidad' => $detalles->count(),
                            'prod_serv' => 'REPARACIÓN EN TALLER',
                            'subtotal' => $detalles->sum('cobro_realizado'),
                            'tipo' => 'TALLER_AGRUPADO',
                        ]]);
                    }

                    if ($this->corteCaja['incluyeCredito'])
                    {
                        $numerosOrdenConCredito = collect($cobrosConCredito)->pluck('num_orden');

                        $detalles = CobroTallerCreditoDetalle::
                            whereIn('num_orden', $numerosOrdenConCredito)
                             ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                            ->where('abono', '>', 0)
                            ->get();

                        if ($detalles->count() > 0) 
                        {
                            $cobrosAgrupados->push([
                                'cantidad' => $detalles->count(),
                                'prod_serv' => 'ABONO TALLER',
                                'subtotal' => $detalles->sum('abono'),
                                'tipo' => 'ABONO_TALLER_AGRUPADO',
                            ]);
                        }
                    }

                    $cobrosTaller = $cobrosAgrupados;
                }
        }
        else
        {
            $cobrosTaller = CobroTaller::with(['equipoTaller.equipo.cliente', 'equipoTaller.usuario'])
            ->whereDoesntHave('credito')
            ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
            ->where('cobro_realizado', '>', 0)
            ->where('id_modo_pago', $idModoPago)
            ->when($cajeroSeleccionado, function ($query) {
                return $query->whereHas('equipoTaller', function ($query) {
                    $query->where('id_usuario_recibio', $this->corteCaja['idUsuario']);
                });
            })
            ->orderBy('created_at')
            ->get()
            ->map(function($cobro) use($movimientoCaja) {
                return [
                    'id' => $cobro->num_orden,
                    'created_at' => $cobro->created_at,
                    'nombre_cliente' => $cobro->equipoTaller->equipo->cliente->nombre,
                    'monto' => $cobro->cobro_realizado,
                    'cajero' => $cobro->equipoTaller->usuario->name,
                    'tipo' => 'TALLER',
                    'id_modo_pago' => $cobro->id_modo_pago,
                ];
            });

             if ($this->corteCaja['chkAgrupar'])
                {
                    $numerosOrden = collect($cobrosTaller)->pluck('id');

                    $detalles = CobroTaller::
                        whereIn('num_orden', $numerosOrden)
                        ->get();

                    $cobrosAgrupados = collect([[
                        'cantidad' => $detalles->count(),
                        'prod_serv' => 'REPARACIÓN EN TALLER',
                        'subtotal' => $detalles->sum('cobro_realizado'),
                        'tipo' => 'TALLER_AGRUPADO',
                    ]]);

                    $cobrosTaller = $cobrosAgrupados;
                }
        }

        $ventas = collect($ventas); 
        $cobrosTaller = collect($cobrosTaller); 

        // Unión de ambas colecciones
        $registros = $cobrosTaller->merge($ventas);

        if ($this->corteCaja['idModoPago'] == 1)
        {
            if ($this->corteCaja['chkAgrupar'])
            {
                $registros->push([
                    'cantidad' => 1,
                    'prod_serv' => 'INICIALIZACION',
                    'subtotal' => $movimientoCaja->saldo_caja,
                    'tipo' => 'INICIALIZACION',
                ]);
                $registros->push([
                    'cantidad' => $entradasManuales->count(),
                    'prod_serv' => 'ENTRADA MANUAL',
                    'subtotal' => $entradasManuales->sum('monto'),
                    'tipo' => 'ENTRADA_MANUAL_AGRUPADO',
                ]);
                $registros->push([
                    'cantidad' => $salidasManuales->count(),
                    'prod_serv' => 'SALIDA MANUAL',
                    'subtotal' => $salidasManuales->sum('monto'),
                    'tipo' => 'SALIDA_MANUAL_AGRUPADO',
                ]);
            }
            else
            {
                $registros->push([
                    'id' => $movimientoCaja->referencia,
                    'created_at' => $movimientoCaja->fecha,
                    'nombre_cliente' => '-',
                    'monto' => $movimientoCaja->saldo_caja,
                    'cajero' => $movimientoCaja->usuario->name,
                    'tipo' => 'INICIALIZACION',
                    'id_modo_pago' => 1,
                    'detalles' => null
                ]);
                if ($entradasManuales)
                { 
                    foreach($entradasManuales as $entrada)
                    { 
                        $registros->push([
                            'id' => $entrada->referencia,
                            'created_at' => $entrada->fecha,
                            'nombre_cliente' => '-',
                            'monto' => $entrada->monto,
                            'cajero' => $entrada->usuario->name,
                            'tipo' => 'ENTRADA_MANUAL',
                            'id_modo_pago' => 1,
                            'detalles' => null
                        ]);
                    }
                }
                if ($salidasManuales)
                { 
                    foreach($salidasManuales as $salida)
                    { 
                        $registros->push([
                            'id' => $salida->referencia,
                            'created_at' => $salida->fecha,
                            'nombre_cliente' => '-',
                            'monto' => $salida->monto,
                            'cajero' => $salida->usuario->name,
                            'tipo' => 'SALIDA_MANUAL',
                            'id_modo_pago' => 1,
                            'detalles' => null
                        ]);
                    }
                }
            }
        }

        // Conversión de resultado a colección de objetos
        $registros = $registros->map(function($item) { return (object) $item; });

        $pdf = SnappyPdf::loadView('taller.corte-caja', ['corteCaja' => $this->corteCaja, 'registros' => $registros])
        ->setOption('page-size', 'Letter')
        ->setOption('margin-top', 30)
        ->setOption('header-html', view('livewire.pdf.encabezado', compact('tituloCorteCaja'))->render())
        ->setOption('header-spacing', 5)
        ->setOption('footer-center', 'Página [page] de [topage]')
        // ->setOption('footer-right', $this->corteCaja['cajero'])
        ->setOption('footer-font-size', '8')
        ->setOption('footer-font-name', 'Montserrat');


        return $pdf->stream('corteCaja.pdf');
    }

    function formatearFecha($fecha) {
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO',
            4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO',
            7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
    
        $fechaFormateada = \DateTime::createFromFormat('Y-m-d', $fecha)->format('d - ');
    
        $numeroMes = \DateTime::createFromFormat('Y-m-d', $fecha)->format('n');
        $nombreMes = $meses[$numeroMes];
    
        $fechaFormateada .= strtoupper($nombreMes) . ' - ' . \DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
    
        return $fechaFormateada;
    }

    public function irCorteCaja()
    {
        Session::put('corteCaja', $this->corteCaja);

        $this->dispatch('abrirPestanaCorteCajaTaller');
    }

    public function mount()
    {
        $this->muestraDivAgregaEquipo = false;
        $this->abreModalAnotaciones = false;
        $this->numberOfPaginatorsRendered = [];
        $this->datosCobroCargados = false;
        $this->muestraDivAbono = false;

        $this->usuariosModal = User::where('disponible', 1)->get();

        $this->corteCaja = [
            'fechaInicial' => now()->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'cajero' => Auth::user()->name,
            'idUsuario' => 0,
            'incluyeCredito' => true,
            'incluyeVentas' => true,
            'idModoPago' => 1,
            'chkAgrupar' => false
        ];

        $this->busquedaEquipos = [
            'fechaEntradaInicio' => now()->subDays(30)->toDateString(),
            'fechaEntradaFin' => now()->toDateString(),
            'idEstatus' => [1,2,3,4],
            'idTipo' => null,
            'entregados' => 'no_entregados',
            'nombreCliente' => null
        ];

        $this->cobro = [
            'fechaEntrada' => null,
            'tipoEquipo' => null,
            'marcaEquipo' => null,
            'modeloEquipo' => null,
            'totalEstimado' => null
        ];

        $this->cobro2 = [
            'fallasEquipo' => null
        ];

        $this->cobroFinal = 
        [
            'numOrden' => null,
            'cliente' => null,
            'fecha' => null,
            'tipoEquipo' => null,
            'marcaEquipo' => null,
            'modeloEquipo' => null,
            'cobroEstimado' => null,
            'cobroRealizado' => null,
            'fallasEquipo' => [],
            'idEstatusEquipo' => null,
            'anticipo' => null,
            'montoAbonado' => null,
            'restante' => null,
            'publicoGeneral' => null,
            'idModoPago' => 1
        ];

        $this->cobroACredito = 
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
            'idAbonoSeleccionado' => null,
            'conCobroEstimado' => false,
            'idModoPago' => 1
        ];
    
        $this->anotacionesMod = [
            'numOrden' => null,
            'marcaEquipo' => null,
            'modeloEquipo' => null,
            'clienteEquipo' => null,
            'estatusEquipo' => null
        ];

        $this->modalCobroCreditoTallerAbierta = false;
        $this->showMainErrors = true;
        $this->showModalErrors = false;

        $this->equipoTallerModal = null;
        $this->modalCambiarEstatusEquipoAbierta = false;
        $this->modalCorteCajaAbierta = false;

        $this->datosCargados = true;

        $this->modosPagoModal = ModoPago::where('id', '>', 0)->get();
    }

    #[On('agregaEquipoAlTaller')] 
    public function refrescaTabla()
    {
    }

public function cambiarEstatusEquipo()
{
    if ($this->estatusModalCambiaEstatus == 0) {
        $this->addError('estatusModalCambiaEstatus', 'Debes seleccionar un estatus.');
        return;
    }
    else
    {
        if ($this->equipoTallerModal->cobroTaller)
        {
            if ($this->equipoTallerModal->cobroTaller->credito)
            {            
                $this->dispatch('mostrarToastSiNo', 
                'El equipo seleccionado ya ha sido COBRADO previamente con CRÉDITO. Si cambias el estatus se BORRARÁ este cobro y el crédito. ¿Deseas continuar de todas formas?',
                'warning'
                );
            }
            else
            {
                $this->dispatch('mostrarToastSiNo', 
                'El equipo seleccionado ya ha sido COBRADO previamente. Si cambias el estatus se BORRARÁ este cobro. ¿Deseas continuar de todas formas?',
                'warning'
                );
            }          
        }
    }
}

public function guardaCambioEstatusEquipo()
{
    DB::beginTransaction();

    try {
        $this->equipoTallerModal->id_estatus = $this->estatusModalCambiaEstatus;
        $this->equipoTallerModal->update();

        // Verificar y eliminar los detalles del crédito si existen
        if ($this->equipoTallerModal->cobroTaller && $this->equipoTallerModal->cobroTaller->credito) {
            $this->equipoTallerModal->cobroTaller->credito->detalles()->delete();

            // Eliminar el crédito
            $this->equipoTallerModal->cobroTaller->credito()->delete();
        }

        // Eliminar el cobroTaller si existe
        if ($this->equipoTallerModal->cobroTaller) {
            $this->equipoTallerModal->cobroTaller()->delete();
        }

        DB::commit();

        $this->dispatch('cierraModalCambiaEstatusEquipoTaller');
        $this->modalCambiarEstatusEquipoAbierta = false;
        $this->dispatch('mostrarToast', 'Equipo actualizado con éxito!!!');
    } catch (\Exception $e) {
        DB::rollBack();  
        
        dd($e);
    }
}



public function cierraCambiaEstatusEquipoModal()
{
    $this->modalCambiarEstatusEquipoAbierta = false;
}

public function abreModalCambiaEstatusEquipo($numOrden)
{
    $this->dispatch('abreModalCambiaEstatusEquipoTaller');

    $this->modalCambiarEstatusEquipoAbierta = true;
    $this->estatusModalCambiaEstatus = 0;

    $this->resetErrorBag('estatusModalCambiaEstatus');

    $this->equipoTallerModal = EquipoTaller::find($numOrden);

}


public function obtenerIconoSegunEstatus($id_estatus)
{
    $iconos = [
        1 => '<i class="fa-solid fa-handshake-simple custom-status-icon-color-1"></i>',
        2 => '<i class="fa-solid fa-screwdriver-wrench custom-status-icon-color-2"></i>',
        3 => '<i class="fa-solid fa-clipboard-check custom-status-icon-color-3"></i>',
        4 => '<i class="fa-solid fa-rectangle-xmark custom-status-icon-color-4"></i>',
        5 => '<i class="fa-solid fa-thumbs-up custom-status-icon-color-5"></i>',
        6 => '<i class="fa-solid fa-thumbs-down custom-status-icon-color-6"></i>',
    ];

    return isset($iconos[$id_estatus]) ? $iconos[$id_estatus] : '';
}

    public function obtenerIconoEstatus($id_estatus)
    {
        $iconos = [
            1 => '<i class="fa-solid fa-handshake-simple"></i>',
            2 => '<i class="fa-solid fa-screwdriver-wrench"></i>',
            3 => '<i class="fa-solid fa-clipboard-check"></i>',
            4 => '<i class="fa-solid fa-rectangle-xmark"></i>',
            5 => '<i class="fa-solid fa-thumbs-up"></i>',
            6 => '<i class="fa-solid fa-thumbs-down"></i>',
        ];

        return isset($iconos[$id_estatus]) ? $iconos[$id_estatus] : '';
    }

    public function cobroEquipoTaller($numOrden)
    {
        return redirect()->route('taller.print', $numOrden, false);
    }

    public function cobroEquipoTallerFinal($numOrden)
    {
        return redirect()->route('taller.print-final', $numOrden, false);
    }

    public function anteriorEstatus($numOrden, $idEstatus)
    {
        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

        if ($idEstatus >= 2) 
        {
            $idEstatus--;
            $equipoTaller->id_estatus = $idEstatus;
            $equipoTaller->save();
        }
    }

    public function toolTipAnteriorEstatus($idEstatus)
    {
        $idEstatus--;

        $estatusEquipo = EstatusEquipo::findorFail($idEstatus);
        return "Cambiar a " . $estatusEquipo->descripcion;
    }

    public function siguienteEstatus($numOrden, $idEstatus)
    {
        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

        if ($idEstatus <= 5)
        {
            $idEstatus++;
            $equipoTaller->id_estatus = $idEstatus;
            $equipoTaller->save();
        }
    }

    public function toolTipSiguienteEstatus($idEstatus)
    {
        $idEstatus++;

        $estatusEquipo = EstatusEquipo::findorFail($idEstatus);
        return "Cambiar a " . $estatusEquipo->descripcion;
    }

    public function editaEquipoTaller($num_orden)
    {
        $this->muestraDivAgregaEquipo = true;
        $this->dispatch('editaEquipoTaller', $num_orden);
    }

    public function abreAgregaEquipo()
    {
        $this->muestraDivAgregaEquipo = true;
        $this->dispatch('muestraDivAgregaEquipo');
    }

    public function cierraAgregaEquipo()
    {
        $this->muestraDivAgregaEquipo = false;
        $this->dispatch('ocultaDivAgregaEquipo');
    }

    public function anotacionesModal($numOrden)
    {
        $equipoTaller = EquipoTaller::find($numOrden);

        $this->anotacionesMod['numOrden'] = $equipoTaller->num_orden;
        if ($equipoTaller->equipo->marca->disponible)
        {
            if($equipoTaller->equipo->marca->id_tipo_equipo === $equipoTaller->equipo->id_tipo)
            {
                $this->anotacionesMod['marcaEquipo'] = $equipoTaller->equipo->marca->nombre;
            }
            else
            {
                $this->anotacionesMod['marcaEquipo'] = "*****";
            }
        }
        else
        {
            $this->anotacionesMod['marcaEquipo'] = $equipoTaller->equipo->marca->nombre . "*";
        }

        if ($equipoTaller->equipo->modelo->disponible)
        {
            if($equipoTaller->equipo->modelo->id_marca === $equipoTaller->equipo->marca->id)
            {
                $this->anotacionesMod['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre;
            }
            else
            {
                $this->anotacionesMod['modeloEquipo'] = "*****";
            }
        }
        else
        {
            $this->anotacionesMod['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre . "*";
        }

        $this->anotacionesMod['clienteEquipo'] = $equipoTaller->equipo->cliente->nombre;
        $this->anotacionesMod['estatusEquipo'] = $equipoTaller->id_estatus;

        $this->anotacionesMod['contenido'] = "";

        $anotaciones = AnotacionEquipoTaller::find($numOrden);
        if ($anotaciones)
        {
            $this->anotacionesMod['contenido'] = $anotaciones->contenido;
        }

        $this->abreModalAnotaciones = true;
    }

    public function guardaAnotaciones()
    {
        if (strlen(trim($this->anotacionesMod['contenido'])) == 0)
        {
            $anotaciones = $this->regresaAnotaciones($this->anotacionesMod['numOrden']);

            if ($anotaciones)
            {
                $anotaciones->delete();
                session()->flash('success', 'Las ANOTACIONES se han *actualizado* correctamente.');
            }
        }
        else
        {
            $anotaciones = $this->regresaAnotaciones($this->anotacionesMod['numOrden']);
            if ($anotaciones)
            {
                $anotaciones->contenido = trim(mb_strtoupper($this->anotacionesMod['contenido']));
                $anotaciones->save();

                session()->flash('success', 'Las ANOTACIONES se han actualizado correctamente.');
            }
            else
            {
                $anotaciones = new AnotacionEquipoTaller();
                $anotaciones->num_orden = trim(mb_strtoupper($this->anotacionesMod['numOrden']));
                $anotaciones->contenido = trim(mb_strtoupper($this->anotacionesMod['contenido']));
                $anotaciones->save();

                session()->flash('success', 'Las ANOTACIONES se han agregado correctamente.');
            }
        }

        $this->abreModalAnotaciones = false;
    }

    public function cierraModalAnotaciones()
    {
        $this->abreModalAnotaciones = false;
    }

    public function regresaAnotaciones($numOrden)
    {
        $anotaciones = AnotacionEquipoTaller::find($numOrden);

        return $anotaciones;
    }

    #[On('ocultaDivAgregaEquipo')] 
    public function cierraDivAgregaEquipo()
    {
        $this->muestraDivAgregaEquipo = false;
    }

    #[On('descartaEquipo')] 
    public function ocultaDivArriba()
    {
        // $this->dispatch('mostrarBoton');
        $this->muestraDivAgregaEquipo = false;
    }


}
