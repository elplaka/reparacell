<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TipoEquipo;

class EquipoTipos extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $showMainErrors, $showModalErrors, $guardoTipoEquipoOK;
    public $nuevoTipo;

    public $tipoEquipoMod = [
        'id',
        'nombre',
        'cveNombre',
        'icono'
    ];

    public function invertirEstatusTipo($idTipo)
    {
        $tipo = TipoEquipo::findOrFail($idTipo);

        $tipo->disponible = !$tipo->disponible;
        $tipo->save();
    }

    public function mount()
    {
        $this->tipoEquipoMod = [
            'id' => null,
            'nombre' => '',
            'cveNombre' => '',
            'icono' => ''
        ];
    }

    public function abreAgregaTipo()
    {
        $this->nuevoTipo = true;
    }

    public function cierraTipoEqModal()
    {

    }

    public function guardaTipoEquipo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'tipoEquipoMod.nombre' => ['required', 'string', 'max:15', 'min:1'],
            'tipoEquipoMod.cveNombre' => ['required', 'string', 'max:3', 'min:1'],
            'tipoEquipoMod.icono' => ['required', 'regex:/^<i class="fa-solid (fa-[a-z-]+)"><\/i>$/']

        ], [
            'tipoEquipoMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'tipoEquipoMod.nombre.max' => 'El nombre no puede tener más de 15 caracteres.',
            'tipoEquipoMod.cveNombre.required' => 'El campo <b> Cve. Nombre </b> es obligatorio.',
            'tipoEquipoMod.cveNombre.max' => 'La Cve. Nombre no puede tener más de 15 caracteres.',
            'tipoEquipoMod.icono.required' => 'El campo <b> Ícono </b> es obligatorio.',
            'tipoEquipoMod.icono.regex' => 'El campo <b> Ícono </b> contiene información incorrecta.',
        ]);

        $tipoEquipo = new TipoEquipo();
        $tipoEquipo->nombre = trim(mb_strtoupper($this->tipoEquipoMod['nombre']));
        $tipoEquipo->cve_nombre = trim(mb_strtoupper($this->tipoEquipoMod['cveNombre']));
        $tipoEquipo->icono = $this->tipoEquipoMod['icono'];
        $tipoEquipo->disponible = 1;
        $tipoEquipo->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        $this->guardoTipoEquipoOK = true;

        $this->dispatch('cerrarModalTipoEquipo');

        session()->flash('success', 'El TIPO DE EQUIPO se ha guardado correctamente.');
    }

    public function editaTipo($idTipo)
    {
        $tipoEquipo = TipoEquipo::findOrFail($idTipo);

        $this->tipoEquipoMod['id'] = $tipoEquipo->id;
        $this->tipoEquipoMod['nombre'] = $tipoEquipo->nombre;
        $this->tipoEquipoMod['cveNombre'] = $tipoEquipo->cve_nombre;
        $this->tipoEquipoMod['icono'] = $tipoEquipo->icono;

        $this->nuevoTipo = false;
    }

    public function actualizaTipoEquipo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;
        
        $tipoEquipo = TipoEquipo::findOrFail($this->tipoEquipoMod['id']);

        $tipoEquipo->nombre = trim(mb_strtoupper($this->tipoEquipoMod['nombre']));
        $tipoEquipo->cve_nombre = trim(mb_strtoupper($this->tipoEquipoMod['cveNombre']));
        $tipoEquipo->icono = $this->tipoEquipoMod['icono'];
        $tipoEquipo->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        $this->dispatch('cerrarModalTipoEquipo');

        session()->flash('success', 'El TIPO DE EQUIPO se ha actualizado correctamente.');
    }

    public function render()
    {
        $tipos = collect();

        $tipos = TipoEquipo::paginate(10);
        $this->goToPage(1);

        return view('livewire.equipos.tipos', compact('tipos'));
    }
}
