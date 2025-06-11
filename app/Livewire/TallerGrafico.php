<?php
namespace App\Livewire;

use Livewire\Component;

class TallerGrafico extends Component
{
    public $labels = [];
    public $valores = [];

    public function mount()
    {
        // Datos de ejemplo (puedes obtenerlos de la BD)
        $this->labels = ['Enero', 'Febrero', 'Marzo'];
        $this->valores = [100, 200, 150];
    }

    public function render()
    {
        return view('livewire.taller.graficos');
    }
}
