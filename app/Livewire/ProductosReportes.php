<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\MovimientoInventario;
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
        'tipoMovimiento' => 0,
        'fechaMovmientoInicio' => null,
        'fechaMovimientoFin' => null,
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
        else if ($this->reporte['tipo'] == 3)
        {
            $productos = MovimientoInventario::query();

            $fechaInicio = date('Y-m-d', strtotime($this->reporte['fechaMovimientoInicio']));
            $fechaFin = date('Y-m-d', strtotime($this->reporte['fechaMovimientoFin']));
    
            if ($fechaInicio == $fechaFin)
            {
                $productos->whereDate('created_at', '=', $fechaInicio);
            }
            else
            {
                $productos->whereDate('created_at', '>=', $fechaInicio)
                            ->whereDate('created_at', '<=', $fechaFin);
            }

            if ($this->reporte['tipoMovimiento'] == 0)
            {
                $productos = $productos->paginate(10);
            }
            else
            {
                $productos = $productos->where('id_tipo_movimiento', $this->reporte['tipoMovimiento'])->paginate(10);
            }

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
            'tipoMovimiento' => '0',
            'fechaMovimientoInicio' => now()->subDays(30)->toDateString(),
            'fechaMovimientoFin' => now()->toDateString(),
        ];
    }
}
