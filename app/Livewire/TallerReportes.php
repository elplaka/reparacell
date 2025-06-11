<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\EquipoTaller;
use App\Models\TipoEquipo;
use App\Models\EstatusEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\FallaEquipo;
use App\Models\ModoPago;
use App\Models\CobroTaller;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;


class TallerReportes extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $marcas, $modelos, $fallas, $clientes;
    public $cobroModal, $idModoPago, $modosPago;
    public $marcasSeleccionadas = [];
    public $marcasDiv;
    public $modelosSeleccionados = [];
    public $modelosDiv;
    public $fallasSeleccionadas = [];
    public $fallasDiv;
    public $clientesSeleccionados = [];
    public $clientesDiv, $nombreCliente;
    public $chkFechaSalida;
    public $chkAgrupar;
    public $verGrafico;
    public $labels = [];
    public $valores = [];
    public $equiposTaller;

    public $busquedaEquipos =
    [
        'fechaEntradaInicio' => null,
        'fechaEntradaFin' => null,
        'idEstatus' => null,
        'idTipos' => [],
        'idMarcas' => [],
        'idModelos' => [],
        'idFallas' => [],
        'idClientes' => [],
        'entregados' => null,
        'nombreCliente' => null,
        'fechaSalidaInicio' => null,
        'fechaSalidaFin' => null,
    ];

    public function abrirEditarModoPagoModal($numOrden)
    {
        $this->cobroModal = CobroTaller::findOrFail($numOrden);

        $this->idModoPago =  $this->cobroModal->id_modo_pago;

        $this->dispatch('abreModalEditaModoPagoCobroTaller');
    }

    public function actualizarModoPago()
    {
        $this->cobroModal->id_modo_pago = $this->idModoPago;
        $this->cobroModal->update();

        $this->dispatch('cierraModalEditaModoPagoCobroTaller');
        $this->dispatch('mostrarToast', 'Modo de pago actualizado con éxito!!!');
    }

    public function eliminarFalla($fallaId)
    {
        $this->fallasSeleccionadas = array_values(array_diff($this->fallasSeleccionadas, [$fallaId]));

        $this->fallasDiv = FallaEquipo::whereIn('id', $this->fallasSeleccionadas)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();

        if ($this->fallasDiv->isEmpty())
        {
            $this->fallasDiv = null;
        }

        $this->busquedaEquipos['idFallas'] = $this->fallasSeleccionadas;
    }

    public function eliminarMarca($marcaId)
    {
        $this->marcasSeleccionadas = array_values(array_diff($this->marcasSeleccionadas, [$marcaId]));

        $this->marcasDiv = MarcaEquipo::whereIn('id', $this->marcasSeleccionadas)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();

        if ($this->marcasDiv->isEmpty())
        {
            $this->marcasDiv = null;
            $this->modelosSeleccionados = [];
            $this->modelosDiv = null;

            $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;
        }

        $this->busquedaEquipos['idMarcas'] = $this->marcasSeleccionadas;
    }

    public function eliminarModelo($modeloId)
    {
        $this->modelosSeleccionados = array_values(array_diff($this->modelosSeleccionados, [$modeloId]));

        $this->modelosDiv = ModeloEquipo::whereIn('id', $this->modelosSeleccionados)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();

        if ($this->modelosDiv->isEmpty())
        {
            $this->modelosDiv = null;
        }

        $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;
    }

    public function eliminarCliente($clienteId)
    {
        $this->clientesSeleccionados = array_values(array_diff($this->clientesSeleccionados, [$clienteId]));

        $this->clientesDiv = Cliente::whereIn('id', $this->clientesSeleccionados)->orderBy('nombre', 'asc')->get();

        if ($this->clientesDiv->isEmpty())
        {
            $this->clientesDiv = null;
        }

        $this->busquedaEquipos['idClientes'] = $this->clientesSeleccionados;
    }

    public function updated($property)
    {
        if ($property == "busquedaEquipos.idTipos.0")
        {
            if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
            {
                $this->marcas = MarcaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
            }
            else
            {
                $this->marcas = MarcaEquipo::where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
            }

            if (isset($this->busquedaEquipos['idModelos']) && $this->busquedaEquipos['idModelos'] != [])
            {
                $this->modelos = ModeloEquipo::whereIn('id_marca', $this->busquedaEquipos['idMarcas'])->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();
            }
            else
            {
                $this->modelos = ModeloEquipo::where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();
            }

            if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
            {
                $this->fallas = FallaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
            }
            else
            {
                $this->fallas = FallaEquipo::where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
            }
    
            $this->marcasSeleccionadas = [];
            $this->marcasDiv = null;

            $this->busquedaEquipos['idMarcas'] = $this->marcasSeleccionadas;

            $this->modelosSeleccionados = [];
            $this->modelosDiv = null;

            $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;

            $this->fallasSeleccionadas = [];
            $this->fallasDiv = null;

            $this->busquedaEquipos['idFallas'] = $this->fallasSeleccionadas;
        }
    }

    public function capturarFila($clienteId)   //Selecciona un cliente de la tabla de buscar clientes
    {
        array_push($this->clientesSeleccionados, $clienteId);

        session()->flash('success', 'El CLIENTE se agregó como parámetro exitosamente.');

        $this->clientesDiv = Cliente::whereIn('id', $this->clientesSeleccionados)->get();

        $this->nombreCliente = '';
        $this->clientes = null;  

        $this->busquedaEquipos['idClientes'] = $this->clientesSeleccionados;
    }

    public function updatedMarcasSeleccionadas()
    {
        $this->modelosSeleccionados = [];
        $this->modelosDiv = null;

        $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;
    }

    public function abreTallerGrafico()
    {
        // return redirect()->route('taller.graficos', ['equiposTaller' => 'equipos_taller']);
        $this->verGrafico = true;
    }

    public function updatedChkAgrupar()
    {
        if (!$this->chkAgrupar)
        {
            if ($this->verGrafico)
            {
                $this->verGrafico = false;
            }
        }
    }

    public function render()
    {
        //$equipos_taller = EquipoTaller::query()->with('equipo');

        // if (isset($this->busquedaEquipos['fechaEntradaInicio']) && isset($this->busquedaEquipos['fechaEntradaFin']))
        // {
        //     $fechaInicio = date('Y-m-d', strtotime($this->busquedaEquipos['fechaEntradaInicio']));
        //     $fechaFin = date('Y-m-d', strtotime($this->busquedaEquipos['fechaEntradaFin']));

        //     if ($fechaInicio == $fechaFin)
        //     {
        //         $equipos_taller->whereDate('fecha_entrada', '=', $fechaInicio);
        //     }
        //     else
        //     {
        //         $equipos_taller->whereDate('fecha_entrada', '>=', $fechaInicio)
        //                     ->whereDate('fecha_entrada', '<=', $fechaFin);
        //     }
        // }

        // if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        // {
        //     $equipos_taller->whereHas('equipo', function ($query) {
        //         $query->whereIn('id_tipo', $this->busquedaEquipos['idTipos']);
        //     });
        // }

        // if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
        // {
        //     if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        //     {
        //         $equipos_taller->whereHas('equipo.marca', function ($query) {
        //             $query->whereIn('id', $this->busquedaEquipos['idMarcas'])->whereIn('id_tipo', $this->busquedaEquipos['idTipos']);
        //         });
        //     }
        //     else
        //     {
        //         $equipos_taller->whereHas('equipo.marca', function ($query) {
        //         $query->whereIn('id', $this->busquedaEquipos['idMarcas']);
        //     });
        //     }
        // }

        // if (isset($this->busquedaEquipos['idModelos']) && $this->busquedaEquipos['idModelos'] != [])
        // {
        //     if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
        //     {
        //         $equipos_taller->whereHas('equipo.modelo', function ($query) {
        //             $query->whereIn('id', $this->busquedaEquipos['idModelos'])->whereIn('id_marca', $this->busquedaEquipos['idMarcas']);
        //         });
        //     }
        //     else
        //     {
        //         $equipos_taller->whereHas('equipo.modelo', function ($query) {
        //         $query->whereIn('id', $this->busquedaEquipos['idModelos']);
        //     });
            
        //     }
        // }

        // if (isset($this->busquedaEquipos['idFallas']) && $this->busquedaEquipos['idFallas'] != [])
        // {
        //     if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        //     {
        //         $equipos_taller->whereHas('fallas.falla', function ($query) {
        //             $query->whereIn('id', $this->busquedaEquipos['idFallas'])->whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos']);
        //         });
        //     }
        //     else
        //     {
        //         $equipos_taller->whereHas('fallas.falla', function ($query) {
        //         $query->whereIn('id', $this->busquedaEquipos['idFallas']);
        //     });
        //     }
        // }

        // if (isset($this->busquedaEquipos['idClientes']) && $this->busquedaEquipos['idClientes'] != [])
        // {
        //     $equipos_taller->whereHas('equipo.cliente', function ($query) {
        //         $query->whereIn('id', $this->busquedaEquipos['idClientes']);
        //     });
        // }

        // if ($this->chkFechaSalida)
        // {
        //     if (isset($this->busquedaEquipos['fechaSalidaInicio']) && isset($this->busquedaEquipos['fechaSalidaFin']))
        //     {
        //         $fechaInicio = date('Y-m-d', strtotime($this->busquedaEquipos['fechaSalidaInicio']));
        //         $fechaFin = date('Y-m-d', strtotime($this->busquedaEquipos['fechaSalidaFin']));

        //         if ($fechaInicio == $fechaFin)
        //         {
        //             $equipos_taller->whereDate('fecha_salida', '=', $fechaInicio);
        //         }
        //         else
        //         {
        //             $equipos_taller->whereDate('fecha_salida', '>=', $fechaInicio)
        //                         ->whereDate('fecha_salida', '<=', $fechaFin);
        //         }
        //     }
        // }

        // $equipos_taller = $equipos_taller->orderBy('equipo.id_modelo', 'asc')->paginate(10);

        $equipos_taller = EquipoTaller::query()
        ->with(['equipo', 'equipo.marca', 'equipo.modelo', 'equipo.cliente', 'fallas']) // carga relaciones que usas
        ->join('equipos', 'equipos.id', '=', 'equipos_taller.id_equipo')
        ->join('modelos_equipos', 'modelos_equipos.id', '=', 'equipos.id_modelo');

        if (isset($this->busquedaEquipos['fechaEntradaInicio']) && isset($this->busquedaEquipos['fechaEntradaFin'])) {
            $fechaInicio = date('Y-m-d', strtotime($this->busquedaEquipos['fechaEntradaInicio']));
            $fechaFin = date('Y-m-d', strtotime($this->busquedaEquipos['fechaEntradaFin']));

            if ($fechaInicio == $fechaFin) {
                $equipos_taller->whereDate('equipos_taller.fecha_entrada', '=', $fechaInicio);
            } else {
                $equipos_taller->whereDate('equipos_taller.fecha_entrada', '>=', $fechaInicio)
                            ->whereDate('equipos_taller.fecha_entrada', '<=', $fechaFin);
            }
        }

        // Si buscas por tipos, filtro directo sobre 'equipos.id_tipo'
        if (isset($this->busquedaEquipos['idTipos']) && !empty($this->busquedaEquipos['idTipos'])) {
            $equipos_taller->whereIn('equipos.id_tipo', $this->busquedaEquipos['idTipos']);
        }

        // Si buscas por marcas, haces join con marcas y filtras
        if (isset($this->busquedaEquipos['idMarcas']) && !empty($this->busquedaEquipos['idMarcas'])) {
            // Si no has hecho el join con marcas, hazlo aquí
            $equipos_taller->join('marcas_equipos', 'marcas_equipos.id', '=', 'equipos.id_marca');

            if (isset($this->busquedaEquipos['idTipos']) && !empty($this->busquedaEquipos['idTipos'])) {
                // Filtra marcas y también tipos (ya está el join a equipos)
                $equipos_taller->whereIn('marcas_equipos.id', $this->busquedaEquipos['idMarcas'])
                            ->whereIn('equipos.id_tipo', $this->busquedaEquipos['idTipos']);
            } else {
                $equipos_taller->whereIn('marcas_equipos.id', $this->busquedaEquipos['idMarcas']);
            }
        }

        if ($this->verGrafico)
        { 
            unset($this->busquedaEquipos['idModelos']);
            // if (isset($this->busquedaEquipos['idModelos']) && !empty($this->busquedaEquipos['idModelos'])) {
            //     // Añadimos join con modelos para poder filtrar directamente
            //     $equipos_taller->join('modelos_equipos', 'modelos_equipos.id', '=', 'equipos.id_modelo');

            //     if (isset($this->busquedaEquipos['idMarcas']) && !empty($this->busquedaEquipos['idMarcas'])) {
            //         $equipos_taller->whereIn('modelos_equipos.id', $this->busquedaEquipos['idModelos'])
            //                     ->whereIn('modelos_equipos.id_marca', $this->busquedaEquipos['idMarcas']);
            //     } else {
            //         $equipos_taller->whereIn('modelos_equipos.id', $this->busquedaEquipos['idModelos']);
            //     }
            // }
        }
        else
        {
            if (isset($this->busquedaEquipos['idModelos']) && $this->busquedaEquipos['idModelos'] != [])
            {
                if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
                {
                    $equipos_taller->whereHas('equipo.modelo', function ($query) {
                        $query->whereIn('id', $this->busquedaEquipos['idModelos'])->whereIn('id_marca', $this->busquedaEquipos['idMarcas']);
                    });
                }
                else
                {
                    $equipos_taller->whereHas('equipo.modelo', function ($query) {
                    $query->whereIn('id', $this->busquedaEquipos['idModelos']);
                });
                
                }
            }
        }

         if (isset($this->busquedaEquipos['idFallas']) && $this->busquedaEquipos['idFallas'] != [])
        {
            if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
            {
                $equipos_taller->whereHas('fallas.falla', function ($query) {
                    $query->whereIn('id', $this->busquedaEquipos['idFallas'])->whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos']);
                });
            }
            else
            {
                $equipos_taller->whereHas('fallas.falla', function ($query) {
                $query->whereIn('id', $this->busquedaEquipos['idFallas']);
            });
            }
        }

        if (isset($this->busquedaEquipos['idClientes']) && !empty($this->busquedaEquipos['idClientes'])) {
            $equipos_taller->join('clientes', 'clientes.id', '=', 'equipos.id_cliente')
                        ->whereIn('clientes.id', $this->busquedaEquipos['idClientes']);
        }

        if ($this->chkFechaSalida) {
            if (isset($this->busquedaEquipos['fechaSalidaInicio']) && isset($this->busquedaEquipos['fechaSalidaFin'])) {
                $fechaInicio = date('Y-m-d', strtotime($this->busquedaEquipos['fechaSalidaInicio']));
                $fechaFin = date('Y-m-d', strtotime($this->busquedaEquipos['fechaSalidaFin']));

                if ($fechaInicio == $fechaFin) {
                    $equipos_taller->whereDate('fecha_salida', '=', $fechaInicio);
                } else {
                    $equipos_taller->whereDate('fecha_salida', '>=', $fechaInicio)
                                ->whereDate('fecha_salida', '<=', $fechaFin);
                }
            }
        }

        if ($this->chkAgrupar)
        { 
            $equipos_taller = $equipos_taller
            ->select('equipos_taller.*', 'modelos_equipos.nombre as nombre_modelo')
            ->orderBy('modelos_equipos.nombre', 'asc');

            // Ejecutas la consulta filtrada
            $equiposFiltrados = $equipos_taller->get();

            // Agrupas y cuentas por nombre del modelo en PHP
            $equiposTaller = $equiposFiltrados->groupBy('nombre_modelo')->map(function ($items, $modelo) {
                return count($items);
            });
                      
            $equipos_taller = $equipos_taller
            ->paginate(10);
        }
        else
        {
            $equiposTaller = $equipos_taller;

            $equiposFiltrados = $equipos_taller->get();

            // Agrupas y cuentas por nombre del modelo en PHP
            $equiposTaller = $equiposFiltrados->groupBy('nombre_modelo')->map(function ($items, $modelo) {
                return count($items);
            });
            

            $equipos_taller = $equipos_taller->paginate(10);
        }

        $this->labels = $equiposTaller->keys()->toArray();     
        $this->valores = $equiposTaller->values()->toArray(); 

        if ($this->verGrafico)
        {
            $this->dispatch('renderChart', labels: $this->labels, valores: $this->valores);
        }

        $estatus_equipos = EstatusEquipo::all();
        $tipos_equipos = TipoEquipo::where('disponible', 1)->get();

        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $this->marcas = MarcaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->whereHas('tipoEquipo', function ($query) {
                $query->where('disponible', 1);
            })->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }
        else
        {
            $this->marcas = MarcaEquipo::where('disponible', 1)->whereHas('tipoEquipo', function ($query) {
                $query->where('disponible', 1);
            })->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }

        if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
        {
            $this->modelos = ModeloEquipo::whereIn('id_marca', $this->busquedaEquipos['idMarcas'])->where('disponible', 1)->whereHas('marca', function ($query) {
                $query->where('disponible', 1)
                    ->whereHas('tipoEquipo', function ($query) {
                        $query->where('disponible', 1);
                    });
            })->orderBy('nombre', 'asc')->get();
        }
        else
        {
            $this->modelos = ModeloEquipo::where('disponible', 1)->whereHas('marca', function ($query) {
                $query->where('disponible', 1)
                    ->whereHas('tipoEquipo', function ($query) {
                        $query->where('disponible', 1);
                    });
            })->orderBy('nombre', 'asc')->get();
        }

        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $this->fallas = FallaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }
        else
        {
            $this->fallas = FallaEquipo::where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }

        if ($this->nombreCliente != '')
        {
            $this->clientes = Cliente::where('nombre', 'like', '%' . $this->nombreCliente .'%')->where('disponible', 1)->get();
        }

        return view('livewire.taller.reportes', compact('equipos_taller', 'estatus_equipos', 'tipos_equipos'));
    }

    public function aceptaParamMarcasModal()
    {
        $this->marcasDiv = MarcaEquipo::whereIn('id', $this->marcasSeleccionadas)->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();

        $this->busquedaEquipos['idMarcas'] = $this->marcasSeleccionadas;
    }

    public function aceptaParamModelosModal()
    {
        $this->modelosDiv = ModeloEquipo::whereIn('id', $this->modelosSeleccionados)->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();

        $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;
    }

    public function aceptaParamFallasModal()
    {
        $this->fallasDiv = FallaEquipo::whereIn('id', $this->fallasSeleccionadas)->where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();

        $this->busquedaEquipos['idFallas'] = $this->fallasSeleccionadas;
    }

    public function cierraParamMarcasModal()
    {

    }

    public function cierraParamModelosModal()
    {

    }

    public function cierraParamFallasModal()
    {

    }

    public function cierraParamClientesModal()
    {
        $this->showModalErrors = false;
        $this->showMainErrors = ! $this->showModalErrors;
    }

    public function abreParamClientesModal()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = ! $this->showModalErrors;
    }

    public function abreParamMarcasModal()
    {
        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $this->marcas = MarcaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }
        else
        {
            $this->marcas = MarcaEquipo::where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }

        $this->marcasDiv = MarcaEquipo::whereIn('id', $this->marcasSeleccionadas)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
    }

    public function abreParamModelosModal()
    {
        if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
        {
            $this->modelos = ModeloEquipo::whereIn('id_marca', $this->busquedaEquipos['idMarcas'])->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();
        }
        else
        {
            $this->modelos = ModeloEquipo::where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();
        }

        $this->modelosDiv = ModeloEquipo::whereIn('id', $this->modelosSeleccionados)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();
    }

    public function abreParamFallasModal()
    {
        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $this->fallas = FallaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }
        else
        {
            $this->fallas = FallaEquipo::where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }

        $this->fallasDiv = FallaEquipo::whereIn('id', $this->fallasSeleccionadas)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
    }

    public function muestraGrafico()
    {
        $this->verGrafico = true;

        // dd($this->equiposTaller);


        $this->dispatch('renderChart', labels: $this->labels, valores: $this->valores);

    }

    public function muestraTabla()
    {
        $this->verGrafico = false;
    }

    public function mount()
    {
        $this->busquedaEquipos = [
            'fechaEntradaInicio' => now()->subDays(30)->toDateString(),
            'fechaEntradaFin' => now()->toDateString(),
            'idEstatus' => [1,2,3,4],
            'idTipos' => [],
            'idMarcas' => [],
            'idModelos' => [],
            'idFallas' => [],
            'entregados' => 'no_entregados',
            'nombreCliente' => null,
            'fechaSalidaInicio' => now()->subDays(30)->toDateString(),
            'fechaSalidaFin' => now()->toDateString(),
        ];

        $this->marcasSeleccionadas = [];
        $this->marcasDiv = null;

        $this->modelosSeleccionados = [];
        $this->modelosDiv = null;

        $this->fallasSeleccionadas = [];
        $this->fallasDiv = null;

        $this->clientesSeleccionados = [];
        $this->clientesDiv = [];

        $this->nombreCliente = '';

        $this->chkFechaSalida = false;
        $this->chkAgrupar = false;
        
        $this->modosPago = ModoPago::where('id', '>', 0)->get();

        $this->verGrafico = false;

        // $this->labels = ['Enero', 'Febrero', 'Marzo'];
        // $this->valores = [100, 200, 150];

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
}
