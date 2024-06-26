<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DepartamentoProducto;

class Departamento extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $showMainErrors, $showModalErrors;
    public $nuevoDepartamento;

    public $departamentoMod = [
        'id',
        'nombre',
    ];

    public function invertirEstatusDepartamento($idDepartamento)
    {
        $departamento = DepartamentoProducto::findOrFail($idDepartamento);

        $departamento->disponible = !$departamento->disponible;
        $departamento->save();
    }

    public function mount()
    {
        $this->departamentoMod = [
            'id' => null,
            'nombre' => '',
        ];
    }

    public function render()
    {
        $departamentos = collect();
        $departamentos = DepartamentoProducto::where('id', '>', 1)->orderBy('nombre')->paginate(10);
        $this->goToPage(1);

        return view('livewire.productos.departamentos', compact('departamentos'));
    }

    public function abreAgregaDepartamento()
    {
        $this->nuevoDepartamento = true;
    }

    public function cierraDepartamentoModal()
    {

    } 

    public function guardaDepartamento()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'departamentoMod.nombre' => ['required', 'string', 'max:30', 'min:1'],
        ], [
            'departamentoMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'departamentoMod.nombre.max' => 'El nombre no puede tener más de 30 caracteres.',
        ]);

        $departamento = new DepartamentoProducto();
        $departamento->nombre = trim(mb_strtoupper($this->departamentoMod['nombre']));
        $departamento->disponible = 1;
        $departamento->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        $this->dispatch('cerrarModalDepartamento');

        session()->flash('success', 'El DEPARTAMENTO se ha guardado correctamente.');
    }

    public function editaDepartamento($idDepartamento)
    {
        $departamento = DepartamentoProducto::findOrFail($idDepartamento);

        $this->departamentoMod['id'] = $departamento->id;
        $this->departamentoMod['nombre'] = $departamento->nombre;

        $this->nuevoDepartamento = false;
    }

    public function actualizaDepartamento()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;
        
        $departamento = DepartamentoProducto::findOrFail($this->departamentoMod['id']);

        $departamento->nombre = trim(mb_strtoupper($this->departamentoMod['nombre']));
        $departamento->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        $this->dispatch('cerrarModalDepartamento');

        session()->flash('success', 'El DEPARTAMENTO se ha actualizado correctamente.');
    }
}
