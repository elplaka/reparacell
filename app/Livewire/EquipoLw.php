<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Equipo;
use App\Models\EquipoTaller;
use App\Models\TipoEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\Cliente;

class EquipoLw extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $clientesMod, $datosCargados;
    public $busquedaClienteHabilitada;
    public $nombreClienteAux;
    public $muestraHistorialEquipoTaller;

    public $filtrosEquipos = [
        'id' => null,
        'idTipo' => null,
        'idMarca' => null,
        'idModelo' => null,
        'nombreCliente' => null,
        'disponible' => null
    ];

    public $equipoMod = [
        'id' => null,
        'idTipo' => null,
        'idMarca' => null,
        'idModelo' => null,
        'idCliente' => null,
        'nombreCliente' => null,
        'disponible' => null
    ];

    public $equipoHistorial = [
        'id' => null,
        'iconoTipo' => null,
        'marca' => null,
        'modelo' => null,
        'cliente' => null
    ];

    protected $rules = [
        'equipoMod.idTipo' => 'required|integer|min:1',
        'equipoMod.idMarca' => 'required|integer|min:1',
        'equipoMod.idModelo' => 'required|integer|min:1',
        'equipoMod.idCliente' => 'required|integer|min:1',
    ];

    protected $messages = [
        'equipoMod.idTipo.min' => 'Selecciona un TIPO DE EQUIPO',
        'equipoMod.idMarca.min' => 'Selecciona una MARCA DE EQUIPO',
        'equipoMod.idModelo.min' => 'Selecciona un MODELO DE EQUIPO',
        'equipoMod.idCliente.min' => 'Selecciona un CLIENTE',
    ];


    public function mount()
    {
        $this->filtrosEquipos = [
            'id' => null,
            'idTipo' => null,
            'idMarca' => null,
            'idModelo' => null,
            'nombreCliente' => null,
            'disponible' => null
        ];

        $this->clientesMod = [];
    }

    public function abreHistorialTaller($idEquipo)
    {
        $this->muestraHistorialEquipoTaller = true;

        $this->equipoMod['id'] = $idEquipo;

        // if ($this->historialEquipoTaller->isEmpty())
        // {
        //     $equipo = Equipo::findOrFail($idEquipo);

        //     $this->equipoHistorial['iconoTipo'] = $equipo->tipo_equipo->icono;
        //     $this->equipoHistorial['marca'] = $equipo->marca->nombre;
        //     $this->equipoHistorial['modelo'] = $equipo->modelo->nombre;
        //     $this->equipoHistorial['cliente'] = $equipo->cliente->nombre; 
        // }
        // else
        // {
        //     $this->equipoHistorial['iconoTipo'] = $this->historialEquipoTaller->first()->equipo->tipo_equipo->icono;
        //     $this->equipoHistorial['marca'] = $this->historialEquipoTaller->first()->equipo->marca->nombre;
        //     $this->equipoHistorial['modelo'] = $this->historialEquipoTaller->first()->equipo->modelo->nombre;
        //     $this->equipoHistorial['cliente'] = $this->historialEquipoTaller->first()->equipo->cliente->nombre;
        // }
    }

    public function cierraModalEquipoHistorial()
    {
        $muestraHistorialEquipoTaller = false;
    }

    public function invertirEstatusEquipo($idEquipo)
    {
        $equipo = Equipo::findOrFail($idEquipo);

        $activacion = $equipo->disponible ? true : false; 
        $equipo->disponible = !$equipo->disponible;
        $equipo->save();          
    } 

    public function abreNuevoEquipo()
    {
        $this->datosCargados = false;
        $this->busquedaClienteHabilitada = false;

        $this->equipoMod['idTipo'] = 1;

        $this->datosCargados = true;

        $this->habilitaBusquedaCliente();

        $this->showModalErrors = true;
        $this->showMainErrors = false;
    }

    public function agregaEquipo()
    {
        $this->validate();

        $equipo = new Equipo();
        $equipo->id_tipo = $this->equipoMod['idTipo'];
        $equipo->id_marca = $this->equipoMod['idMarca'];
        $equipo->id_modelo = $this->equipoMod['idModelo'];
        $equipo->id_cliente = $this->equipoMod['idCliente'];
        $equipo->save();

        session()->flash('success', 'Equipo guardado exitosamente.');
    }

    public function cambia()
    {
        $this->render();
    }

    public function actualizaEquipo()
    {
        $this->resetErrorBag();

        $this->validate();

        $equipo = Equipo::find($this->equipoMod['id']);

        if (!$equipo) {
            $this->addError('equipoMod.id', 'El equipo no existe.');
            return;
        }

        // $equipo->id = $this->equipoMod['id'];
        $equipo->id_tipo = $this->equipoMod['idTipo'];
        $equipo->id_marca = $this->equipoMod['idMarca'];
        $equipo->id_modelo = $this->equipoMod['idModelo'];
        $equipo->id_cliente = $this->equipoMod['idCliente'];
        $equipo->save();

        session()->flash('success', 'Equipo actualizado exitosamente.');
    }

    public function habilitaBusquedaCliente()
    {
        $this->nombreClienteAux = $this->equipoMod['nombreCliente'];
        $this->busquedaClienteHabilitada = true;
        $this->equipoMod['nombreCliente'] = "";
        $this->resetErrorBag();
    }

    public function desHabilitaBusquedaCliente()
    {
        $this->busquedaClienteHabilitada = false;
        $this->equipoMod['nombreCliente'] = $this->nombreClienteAux;
    }

    public function seleccionaCliente($idCliente, $nombreCliente)
    {
        $this->equipoMod['idCliente'] = $idCliente;
        $this->equipoMod['nombreCliente'] = $nombreCliente;
        $this->nombreClienteAux = $this->equipoMod['nombreCliente'];
        $this->busquedaClienteHabilitada = false;
        $this->resetErrorBag();
    }

    public function validaMarca($idTipoEquipo, $idMarca)
    {
        $marca = MarcaEquipo::find($idMarca);

        $idMar = $idMarca;

        //SI EL ID_TIPO_EQUIPO DE LA MARCA ENCONTRADA ES DISTINTO AL IDTIPOEQUIPO DE LA MARCA GUARDADA
        //ESTA VALIDACIÓN ES PARA IDENTIFICAR LOS CASOS
        //DE TIPOS DE EQUIPS QUE NO CORRESPONDEN A LAS MARCAS  Y VICEVERSA

        if ($marca->id_tipo_equipo != $idTipoEquipo)
        {
            $idMar = 0;  //SE PONE EN 0 PARA INDICAR QUE NO SE HA SELECCIONADO LA IDMODELO
        }

        return $idMar;
    }

    public function validaModelo($idMarca, $idModelo)
    {
        $modelo = ModeloEquipo::find($idModelo);

        $idMod = $idModelo;

        //SI EL ID_MARCA DEL MODELO ENCONTRADO ES DISTINTO AL IDMARCA DEL MODELO GUARDADO
        //ESTA VALIDACIÓN ES PARA IDENTIFICAR LOS CASOS
        //DE MARCAS QUE NO CORRESPONDEN A LOS MODELOS Y VICEVERSA

        if ($modelo->id_marca != $idMarca)
        {
            $idMod = 0;  //SE PONE EN 0 PARA INDICAR QUE NO SE HA SELECCIONADO LA IDMODELO
        }

        return $idMod;
    }

    public function updatedEquipoModIdTipo()
    {
        $this->equipoMod['idMarca'] = 0;
        $this->equipoMod['idModelo'] = 0;
        $this->resetErrorBag();
    }

    public function updatedEquipoModIdMarca()
    {
        $this->equipoMod['idModelo'] = 0;
        $this->resetErrorBag();
    }

    public function editaEquipo($idEquipo)
    {
        $this->datosCargados = false;
        $this->busquedaClienteHabilitada = false;

        $equipo = Equipo::find($idEquipo);
        
        if ($equipo)
        {
            $this->equipoMod['id'] = $equipo->id;
            $this->equipoMod['idTipo'] = $equipo->id_tipo;
            $this->equipoMod['idMarca'] = $this->validaMarca($this->equipoMod['idTipo'], $equipo->id_marca);
            $this->equipoMod['idModelo'] = $this->validaModelo($equipo->id_marca, $equipo->id_modelo);
            $this->equipoMod['idCliente'] = $equipo->id_cliente;
            $this->equipoMod['nombreCliente'] = $equipo->cliente->nombre;
            $this->equipoMod['disponible'] = $equipo->disponible;
        }

        $this->datosCargados = true;

        $this->showModalErrors = true;
        $this->showMainErrors = false;
    }

    public function cierraEquipoModal()
    {
        $this->datosCargados = false;

        $this->showModalErrors = false;
        $this->showMainErrors = true;
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

    public function render()
    {
        $hayFiltros = false;
        $equipos = Equipo::paginate(10);

        $marcas = MarcaEquipo::where('disponible', 1)->orderBy('nombre')->get();
        $modelos = ModeloEquipo::where('disponible', 1)->orderBy('nombre')->get();

        $equiposQuery = Equipo::query();

        if (!is_null($this->filtrosEquipos['id']) && strlen(trim($this->filtrosEquipos['id'])) > 0)
        {
            $equiposQuery->where('id',$this->filtrosEquipos['id']);
            $hayFiltros = true;
        }

        if (!is_null($this->filtrosEquipos['idTipo']) && $this->filtrosEquipos['idTipo'] != [])
        {
            $equiposQuery->whereIn('id_tipo', $this->filtrosEquipos['idTipo']);
            $marcas = MarcaEquipo::where('id_tipo_equipo', $this->filtrosEquipos['idTipo'])->where('disponible', 1)->orderBy('nombre')->get();
            $modelos = ModeloEquipo::where('disponible', 1)
            ->whereHas('marca', function($query) {
                $query->where('id_tipo_equipo', $this->filtrosEquipos['idTipo']);
            })
            ->orderBy('nombre')
            ->get();
            $hayFiltros = true;
        }

        if (!is_null($this->filtrosEquipos['idMarca']) && $this->filtrosEquipos['idMarca'] != [])
        {
            $equiposQuery->whereIn('id_marca',$this->filtrosEquipos['idMarca']);
            if (!is_null($this->filtrosEquipos['idTipo']) && $this->filtrosEquipos['idTipo'] != [])
            {
                $modelos = ModeloEquipo::where('id_marca', $this->filtrosEquipos['idMarca'])
                ->where('disponible', 1)
                ->whereHas('marca', function($query) {
                    $query->where('id_tipo_equipo', $this->filtrosEquipos['idTipo']);
                })
                ->orderBy('nombre')
                ->get();
            }
            else
            {
                $modelos = ModeloEquipo::where('id_marca', $this->filtrosEquipos['idMarca'])
                ->where('disponible', 1)
                ->orderBy('nombre')
                ->get();
            }

            $hayFiltros = true;
        }

        if (!is_null($this->filtrosEquipos['idModelo']) && $this->filtrosEquipos['idModelo'] != [])
        {
            $equiposQuery->whereIn('id_modelo',$this->filtrosEquipos['idModelo']);
            $hayFiltros = true;
        }

        if (!is_null($this->filtrosEquipos['disponible']))
        {
            $equiposQuery->where('disponible',$this->filtrosEquipos['disponible']);
            $hayFiltros = true;
        }

        if ($this->filtrosEquipos['nombreCliente'] != '')
        {
            $nombreCliente = $this->filtrosEquipos['nombreCliente'];
            $equiposQuery->whereHas('cliente', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'LIKE', "%$nombreCliente%");
            });
            $hayFiltros = true;
        }

        if (trim($this->equipoMod['nombreCliente']) != '')
        {
            $this->clientesMod = Cliente::where('nombre', 'like', '%'. $this->equipoMod['nombreCliente'] . '%')->get();
        }

        if ($hayFiltros)
        {
            $equipos = $equiposQuery->paginate(10);            
        }

        $historialEquipoTaller = null;
        $idEquipo = $this->equipoMod['id'];

        if ($this->muestraHistorialEquipoTaller)
        {
            $historialEquipoTaller = EquipoTaller::where('id_equipo', $idEquipo)->paginate(10);

            $equipo = Equipo::findOrFail($idEquipo);
    
            $this->equipoHistorial['iconoTipo'] = $equipo->tipo_equipo->icono;
            $this->equipoHistorial['marca'] = $equipo->marca->nombre;
            $this->equipoHistorial['modelo'] = $equipo->modelo->nombre;
            $this->equipoHistorial['cliente'] = $equipo->cliente->nombre; 
        }

        $tiposEquipos = TipoEquipo::all();

        return view('livewire.equipos.index', compact('equipos', 'tiposEquipos', 'marcas', 'modelos', 'historialEquipoTaller'));
    }
   
}
