<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\TipoMovimientoInventario;
use Livewire\WithPagination;

class ProductosReportes extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;

    public $reporte = [
        'tipo' => null,
        'inventarioMaximo' => null,
        'inventarioMinimo' => null,
        'tipoMovimiento' => ''
    ];

    public function render()
    {
        $productos = null;
        $tiposMovimientos = TipoMovimientoInventario::all();

        if ($this->reporte['tipo'] == 1)
        {
            $productos = Producto::where('inventario', '>=', $this->reporte['inventarioMinimo'])->where('disponible', 1)->paginate(10);
        }
        else if ($this->reporte['tipo'] == 2)
        {
            $productos = Producto::where('inventario', '<=', $this->reporte['inventarioMaximo'])->where('disponible', 1)->paginate(10);
        }

        $this->dispatch('contentChanged');
        
        return view('livewire.productos.reportes', compact('productos', 'tiposMovimientos'));

    }

    public function mount()
    {
        $this->reporte = [
            'tipo' => 0,
            'inventarioMaximo' => 1,
            'inventarioMinimo' => 1,
            'tipoMovimiento' => '0'
        ];
    }
}
