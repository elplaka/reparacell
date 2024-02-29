<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\WithPagination;

class ClienteHistorial extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;

    public function render()
    {
        $this->goToPage(1);

        $clientes = Cliente::paginate(10);

        return view('livewire.clientes.historial', compact('clientes'));
    }
}
