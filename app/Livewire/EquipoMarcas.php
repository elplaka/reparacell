<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;


class EquipoMarcas extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $marca = [
        'idTipoEquipo',
        'nombre'
    ];

    public $filtrosMarcas =
    [
        'idTipoEquipo',
        'disponible',
        'nombre'
    ];

    public $marcaMod = [
        'id',
        'idTipoEquipo',
        'nombre'
    ];

    public $tipos_equipos;
    public $showMainErrors, $showModalErrors;
    public $guardoMarcaOK;

    public function render()
    {
        $marcas = collect();
        if ($this->filtrosMarcas['idTipoEquipo'] == 0 && $this->filtrosMarcas['disponible'] == -1 && $this->filtrosMarcas['nombre'] == '')
        {
            $marcas = MarcaEquipo::paginate(10);
        }
        else
        {
            $marcasQuery = MarcaEquipo::query();

            if ($this->filtrosMarcas['idTipoEquipo'] != 0) {
                $marcasQuery->where('id_tipo_equipo', $this->filtrosMarcas['idTipoEquipo']);
            }
            
            if ($this->filtrosMarcas['disponible'] != -1) {
                $marcasQuery->where('disponible', $this->filtrosMarcas['disponible']);
            }

            if ($this->filtrosMarcas['nombre'] != '') {
                $marcasQuery->where('nombre', 'like', '%' . $this->filtrosMarcas['nombre'] . '%');
            }
            
            $marcas = $marcasQuery->paginate(10);
            $this->goToPage(1);
        }

        return view('livewire.equipos.marcas', compact('marcas'));
    }

    public function mount()
    {
        $this->marca = [
            'idTipoEquipo'    => 1,
            'nombre'            => ''
        ];

        $this->filtrosMarcas = [
            'idTipoEquipo' => 0,
            'disponible' => -1,
            'nombre' => ''
        ];

        $this->marcaMod = [
            'idTipoEquipo' => 1,
            'nombre' => ''
        ];
        

        $this->tipos_equipos = TipoEquipo::where('disponible', 1)->get();
    }

    public function abreAgregaMarca()
    {
 
    }

    public function cierraMarcaModal()
    {
        $this->marcaMod = [
            'idTipoEquipo' => 1,
            'nombre' => null
        ];
    }

    public function marcaYaExiste($idTipoEquipo, $nombreMarca)
    {
        $marca = MarcaEquipo::where('id_tipo_equipo', $idTipoEquipo)->where('nombre', $nombreMarca)->first();

        if (is_null($marca))
        {
            return 0;   //Para indicar que el nombre de la marca no existe
        }
        else
        {
          $this->marcaMod['tipoEquipo'] = $marca->tipoEquipo->nombre;
          return $marca->disponible ? 1 : 2;  //Si la marca está disponible regresa 1 si no regresa 2
        }
    }

    public function guardaMarcaEnTabla()
    {
        try {
            DB::transaction(function () {
                $marcaEquipo = new MarcaEquipo();
                $marcaEquipo->id_tipo_equipo = $this->marcaMod['idTipoEquipo'];
                $marcaEquipo->nombre = trim(mb_strtoupper($this->marcaMod['nombre']));
                $marcaEquipo->disponible = 1;
                $marcaEquipo->save();

                $modeloEquipo = new ModeloEquipo();
                $modeloEquipo->id_marca = $marcaEquipo->id;
                $modeloEquipo->nombre = 'GENÉRICO';
                $modeloEquipo->disponible = 1;
                $modeloEquipo->save();
            });
        } catch (\Exception $e) {
            // Manejo de errores si ocurre una excepción
            // Puedes agregar logs o notificaciones aquí
            dd($e);
        }
    }

    public function guardaMarca()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'marcaMod.nombre' => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'marcaMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'marcaMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'marcaMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        $estatusMarca = $this->marcaYaExiste($this->marcaMod['idTipoEquipo'], trim(mb_strtoupper($this->marcaMod['nombre'])));

        if ($estatusMarca == 1)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->marcaMod['nombre'])) . ' ya existe para el tipo de equipo ' . $this->marcaMod['tipoEquipo'] . '. Intenta con otro nombre.');

        }
        elseif ($estatusMarca == 2)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->marcaMod['nombre'])) . ' ya existe para el tipo de equipo ' . $this->marcaMod['tipoEquipo'] . '. Pero tiene estatus NO DISPONIBLE. Intenta con otro nombre.');
        }
        else
        {    
            $this->guardaMarcaEnTabla();

            session()->flash('success', 'La MARCA se ha guardado correctamente.');

            $this->guardoMarcaOK = true;
        }
    }

    public function guardaMarcaEnTablaActualizar()
    {
        // Buscar la marca existente en la base de datos
        $marcaEquipo = MarcaEquipo::findOrFail($this->marcaMod['id']);

        // Actualizar los campos necesarios
        $marcaEquipo->id_tipo_equipo = $this->marcaMod['idTipoEquipo'];
        $marcaEquipo->nombre = trim(mb_strtoupper($this->marcaMod['nombre']));
        $marcaEquipo->disponible = 1;

        // Guardar los cambios
        $marcaEquipo->save();

        session()->flash('success', 'La MARCA se ha actualizado correctamente.');
    }

    public function marcaYaExisteActualizar($idTipoEquipo, $nombreMarca, $idMarca)
    {
        $marca = MarcaEquipo::where('id_tipo_equipo', $idTipoEquipo)->where('nombre', $nombreMarca)->first();

        if (is_null($marca))
        {
            return 0;
        }
        else
        {
            if ($idMarca != $marca->id)
            {
                $this->marcaMod['tipoEquipo'] = $marca->tipoEquipo->nombre;
                return $marca->disponible ? 1 : 2;  //Si la marca está disponible regresa 1 si no regresa 2
            }
            else
            {
                return 0;
            }
        }
    }

    public function actualizaMarca()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'marcaMod.nombre' => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'marcaMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'marcaMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'marcaMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        $estatusMarca = $this->marcaYaExisteActualizar($this->marcaMod['idTipoEquipo'], trim(mb_strtoupper($this->marcaMod['nombre'])), $this->marcaMod['id']);

        if ($estatusMarca == 1)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->marcaMod['nombre'])) . ' ya existe para el tipo de equipo ' . $this->marcaMod['tipoEquipo'] . '. Intenta con otro nombre.');

        }
        elseif ($estatusMarca == 2)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->marcaMod['nombre'])) . ' ya existe para el tipo de equipo ' . $this->marcaMod['tipoEquipo'] . '. Pero tiene estatus NO DISPONIBLE. Intenta con otro nombre.');
        }
        else
        {    
            $this->guardaMarcaEnTablaActualizar();
        }
    }

    public function editaMarca($idMarca)
    {
        $marca = MarcaEquipo::findOrFail($idMarca);

        $this->marcaMod = [
            'id' => $marca->id,
            'idTipoEquipo' => $marca->id_tipo_equipo,
            'nombre' => $marca->nombre
        ];

        $this->dispatch('abrirEditarMarcaModal', $this->marcaMod['idTipoEquipo']);
    }

    public function invertirEstatusMarca($idMarca)
    {
        $marca = MarcaEquipo::findOrFail($idMarca);

        $marca->disponible = !$marca->disponible;
        $marca->save();
    }  
}
