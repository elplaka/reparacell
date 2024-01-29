<?php

namespace App\Livewire;

use Livewire\Component;

class MarcaEquipo extends Component
{
    public $marca = [
        'idTipoEquipo',
        'nombre'
    ];


    public function render()
    {
        return view('livewire.marca-equipo');
    }

    public function mount()
    {
        $this->marca = [
            'idTipoEquipo'    => 1,
            'nombre'            => ''
        ];
    }
}
