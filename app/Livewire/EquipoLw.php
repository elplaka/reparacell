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
use Livewire\Attributes\On;

class EquipoLw extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $clientesMod, $datosCargados;
    public $busquedaClienteHabilitada;
    public $nombreClienteAux;
    public $muestraHistorialEquipoTaller;

    // protected $listeners = [
    //     'lisGuardarEquipoExistente' => 'guardarEquipoExistente',
    //     // 'lisBorraAbono' => 'borraAbono',
    // ]; 
    

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

    #[On('lisGuardarEquipoExistente')] 
    public function guardarEquipoExistente($idTipo, $idMarca, $idModelo, $idCliente)
    {
        $equipo = new Equipo();
        $equipo->id_tipo = $idTipo;
        $equipo->id_marca = $idMarca;
        $equipo->id_modelo = $idModelo;
        $equipo->id_cliente = $idCliente;
        $equipo->save();

        session()->flash('success', 'Equipo guardado exitosamente.');
    }

    //ME QUEDÉ IMPIDIENDO QUE NO SE PERMITA INSERTAR (ACTUALIZAR) EQUIPOS DUPLICADOS PARA EL CLIENTE
    public function agregaEquipo()
    {
        $this->validate();

        $equipoBD = Equipo::where('id_tipo', $this->equipoMod['idTipo'])->where('id_marca', $this->equipoMod['idMarca'])->where('id_modelo', $this->equipoMod['idModelo'])->where('id_cliente', $this->equipoMod['idCliente'])->first();

        if ($equipoBD)
        {
            $this->dispatch('mostrarToastAceptarCancelarParam', 
            'El cliente ' . $this->equipoMod['nombreCliente'] . ' ya tiene un equipo con estas características. ¿Deseas agregar de todas formas?', 'lisGuardarEquipoExistente', $this->equipoMod['idTipo'], $this->equipoMod['idMarca'], $this->equipoMod['idModelo'], $this->equipoMod['idCliente']);
        }
        else
        {
            $equipo = new Equipo();
            $equipo->id_tipo = $this->equipoMod['idTipo'];
            $equipo->id_marca = $this->equipoMod['idMarca'];
            $equipo->id_modelo = $this->equipoMod['idModelo'];
            $equipo->id_cliente = $this->equipoMod['idCliente'];
            $equipo->save();

            session()->flash('success', 'Equipo guardado exitosamente.');
        }
    }

    public function cambia()
    {
        $this->render();
    }

    #[On('lisActualizarEquipoExistente')] 
        public function actualizarEquipoExistente($idTipo, $idMarca, $idModelo, $idCliente)
    {
        $equipo = Equipo::find($this->equipoMod['id']);

        // Verifica si el equipo existe
        if ($equipo) {
            // Actualiza el equipo con los nuevos valores
            $equipo->update([
                'id_tipo' => $idTipo,
                'id_marca' => $idMarca,
                'id_modelo' => $idModelo,
                'id_cliente' => $idCliente,
            ]);
        } else {
            // Manejo de error si el equipo no se encuentra
            session()->flash('error', 'Equipo no encontrado');
        }

        session()->flash('success', 'Equipo actualizado exitosamente.');
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

        $equipoBD = Equipo::where('id', '!=', $this->equipoMod['id'])->where('id_tipo', $this->equipoMod['idTipo'])->where('id_marca', $this->equipoMod['idMarca'])->where('id_modelo', $this->equipoMod['idModelo'])->where('id_cliente', $this->equipoMod['idCliente'])->first();

        if ($equipoBD)
        {
            $this->dispatch('mostrarToastAceptarCancelarParam', 
            'El cliente ' . $this->equipoMod['nombreCliente'] . ' ya tiene un equipo con estas características. ¿Deseas actualizar de todas formas?', 'lisActualizarEquipoExistente', $this->equipoMod['idTipo'], $this->equipoMod['idMarca'], $this->equipoMod['idModelo'], $this->equipoMod['idCliente']);
        }
        else
        {
            $equipo->id_tipo = $this->equipoMod['idTipo'];
            $equipo->id_marca = $this->equipoMod['idMarca'];
            $equipo->id_modelo = $this->equipoMod['idModelo'];
            $equipo->id_cliente = $this->equipoMod['idCliente'];
            $equipo->save();
    
            session()->flash('success', 'Equipo actualizado exitosamente.');
        }   
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
        else
        {
            if (!$marca->disponible) //LA MARCA NO ESTÁ DISPONIBLE
            {
                $idMar = 0;
            }
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
        else
        {
            if (!$modelo->disponible) //EL MODELO NO ESTÁ DISPONIBLE
            {
                $idMod = 0;
            }
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
        $this->resetErrorBag();

        $this->datosCargados = false;
        $this->busquedaClienteHabilitada = false;

        $equipo = Equipo::find($idEquipo);
        
        if ($equipo)
        {
            $this->equipoMod['id'] = $equipo->id;
            $this->equipoMod['idTipo'] = $equipo->id_tipo;
            $this->equipoMod['idMarca'] = $this->validaMarca($this->equipoMod['idTipo'], $equipo->id_marca);
            $this->equipoMod['idModelo'] =  $this->equipoMod['idMarca'] ? $this->validaModelo($equipo->id_marca, $equipo->id_modelo) : 0;
            $this->equipoMod['idCliente'] = $equipo->id_cliente;
            if ($equipo->cliente->disponible)
            {
                $this->equipoMod['nombreCliente'] = $equipo->cliente->nombre;
            }
            else
            {
                $this->equipoMod['nombreCliente'] = "*";
            }
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

        $equipos = Equipo::whereHas('tipo_equipo', function ($query) {
            $query->where('disponible', 1);
        })->paginate(10);
        
        $marcas = MarcaEquipo::where('disponible', 1)
        ->whereHas('tipoEquipo', function ($query) {
            $query->where('disponible', 1);
        })
        ->orderBy('nombre')
        ->get();

        $modelos = ModeloEquipo::where('disponible', 1)
        ->whereHas('marca', function ($query) {
            $query->where('disponible', 1)
                ->whereHas('tipoEquipo', function ($query) {
                    $query->where('disponible', 1);
                });
        })
        ->orderBy('nombre')
        ->get();

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
            $this->clientesMod = Cliente::where('nombre', 'like', '%'. $this->equipoMod['nombreCliente'] . '%')->where('disponible', 1)->get();
        }

        if ($hayFiltros)
        {
            $equipos = $equiposQuery->whereHas('tipo_equipo', function ($query) {
                $query->where('disponible', 1);
            })->paginate(10);            
        }

        $historialEquipoTaller = null;
        $idEquipo = $this->equipoMod['id'];

        if ($this->muestraHistorialEquipoTaller)
        {
            $historialEquipoTaller = EquipoTaller::where('id_equipo', $idEquipo)->paginate(10, ['*'], 'historial-equipo-taller');

            $equipo = Equipo::findOrFail($idEquipo);
    
            $this->equipoHistorial['iconoTipo'] = $equipo->tipo_equipo->icono;
            if($equipo->marca->id_tipo_equipo === $equipo->id_tipo)
            {                        
                if($equipo->marca->disponible)
                {
                    $nombreMarca = $equipo->marca->nombre;
                }
                else 
                {
                    $nombreMarca = $equipo->marca->nombre . "*";
                }
            }
            else 
            {
                $nombreMarca = "*****";
            }

            if($equipo->modelo->id_marca === $equipo->marca->id)
            {
                if ($equipo->modelo->disponible)
                {
                    $nombreModelo = $equipo->modelo->nombre;
                }
                else 
                {
                    $nombreModelo = $equipo->modelo->nombre . "*";
                }
            }
            else 
            {
                $nombreModelo = "*****";
            }

            if ($equipo->cliente->disponible)
            {
                $nombreCliente = $equipo->cliente->nombre;
            }
            else 
            {
                $nombreCliente = $equipo->cliente->nombre . "*";
            }
            $this->equipoHistorial['marca'] = $nombreMarca;
            $this->equipoHistorial['modelo'] = $nombreModelo;
            $this->equipoHistorial['cliente'] = $nombreCliente; 
        }

        $tiposEquipos = TipoEquipo::where('disponible', 1)->get();

        return view('livewire.equipos.index', compact('equipos', 'tiposEquipos', 'marcas', 'modelos', 'historialEquipoTaller'));
    }
   
}
