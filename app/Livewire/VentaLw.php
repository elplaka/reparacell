<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\User;
use Livewire\WithPagination;

class VentaLw extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $collapsed = [];
    public $usuarios;

    public $filtrosVentas = [
        'fechaInicial',
        'fechaFinal',
        'cliente',
        'idUsuario'
    ];


    public function render()
    {
        $ventasQuery = Venta::query();

        $ventasQuery->whereDate('created_at', '>=', $this->filtrosVentas['fechaInicial'])
        ->whereDate('created_at', '<=', $this->filtrosVentas['fechaFinal']);

        if ($this->filtrosVentas['cliente'] != '')
        {
            $nombreCliente = $this->filtrosVentas['cliente'];
            $ventasQuery->whereHas('cliente', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'LIKE', "%$nombreCliente%");
            });
        }

        if ($this->filtrosVentas['idUsuario'] != 0)
        {
            $ventasQuery->where('id_usuario', $this->filtrosVentas['idUsuario']);
        }

        $ventas = $ventasQuery->paginate(10);

        return view('livewire.ventas.index', compact('ventas'));
    }

    public function updatedPage($page)
    {
        // Runs after the page is updated for this component...
        $this->collapsed = [];
    }

    public function mount()
    {
        $this->collapsed = [];

        $this->filtrosVentas = [
            'fechaInicial' => now()->subDays(7)->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'cliente' => '',
            'idUsuario' => 0
        ];

        $this->usuarios = User::all();
    }

    public function verDetalles($ventaId)
    {
        if (isset($this->collapsed[$ventaId]))
        {
            unset($this->collapsed[$ventaId]);
        } 
        else 
        {
            $this->collapsed[$ventaId] = true;
        }
    }
}
