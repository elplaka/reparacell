<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEquipo;
use App\Models\FallaEquipo;
use Livewire\WithPagination;
use Illuminate\Validation\Rule as ValidationRule;

class EquipoFallas extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $fallaEquipo = [
        'id',
        'idTipoEquipo',
        'descripcion',
        'cveDescripcion',
        'disponible'
    ];

    public $fallaMod = [
        'id',
        'idTipoEquipo',
        'descripcion',
        'cveDescripcion',
        'costo'
    ];

    public $filtrosFallas =
    [
        'idTipoEquipo',
        'disponible',
        'descripcion'
    ];

    public $tipos_equipos;
    public $showMainErrors, $showModalErrors;
    public $guardoFallaOK;

    public function render()
    {
        $fallas = collect();
        if ($this->filtrosFallas['idTipoEquipo'] == 0 && $this->filtrosFallas['disponible'] == -1 && $this->filtrosFallas['descripcion'] == '')
        {
            $fallas = FallaEquipo::paginate(10);

            $this->gotoPage($fallas->currentPage());
        }
        else
        {
            $fallasQuery = FallaEquipo::query();

            if ($this->filtrosFallas['idTipoEquipo'] != 0) {
                $fallasQuery->where('id_tipo_equipo', $this->filtrosFallas['idTipoEquipo']);
            }
            
            if ($this->filtrosFallas['disponible'] != -1) {
                $fallasQuery->where('disponible', $this->filtrosFallas['disponible']);
            }

            if ($this->filtrosFallas['descripcion'] != '') {
                $fallasQuery->where('descripcion', 'like', '%' . $this->filtrosFallas['descripcion'] . '%');
            }
            
            $fallas = $fallasQuery->paginate(10);
            $this->goToPage(1);
        }

        return view('livewire.equipos.fallas', compact('fallas'));
    }

    public function mount()
    {
        $this->fallaEquipo = [
            'id' => '',
            'idTipoEquipo' => 1,
            'descripcion' => '',
            'cveDescripcion' => '',
            'disponible' => 1
        ];

        $this->filtrosFallas = [
            'idTipoEquipo' => 0,
            'disponible' => -1,
            'descripcion' => ''
        ];

        $this->fallaMod = [
            'idTipoEquipo' => 1,
            'descripcion' => '',
            'cveDescripcion' => '',
            'costo' => ''
        ];

        $this->tipos_equipos = TipoEquipo::all();
    }
    
    public function invertirEstatusFalla($idFalla)
    {
        $falla = FallaEquipo::findOrFail($idFalla);

        $falla->disponible = !$falla->disponible;
        $falla->save();
    }   

    public function abreAgregaFalla()
    {

    }

    public function cierraFallaModal()
    {
        $this->fallaMod = [
            'id' => null,
            'idTipoEquipo' => null,
            'descripcion' => null,
            'cveDescripcion' => null,
            'costo' => null
        ];
    }

        // public function gotoPageAndCapture($codigo, $page)
    // {
    //     $this->gotoPage($page);
    //     $this->capturarFilaBuscarProducto($codigo);
    // }

    public function editaFalla($idFalla)
    {
        $falla = FallaEquipo::findOrFail($idFalla);

        $this->fallaMod = [
            'id' => $idFalla,
            'idTipoEquipo' => $falla->id_tipo_equipo,
            'descripcion' => $falla->descripcion,
            'cveDescripcion' => $falla->cve_descripcion,
            'costo' => $falla->costo
        ];

        $this->dispatch('abrirEditarFallaModal', $this->fallaMod['idTipoEquipo']);

    }

    public function guardaFalla()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'fallaMod.descripcion'     => ['required','string','min:1', 'max:30'],
            'fallaMod.cveDescripcion'  => ['required', 'string', 'min:1', 'max:6', ValidationRule::unique('fallas_equipos', 'cve_descripcion')->where('id_tipo_equipo', $this->fallaMod['idTipoEquipo'])],
            'fallaMod.costo'           => ['required','numeric']
        ], [
            'fallaMod.descripcion.required' => 'El campo <b> Descripción </b> es obligatorio.',
            'fallaMod.descripcion.max' => 'La <b> Descripción </b> no puede tener más de 30 caracteres.',
            'fallaMod.descripcion.min' => 'La <b> Descripción </b> debe tener al menos 1 caracter.',
            'fallaMod.cveDescripcion.required' => 'El campo <b> Clave de Descripción </b> es obligatorio.',
            'fallaMod.cveDescripcion.max' => 'La <b> Clave de Descripción </b> no puede tener más de 6 caracteres.',
            'fallaMod.cveDescripcion.min' => 'La <b> Clave de Descripción </b> debe tener al menos 1 caracter.',
            'fallaMod.cveDescripcion.unique' => 'La <b>Clave de Descripción</b> ya está en uso para este tipo de equipo.',
            'fallaMod.costo.required' => 'El campo <b>Costo</b> es obligatorio.',
            'fallaMod.costo.required' => 'El campo <b> Costo </b> es obligatorio.',
            'fallaMod.costo.numeric' => 'El campo <b> Costo </b> debe ser numérico.'
        ]);

        $fallaEquipo = new FallaEquipo();
        $fallaEquipo->id_tipo_equipo = $this->fallaMod['idTipoEquipo'];
        $fallaEquipo->descripcion = trim(mb_strtoupper($this->fallaMod['descripcion']));
        $fallaEquipo->cve_descripcion = trim(mb_strtoupper($this->fallaMod['cveDescripcion']));
        $fallaEquipo->costo = $this->fallaMod['costo'];
        $fallaEquipo->disponible = 1;
        $fallaEquipo->save();

        session()->flash('success', 'La FALLA se ha guardado correctamente.');

        $this->guardoFallaOK = true;
    }

    public function actualizaFalla()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'fallaMod.descripcion'     => ['required','string','min:1', 'max:30'],
            'fallaMod.cveDescripcion'  => [
                'required',
                'string',
                'min:1',
                'max:6',
                ValidationRule::unique('fallas_equipos', 'cve_descripcion')
                    ->ignore($this->fallaMod['id'], 'id')
                    ->where('id_tipo_equipo', $this->fallaMod['idTipoEquipo'])
            ],
            'fallaMod.costo'           => ['required','numeric']
        ], [
            'fallaMod.descripcion.required' => 'El campo <b> Descripción </b> es obligatorio.',
            'fallaMod.descripcion.max' => 'La <b> Descripción </b> no puede tener más de 30 caracteres.',
            'fallaMod.descripcion.min' => 'La <b> Descripción </b> debe tener al menos 1 caracter.',
            'fallaMod.cveDescripcion.required' => 'El campo <b> Clave de Descripción </b> es obligatorio.',
            'fallaMod.cveDescripcion.max' => 'La <b> Clave de Descripción </b> no puede tener más de 6 caracteres.',
            'fallaMod.cveDescripcion.min' => 'La <b> Clave de Descripción </b> debe tener al menos 1 caracter.',
            'fallaMod.cveDescripcion.unique' => 'La <b>Clave de Descripción</b> ya está en uso para este tipo de equipo.',
            'fallaMod.costo.required' => 'El campo <b>Costo</b> es obligatorio.',
            'fallaMod.costo.required' => 'El campo <b> Costo </b> es obligatorio.',
            'fallaMod.costo.numeric' => 'El campo <b> Costo </b> debe ser numérico.'
        ]);

        // Buscar la falla existente en la base de datos
        $fallaEquipo = FallaEquipo::find($this->fallaMod['id']);

        if (!$fallaEquipo) {
            // Manejar el caso donde la falla no existe
            // Puedes lanzar una excepción, redireccionar o mostrar un mensaje de error
            return;
        }

        // Actualizar los atributos de la falla
        $fallaEquipo->id_tipo_equipo = $this->fallaMod['idTipoEquipo'];
        $fallaEquipo->descripcion = trim(mb_strtoupper($this->fallaMod['descripcion']));
        $fallaEquipo->cve_descripcion = trim(mb_strtoupper($this->fallaMod['cveDescripcion']));
        $fallaEquipo->costo = $this->fallaMod['costo'];
        $fallaEquipo->save();

        session()->flash('success', 'La FALLA se ha actualizado correctamente.');

    }

}
