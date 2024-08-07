<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EquipoTaller;
use App\Models\Cliente;
use App\Models\TipoEquipo;
use App\Models\EstatusEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\FallaEquipo;
use App\Models\FallaEquipoTaller;
use App\Models\CobroTaller;
use App\Models\CobroEstimadoTaller;
use App\Models\AnotacionEquipoTaller;
use App\Models\CobroTallerCredito;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\Snappy\Facades\SnappyPdf;

class Taller extends Component
{
    use WithPagination;

    protected $listeners = [
        'f2-pressed' => 'cobroLiquidar',
        'f3-pressed' => 'cobroCredito',
        'f4-pressed' => 'cobrar',
        'f10-pressed' => 'abrirCorteCaja', 
        'lisLiquidarCobroCredito' => 'liquidarCobroCredito',
        'lisBorraAbono' => 'borraAbono',
    ]; 

    public $muestraDivAgregaEquipo;
    public $numberOfPaginatorsRendered = [];
    public $estatusEquipos;
    public $datosCobroCargados;
    public $detallesCredito;
    public $modalCobroFinalAbierta;
    public $modalCobroCreditoTallerAbierta;
    public $muestraDivAbono;
    public $showMainErrors, $showModalErrors, $usuariosModal;
    public $sumaAbonos, $montoLiquidar;
    public $datosCargados;

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
        'entregados' => null,
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
        'cobroRealizado' => null,
        'fallasEquipo' => [],
        'idEstatusEquipo' => null,
        'anticipo' => null,
        'restante' => null,
        'publicoGeneral' => null
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

    public $anotacionesMod =
    [
        'numOrden' => null,
        'marcaEquipo' => null,
        'modeloEquipo' => null,
        'clienteEquipo' => null,
        'contenido' => null,
        'estatusEquipo' => null,
    ];

    public $corteCaja = [
        'fechaInicial',
        'fechaFinal',
        'cajero',
        'idUsuario',
        'incluyeCredito'
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
        list($property, $index) = explode('.', $propertyName);

        if ($property === 'busquedaEquipos' && $index === 'entregados') 
        {
            $this->busquedaEquipos['idEstatus'] = [];

            if (in_array('entregados', $this->busquedaEquipos['entregados'])) {
                $this->busquedaEquipos['idEstatus'] = [5, 6];
            }
    
            // Verificar las opciones seleccionadas en "no_entregados" y asignar valores correspondientes a "idEstatus"
            if (in_array('no_entregados', $this->busquedaEquipos['entregados'])) {
                $this->busquedaEquipos['idEstatus'] = array_merge($this->busquedaEquipos['idEstatus'], [1, 2, 3, 4]);
            }

            for ($i = 1; $i <= 6; $i++) {
                if (in_array((string)$i, $this->busquedaEquipos['entregados'])) {
                    $this->busquedaEquipos['idEstatus'] = array_merge($this->busquedaEquipos['idEstatus'], [$i]);
                }
            }
        }
    }

    public function updatedCobroFinalCobroRealizado($value)
    {
        if (strlen(trim($this->cobroFinal['cobroRealizado'])) == 0) 
        {
            $this->cobroFinal['restante'] = 0 - $this->cobroFinal['anticipo'];
        }
        else
        {
            $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
        }
    }

    public function cierraCobroFinalModal()
    {
        $this->modalCobroFinalAbierta = false;
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
        $this->cobroFinal['anticipo'] = null;
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

        // if ($cobro->credito)
        // {
        //     $this->cobroFinal['anticipo'] = $cobro->credito->detalles->where('num_orden', $numOrden)->where('id_abono', 0)->first()->abono;
        //     $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
        // }

        if ($cobro->credito && $detalle = $cobro->credito->detalles->where('num_orden', $numOrden)->where('id_abono', 0)->first()) {
            $this->cobroFinal['anticipo'] = $detalle->abono;
            $this->cobroFinal['restante'] = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
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
                        ]);

                        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                        $equipoTaller->fecha_salida = now();
                        $equipoTaller->id_estatus = $this->cobroFinal['idEstatusEquipo'];
                        $equipoTaller->save();

                        $idCliente = $equipoTaller->equipo->cliente->id;

                        if (is_null($this->cobroFinal['anticipo']))
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

                        // $this->cobroACredito['tipoEquipo'] = $equipoTaller->equipo->tipo_equipo->nombre;
                        // $this->cobroACredito['marcaEquipo'] = $equipoTaller->equipo->marca->nombre;
                        // $this->cobroACredito['modeloEquipo'] = $equipoTaller->equipo->modelo->nombre;

                        $this->cobroACredito['idEstatus'] = 1;
                        $this->cobroACredito['estatus'] = "SIN LIQUIDAR";

                        if ($equipoTaller->cobroTallerCredito)
                        {
                            $this->cobroACredito['idEstatus'] = $equipoTaller->cobroTallerCredito->estatus->id;
                            $this->cobroACredito['estatus'] = $equipoTaller->cobroTallerCredito->estatus->descripcion;
                        }

                        if ($equipoTaller->cobroTaller)
                        {
                            $this->cobroACredito['monto'] = $equipoTaller->cobroTaller->cobro_realizado;
                        }

                        $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;
                
                        $this->modalCobroCreditoTallerAbierta = true;
                
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

                        $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
                        $cobroTallerCreditoDetalles->num_orden = $numOrden;
                        $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        $cobroTallerCreditoDetalles->abono = $this->cobroACredito['abono'];
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

    public function abreCobroCredito($numOrden)
    {
        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();

        $idCliente = $equipoTaller->equipo->cliente->id;

        $this->cobroACredito['nombreCliente'] = $equipoTaller->equipo->cliente->disponible ? $equipoTaller->equipo->cliente->nombre : $equipoTaller->equipo->cliente->nombre . "*";
        $this->cobroACredito['numOrden'] = $numOrden;
        
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
        $this->sumaAbonos = $this->detallesCredito->sum('abono');
        $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;

        $this->modalCobroCreditoTallerAbierta = true;

        $this->muestraDivAbono = false;

        $this->cobroACredito['abono'] = null;

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
        if ($this->modalCobroFinalAbierta && $this->cobroFinal['anticipo'])
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
                        ]);

                        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                        $equipoTaller->fecha_salida = now();
                        $equipoTaller->id_estatus = $this->cobroFinal['idEstatusEquipo'];
                        $equipoTaller->save();
                            
                        $ultimoIdAbono = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->max('id_abono');

                        $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
                        $cobroTallerCreditoDetalles->num_orden = $numOrden;
                        $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        $abono = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
                        $cobroTallerCreditoDetalles->abono = $abono;
                        $cobroTallerCreditoDetalles->save();

                        $cobroTallerCredito = CobroTallerCredito::where('num_orden', $numOrden)->first();
                        $cobroTallerCredito->id_estatus = 2;
                        $cobroTallerCredito->save();

                        $this->dispatch('cierraCobroModal');
                        $this->dispatch('mostrarToast', 'Cobro liquidado con éxito!!!');

                        return redirect()->route('taller.print-final', $numOrden);
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
               $this->cobroACredito['monto'] = $this->detallesCredito->first()->cobroCredito->cobroTaller->cobro_realizado;
               $this->sumaAbonos = $this->detallesCredito->sum('abono');
               $this->montoLiquidar = $this->cobroACredito['monto'] - $this->sumaAbonos;
       
               $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
               $cobroTallerCreditoDetalles->num_orden = $numOrden;
               $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
               $cobroTallerCreditoDetalles->abono = $this->montoLiquidar;
               $cobroTallerCreditoDetalles->id_usuario_cobro = Auth::id();
               $cobroTallerCreditoDetalles->save();

                CobroTallerCredito::where('num_orden', $numOrden)->update(['id_estatus' => 2]);
                $this->cobroACredito['estatus'] = $cobroTallerCreditoDetalles->first()->cobroCredito->estatus->descripcion;
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

    public function cobrar($numOrden)
    {
        if ($this->modalCobroFinalAbierta && !$this->cobroFinal['anticipo'])
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
                        ]);

                        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                        $equipoTaller->fecha_salida = now();
                        $equipoTaller->id_estatus = $this->cobroFinal['idEstatusEquipo'];
                        $equipoTaller->save();

                        if ($this->cobroFinal['anticipo'])  //Si hay anticipo agrega el abono
                        {
                            $ultimoIdAbono = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->max('id_abono');

                            $cobroTallerCreditoDetalles = new CobroTallerCreditoDetalle();
                            $cobroTallerCreditoDetalles->num_orden = $numOrden;
                            $cobroTallerCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                            $abono = $this->cobroFinal['cobroRealizado'] - $this->cobroFinal['anticipo'];
                            $cobroTallerCreditoDetalles->abono = $abono;
                            $cobroTallerCreditoDetalles->save();

                            if ($abono < 0)  //Está liquidado el cobro
                            {
                                $cobroTallerCredito = CobroTallerCredito::where('num_orden', $numOrden)->first();
                                $cobroTallerCredito->id_estatus = 2;
                                $cobroTallerCredito->save();
                            }
                        }

                        $this->dispatch('cierraCobroModal');
                        $this->dispatch('mostrarToast', 'Cobro realizado con éxito!!!');

                        return redirect()->route('taller.print-final', $numOrden);
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

        // $this->dispatch('contentChanged');

        return view('livewire.taller', compact('equipos_taller', 'estatus_equipos', 'tipos_equipos'));
    }

    public function preguntaBorraAbono($numOrden, $idAbono)
    {
        $this->cobroACredito['idAbonoSeleccionado'] = $idAbono;
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas eliminar el abono seleccionado?', 'lisBorraAbono');
    }

    public function borraAbono()
    {
        $numOrden = $this->cobroACredito['numOrden'];
        $idAbono = $this->cobroACredito['idAbonoSeleccionado'];

        $detalleCredito =  CobroTallerCreditoDetalle::where('num_orden', $numOrden)
        ->where('id_abono', $idAbono)
        ->delete();

        if ($detalleCredito)
        {
            $this->detallesCredito = CobroTallerCreditoDetalle::where('num_orden', $numOrden)->get();

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
    }

    public function generaCorteCajaPDF()
    {
        $this->corteCaja = Session::get('corteCaja');
        $cajeroSeleccionado = false;

        if ($this->corteCaja['idUsuario'] != 0)
        {
            $this->corteCaja['cajero'] = User::findOrFail($this->corteCaja['idUsuario']);
            $cajeroSeleccionado = true;
        }

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            $tituloCorteCaja = 'CORTE DE CAJA DE TALLER DEL DÍA :: ' .  $this->formatearFecha($this->corteCaja['fechaInicial']);
        }
        else
        {
            $tituloCorteCaja = 'CORTE DE CAJA DE TALLER DEL ' .  $this->formatearFecha($this->corteCaja['fechaInicial']) . ' AL ' . $this->formatearFecha($this->corteCaja['fechaFinal']);
        }

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            if ($cajeroSeleccionado)
            {
                $cobrosCorteCaja = CobroTaller::whereDate('created_at', '=', $this->corteCaja['fechaInicial'])->whereHas('equipoTaller', function ($query) {
                    $query->where('id_usuario_recibio', $this->corteCaja['idUsuario']);
                })
                ->get();
            }
            else
            {
                $cobrosCorteCaja = CobroTaller::whereDate('created_at', '=', $this->corteCaja['fechaInicial'])
                ->get();
            }
        }
        else
        {
            if ($cajeroSeleccionado)
            {
                $cobrosCorteCaja = CobroTaller::whereDate('created_at', '>=', $this->corteCaja['fechaInicial'])
                ->whereDate('created_at', '<=', $this->corteCaja['fechaFinal'])
                ->whereHas('equipoTaller', function ($query) {
                    $query->where('id_usuario_recibio', $this->corteCaja['idUsuario']);
                })
                ->get();
            }
            else
            {
                $cobrosCorteCaja = CobroTaller::whereDate('created_at', '>=', $this->corteCaja['fechaInicial'])
                ->whereDate('created_at', '<=', $this->corteCaja['fechaFinal'])
                ->get();
            }
        }

        $pdf = SnappyPdf::loadView('taller.corte-caja', ['corteCaja' => $this->corteCaja, 'cobros' => $cobrosCorteCaja])
        ->setOption('page-size', 'Letter')
        ->setOption('margin-top', 30)
        ->setOption('header-html', view('livewire.pdf.encabezado', compact('tituloCorteCaja'))->render())
        ->setOption('header-spacing', 5)
        ->setOption('footer-center', 'Página [page] de [topage]')
        // ->setOption('footer-right', $this->corteCaja['cajero'])
        ->setOption('footer-font-size', '8')
        ->setOption('footer-font-name', 'Montserrat');


        return $pdf->stream('test.pdf');
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

    public function cierraCorteCajaModal()
    {

    }

    public function irCorteCaja()
    {
        Session::put('corteCaja', $this->corteCaja);

        $this->dispatch('abrirPestanaCorteCajaTaller');
    }

    public function mount()
    {
        $this->muestraDivAgregaEquipo = false;
        $this->numberOfPaginatorsRendered = [];
        $this->paginaActual = 1;
        $this->datosCobroCargados = false;
        $this->muestraDivAbono = false;

        $this->usuariosModal = User::where('disponible', 1)->get();

        $this->corteCaja = [
            'fechaInicial' => now()->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'cajero' => Auth::user()->name,
            'idUsuario' => 0,
            'incluyeCredito' => false
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
            'restante' => null,
            'publicoGeneral' => null
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
            'idAbonoSeleccionado' => null
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

        $this->datosCargados = true;
    }

    #[On('agregaEquipoAlTaller')] 
    public function refrescaTabla()
    {
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
        return redirect()->route('taller.print', $numOrden);
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
        $this->dispatch('mostrarBoton');
        $this->muestraDivAgregaEquipo = false;
    }


}
