<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EquipoTaller;
use App\Models\TipoEquipo;
use App\Models\EstatusEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\FallaEquipo;
use Livewire\WithPagination;

class TallerReportes extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $marcas, $modelos, $fallas;
    public $marcasSeleccionadas = [];
    public $marcasDiv;
    public $modelosSeleccionados = [];
    public $modelosDiv;
    public $fallasSeleccionadas = [];
    public $fallasDiv;


    public $busquedaEquipos =
    [
        'fechaEntradaInicio' => null,
        'fechaEntradaFin' => null,
        'idEstatus' => null,
        'idTipos' => [],
        'idMarcas' => [],
        'idModelos' => [],
        'idFallas' => [],
        'entregados' => null,
        'nombreCliente' => null
    ];

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

    public function updatedMarcasSeleccionadas()
    {
        $this->modelosSeleccionados = [];
        $this->modelosDiv = null;

        $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;
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

        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $equipos_taller->whereHas('equipo', function ($query) {
                $query->whereIn('id_tipo', $this->busquedaEquipos['idTipos']);
            });
        }

        if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
        {
            if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
            {
                $equipos_taller->whereHas('equipo.marca', function ($query) {
                    $query->whereIn('id', $this->busquedaEquipos['idMarcas'])->whereIn('id_tipo', $this->busquedaEquipos['idTipos']);
                });
            }
            else
            {
                $equipos_taller->whereHas('equipo.marca', function ($query) {
                $query->whereIn('id', $this->busquedaEquipos['idMarcas']);
            });
            }
        }

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

        if (isset($this->busquedaEquipos['idFallas']) && $this->busquedaEquipos['idFallas'] != [])
        {
            if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
            {
                $equipos_taller->whereHas('fallas.falla', function ($query) {
                    $query->whereIn('id', $this->busquedaEquipos['idFallas'])->whereIn('id_tipo', $this->busquedaEquipos['idTipos']);
                });
            }
            else
            {
                $equipos_taller->whereHas('fallas.falla', function ($query) {
                $query->whereIn('id', $this->busquedaEquipos['idFallas']);
            });
            }
        }

        $equipos_taller = $equipos_taller->orderBy('fecha_entrada', 'asc')->paginate(10);

        $estatus_equipos = EstatusEquipo::all();
        $tipos_equipos = TipoEquipo::all();
     
        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $this->marcas = MarcaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }
        else
        {
            $this->marcas = MarcaEquipo::where('disponible', 1)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }

        if (isset($this->busquedaEquipos['idMarcas']) && $this->busquedaEquipos['idMarcas'] != [])
        {
            $this->modelos = ModeloEquipo::whereIn('id_marca', $this->busquedaEquipos['idMarcas'])->where('disponible', 1)->orderBy('nombre', 'asc')->get();
        }
        else
        {
            $this->modelos = ModeloEquipo::where('disponible', 1)->orderBy('nombre', 'asc')->get();
        }

        if (isset($this->busquedaEquipos['idTipos']) && $this->busquedaEquipos['idTipos'] != [])
        {
            $this->fallas = FallaEquipo::whereIn('id_tipo_equipo', $this->busquedaEquipos['idTipos'])->where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }
        else
        {
            $this->fallas = FallaEquipo::where('disponible', 1)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();
        }

        return view('livewire.taller.reportes', compact('equipos_taller', 'estatus_equipos', 'tipos_equipos'));
    }

    public function aceptaParamMarcasModal()
    {
        $this->marcasDiv = MarcaEquipo::whereIn('id', $this->marcasSeleccionadas)->orderBy('nombre', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();

        $this->busquedaEquipos['idMarcas'] = $this->marcasSeleccionadas;
    }

    public function aceptaParamModelosModal()
    {
        $this->modelosDiv = ModeloEquipo::whereIn('id', $this->modelosSeleccionados)->orderBy('nombre', 'asc')->orderBy('id_marca', 'asc')->get();

        $this->busquedaEquipos['idModelos'] = $this->modelosSeleccionados;
    }

    public function aceptaParamFallasModal()
    {
        $this->fallasDiv = FallaEquipo::whereIn('id', $this->fallasSeleccionadas)->orderBy('descripcion', 'asc')->orderBy('id_tipo_equipo', 'asc')->get();

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
            'nombreCliente' => null
        ];

        $this->marcasSeleccionadas = [];
        $this->marcasDiv = null;

        $this->modelosSeleccionados = [];
        $this->modelosDiv = null;

        $this->fallasSeleccionadas = [];
        $this->fallasDiv = null;
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
