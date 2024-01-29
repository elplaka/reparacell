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
use Carbon\Carbon;
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Taller extends Component
{
    use WithPagination;

    protected $listeners = ['f4-pressed' => 'cobrar'];

    public $muestraDivAgregaEquipo;
    public $numberOfPaginatorsRendered = [];
    public $estatusEquipos;

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
        'entregados' => null
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
        'idEstatusEquipo' => null
    ];

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

    public function cobroFinalEquipoTaller($numOrden)
    {
        $cobro = CobroEstimadoTaller::where('num_orden', $numOrden)
        ->orderBy('id', 'desc')
        ->first();

        $this->estatusEquipos = EstatusEquipo::whereIn('id', [5, 6])->get();

        $this->cobroFinal['numOrden'] = $numOrden;
        $this->cobroFinal['cliente'] = $cobro->equipoTaller->equipo->cliente->nombre;
        $this->cobroFinal['fecha'] = now();
        $this->cobroFinal['tipoEquipo'] = $cobro->equipoTaller->equipo->tipo_equipo->nombre;
        $this->cobroFinal['marcaEquipo'] = $cobro->equipoTaller->equipo->marca->nombre;
        $this->cobroFinal['modeloEquipo'] = $cobro->equipoTaller->equipo->modelo->nombre;
        $this->cobroFinal['cobroEstimado'] = $cobro->cobro_estimado;
        $this->cobroFinal['cobroRealizado'] = $cobro->cobro_estimado;
        $this->cobroFinal['idEstatusEquipo'] = 5;
        $this->cobroFinal['fallasEquipo'][] = null;
        $this->cobroFinal['fallasEquipo'] = $cobro->equipoTaller->fallas;

        $this->cobroFinal['fallasEquipo'] = $cobro->equipoTaller->fallas->map(function ($falla) {
            return [
                'descripcion' => $falla->falla->descripcion,
            ];
        })->toArray();

        $this->dispatch('lanzaCobroModal');  //Abre la ventana modal con Javascript en el layout.main
      
    }

    public function cobrar($numOrden)
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
            // dump($this->busquedaEquipos['entregados']);
            // dd($this->busquedaEquipos['idEstatus']);
        }

        if (isset($this->busquedaEquipos['idTipo']) && $this->busquedaEquipos['idTipo'] != [])
        {
            $equipos_taller->whereHas('equipo', function ($query) {
                $query->whereIn('id_tipo', $this->busquedaEquipos['idTipo']);
            });
        }

        $equipos_taller = $equipos_taller->paginate(10);
        $estatus_equipos = EstatusEquipo::all();
        $tipos_equipos = TipoEquipo::all();

        $this->dispatch('contentChanged');

        return view('livewire.taller', compact('equipos_taller', 'estatus_equipos', 'tipos_equipos'));
        // return view('livewire.taller', compact('equipos_taller', 'tipos_equipos'));
    }


    public function mount()
    {
        $this->muestraDivAgregaEquipo = false;
        $this->numberOfPaginatorsRendered = [];

        $this->busquedaEquipos = [
            'fechaEntradaInicio' => now()->subDays(30)->toDateString(),
            'fechaEntradaFin' => now()->toDateString(),
            'idEstatus' => [1,2,3,4],
            'idTipo' => null,
            'entregados' => 'no_entregados'
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
            'idEstatusEquipo' => null
        ];
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

        if ($idEstatus <= 3)
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

    #[On('ocultaDivAgregaEquipo')] 
    public function cierraDivAgregaEquipo()
    {
        $this->muestraDivAgregaEquipo = false;
    }

    #[On('descartaEquipo')] 
    public function ocultaDivArriba()
    {
        $this->muestraDivAgregaEquipo = false;
        $this->dispatch('mostrarBoton');
    }

}
