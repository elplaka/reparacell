<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\Producto;
use App\Models\DepartamentoProducto;
use Livewire\WithPagination;

class ProductoLw extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $filtrosProductos = [
        'descripcion',
        'idDepartamento',
        'disponible'
    ];

    public $departamentos;

    public function mount()
    {
        $this->filtrosProductos = [
            'descripcion' => '',
            'idDepartamento' => [],
            'disponible' => -1
        ];

        $this->departamentos = DepartamentoProducto::orderBy('nombre')->get();

    }

    public function render()
    {
        $productos = collect();
        if ($this->filtrosProductos['descripcion'] == ''  && $this->filtrosProductos['idDepartamento'] == 0)
        {
            $productos = Producto::orderBy('descripcion')->paginate(10);

            $this->gotoPage($productos->currentPage());
        }
        else
        {
            $productosQuery = Producto::query();

            if ($this->filtrosProductos['descripcion'] != '') {
                $productosQuery->where('descripcion', 'like', '%'.  $this->filtrosProductos['descripcion'] . '%');
            }

            if ($this->filtrosProductos['idDepartamento'] != []) {
                $productosQuery->whereIn('id_departamento', $this->filtrosProductos['idDepartamento']);
            }

            if ($this->filtrosProductos['disponible'] != -1) {
                $productosQuery->where('disponible', $this->filtrosProductos['disponible']);
            }

            $productos = $productosQuery->orderBy('descripcion')->paginate(10);
            $this->goToPage(1);
        }

        return view('livewire.productos.index', compact('productos'));
    }

    public function abreAgregaProducto()
    {

    }

    public function cierraNuevoProductoModal()
    {
        
    }
}
