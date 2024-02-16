<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use Livewire\WithPagination;

class EquipoModelos extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $modelo = [
        'idTipoEquipo',
        'nombre'
    ];

    public $filtrosModelos =
    [
        'idMarca',
        'idTipoEquipo',
        'disponible',
        'nombre'
    ];

    public $marcaMod = [
        'idMarca'
    ];

    public $modeloMod = [
        'id',
        'idMarca',
        'idTipoEquipo',
        'nombre'
    ];

    public $tipos_equipos, $marcas_equipos;
    public $marcas;
    public $showMainErrors, $showModalErrors;
    public $guardoModeloOK;

    public function render()
    {
        $modelos = collect();
        if ($this->filtrosModelos['idTipoEquipo'] == 0 && $this->filtrosModelos['disponible'] == -1 && $this->filtrosModelos['nombre'] == '')
        {
            $modelos = ModeloEquipo::orderBy('nombre')->paginate(10);
        }
        else
        {
            $modelosQuery = ModeloEquipo::query();

            if ($this->filtrosModelos['idTipoEquipo'] != 0) {
                $this->marcas = MarcaEquipo::where('id_tipo_equipo', $this->filtrosModelos['idTipoEquipo'])->orderBy('nombre')->get();
                $modelosQuery->whereHas('marca', function ($query) {
                    $query->where('id_tipo_equipo', $this->filtrosModelos['idTipoEquipo']);
                });
            }

            if ($this->filtrosModelos['idMarca'] != 0) {
                $modelosQuery->where('id_marca', $this->filtrosModelos['idMarca']);
            }
            
            if ($this->filtrosModelos['disponible'] != -1) {
                $modelosQuery->where('disponible', $this->filtrosModelos['disponible']);
            }

            if ($this->filtrosModelos['nombre'] != '') {
                $modelosQuery->where('nombre', 'like', '%' . $this->filtrosModelos['nombre'] . '%');
            }

            $this->goToPage(1);
            $modelos = $modelosQuery->orderBy('nombre')->paginate(10);
        }

        return view('livewire.equipos.modelos', compact('modelos'));
    }

    public function mount()
    {
        $this->modelo = [
            'idTipoEquipo'    => 1,
            'nombre'            => ''
        ];

        $this->filtrosModelos = [
            'idMarca' => 0,
            'idTipoEquipo' => 0,
            'disponible' => -1,
            'nombre' => ''
        ];

        $this->marcaMod = [
            'idTipoEquipo' => 1,
        ];

        $this->modeloMod = [
            'id' => '',
            'idMarca' => 0,
            'idTipoEquipo' => 1,
            'nombre' => ''
        ];

        $this->tipos_equipos = TipoEquipo::where('disponible', 1)->get();
        $this->marcas_equipos = MarcaEquipo::where('id_tipo_equipo', $this->marcaMod['idTipoEquipo'])->where('disponible',1)->orderBy('nombre')->get();

        // $this->marcas_equipos = collect();

        $this->marcas = collect();
    }

    public function updatedMarcaModIdTipoEquipo()
    {
        $this->marcas_equipos = MarcaEquipo::where('id_tipo_equipo', $this->marcaMod['idTipoEquipo'])->where('disponible',1)->orderBy('nombre')->get();
    }

    public function abreAgregaModelo()
    {
        $this->marcas_equipos = MarcaEquipo::where('id_tipo_equipo', $this->marcaMod['idTipoEquipo'])->where('disponible',1)->orderBy('nombre')->get();
    }

    public function cierraModeloModal()
    {
        $this->marcaMod = [
            'idTipoEquipo' => 1,
            'nombre' => null
        ];
    }

    public function guardaModelo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'modeloMod.idMarca' => 'required|not_in:null',
            'modeloMod.nombre'      => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'modeloMod.idMarca.not_in' => 'Por favor selecciona una <b> Marca </b>',
            'modeloMod.idMarca.required' => 'El campo <b> Marca </b> es obligatorio.',
            'modeloMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'modeloMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'modeloMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        $modeloEquipo = new ModeloEquipo();
        $modeloEquipo->id_marca = $this->modeloMod['idMarca'];
        $modeloEquipo->nombre = trim(mb_strtoupper($this->modeloMod['nombre']));
        $modeloEquipo->disponible = 1;
        $modeloEquipo->save();

        session()->flash('success', 'El MODELO se ha guardado correctamente.');

        $this->guardoModeloOK = true;
    }

    public function actualizaModelo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;
        
        $this->validate([
            'modeloMod.idMarca' => 'required|not_in:null',
            'modeloMod.nombre' => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'modeloMod.idMarca.not_in' => 'Por favor selecciona una <b> Marca </b>',
            'modeloMod.idMarca.required' => 'El campo <b> Marca </b> es obligatorio.',
            'modeloMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'modeloMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'modeloMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        // Buscar el modelo existente o crear uno nuevo
        $modeloEquipo = ModeloEquipo::findOrFail($this->modeloMod['id']);

        // Si ya existe, simplemente actualiza los campos necesarios
        if ($modeloEquipo->exists) {
            $modeloEquipo->update([
                'id_marca' => $this->modeloMod['idMarca'],
                'nombre' => trim(mb_strtoupper($this->modeloMod['nombre'])),
            ]);
        } 
        
        session()->flash('success', 'El MODELO se ha actualizado correctamente.');
    }

    public function editaModelo($idModelo)
    {

        $modelo = ModeloEquipo::findOrFail($idModelo);

        $this->marcaMod = [
            'idTipoEquipo' => $modelo->marca->id_tipo_equipo,
        ];

       $this->modeloMod = [
            'id' => $modelo->id,
            'idMarca' => $modelo->id_marca,
            'nombre' => $modelo->nombre,
       ]; 

       $this->marcas_equipos = MarcaEquipo::where('id_tipo_equipo', $this->marcaMod['idTipoEquipo'])->where('disponible',1)->orderBy('nombre')->get();

        $this->dispatch('abrirEditarModeloModal', $this->marcaMod['idTipoEquipo']);
    }

    public function invertirEstatusModelo($idModelo)
    {
        $modelo = ModeloEquipo::findOrFail($idModelo);

        $modelo->disponible = !$modelo->disponible;
        $modelo->save();
    }  
}
