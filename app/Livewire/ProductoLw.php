<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\Producto;
use App\Models\DepartamentoProducto;
use App\Models\MovimientoInventario;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ProductoLw extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $filtrosProductos = [
        'codigo',
        'descripcion',
        'idDepartamento',
        'disponible'
    ];

    public $productoMod = [
        'codigo',
        'descripcion',
        'precioCosto',
        'precioVenta',
        'precioMayoreo',
        'idDepartamento',
        'inventario',
        'inventarioMinimo'
    ];

    public $inventarioMod = [
        'codigo',
        'descripcion',
        'precioCosto',
        'precioVenta',
        'precioMayoreo',
        'existencia',
        'existenciaMinima'
    ];

    public $departamentos;
    public $showMainErrors, $showModalErrors;

    protected $rules = [
        'productoMod.codigo' => 'required|string',
        'productoMod.descripcion' => 'required|string',
        'productoMod.precioCosto' => 'required|numeric|min:0',
        'productoMod.precioVenta' => 'required|numeric|min:0',
        'productoMod.precioMayoreo' => 'required|numeric|min:0',
        'productoMod.idDepartamento' => 'required|exists:departamentos_productos,id',
        'productoMod.inventario' => 'required|integer|min:0',
        'productoMod.inventarioMinimo' => 'required|integer|min:0',

        'inventarioMod.codigo' => 'required|string',
        'inventarioMod.precioCosto' => 'required|numeric|min:0',
        'inventarioMod.precioVenta' => 'required|numeric|min:0',
        'inventarioMod.precioMayoreo' => 'required|numeric|min:0',
        'inventarioMod.existencia' => 'required|integer|min:0',
        'inventarioMod.existenciaMinima' => 'required|integer|min:0',

    ];

    protected $messages = [
        'productoMod.codigo.required' => 'El campo Código es obligatorio.',
        'productoMod.descripcion.required' => 'El campo Descripción es obligatorio.',
        'productoMod.precioCosto.required' => 'El campo Precio Costo es obligatorio.',
        'productoMod.precioCosto.numeric' => 'El campo Precio Costo debe ser un número.',
        'productoMod.precioCosto.min' => 'El campo Precio Costo debe ser mayor o igual a :min.',
        'productoMod.precioVenta.numeric' => 'El campo Precio Venta debe ser un número.',
        'productoMod.precioVenta.min' => 'El campo Precio Venta debe ser mayor o igual a :min.',
        'productoMod.precioMayoreo.numeric' => 'El campo Precio Mayoreo debe ser un número.',
        'productoMod.precioMayoreo.min' => 'El campo Precio Mayoreo debe ser mayor o igual a :min.',
        'productoMod.idDepartamento.exists' => 'Debes seleccionar un Departamento válido.',
        'productoMod.inventario.numeric' => 'El campo Inventario debe ser un número.',
        'productoMod.inventario.min' => 'El campo Inventario debe ser mayor o igual a :min.',
        'productoMod.inventarioMinimo.numeric' => 'El campo Inventario Mínimo debe ser un número.',
        'productoMod.inventarioMinimo.min' => 'El campo Inventario Mínimo debe ser mayor o igual a :min.',

        'inventarioMod.codigo.required' => 'El campo Código es obligatorio.',
        'inventarioMod.descripcion.required' => 'El campo Descripción es obligatorio.',
        'inventarioMod.precioCosto.required' => 'El campo Precio Costo es obligatorio.',
        'inventarioMod.precioCosto.numeric' => 'El campo Precio Costo debe ser un número.',
        'inventarioMod.precioCosto.min' => 'El campo Precio Costo debe ser mayor o igual a :min.',
        'inventarioMod.precioVenta.numeric' => 'El campo Precio Venta debe ser un número.',
        'inventarioMod.precioVenta.min' => 'El campo Precio Venta debe ser mayor o igual a :min.',
        'inventarioMod.precioMayoreo.numeric' => 'El campo Precio Mayoreo debe ser un número.',
        'inventarioMod.precioMayoreo.min' => 'El campo Precio Mayoreo debe ser mayor o igual a :min.',
        'inventarioMod.existencia.numeric' => 'El campo Inventario debe ser un número.',
        'inventarioMod.existencia.min' => 'El campo Inventario debe ser mayor o igual a :min.',
        'inventarioMod.existenciaMinima.numeric' => 'El campo Inventario Mínimo debe ser un número.',
        'inventarioMod.existenciaMinima.min' => 'El campo Inventario Mínimo debe ser mayor o igual a :min.',
    ];


    public function mount()
    {
              
        $this->resetModal();

        $this->filtrosProductos = [
            'codigo' => '',
            'descripcion' => '',
            'idDepartamento' => [],
            'disponible' => -1
        ];

        $this->departamentos = DepartamentoProducto::orderBy('nombre')->get();
    }

    public function render()
    {
        $productos = collect();

        if ($this->filtrosProductos['codigo'] == '' && $this->filtrosProductos['descripcion'] == '' && $this->filtrosProductos['idDepartamento'] == [] && $this->filtrosProductos['disponible'] == -1)
        {
            $productos = Producto::orderBy('descripcion')->paginate(10);

            $this->gotoPage($productos->currentPage());
        }
        else
        {
            $productosQuery = Producto::query();

            if ($this->filtrosProductos['codigo'] != '') {
                $productosQuery->where('codigo', 'like', '%'.  $this->filtrosProductos['codigo'] . '%');
            }

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
        $this->departamentos = DepartamentoProducto::where('disponible', 1)->get();
    }

    public function cierraNuevoProductoModal()
    {
        $this->resetModal();
    }

    public function yaExisteCodigoProducto($codigoProducto)
    {
        $producto = Producto::find($codigoProducto);
        return $producto ? true : false;
    }

    public function guardaProducto()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->inventarioMod = [
            'codigo' => '0',
            'descripcion' => '_INVENTARIO_',
            'precioCosto' => 0,
            'precioVenta' => 0,
            'precioMayoreo' => 0,
            'existencia' => 0,
            'existenciaMinima' => 0
        ];

        $this->validate();

        if ($this->yaExisteCodigoProducto($this->productoMod['codigo']))
        {
            $this->addError('productoMod.codigo', 'El Código del producto ya existe. Intenta con otro.');
            return;
        }

        DB::beginTransaction();

        try {
            $producto = new Producto();
            $producto->codigo = trim(mb_strtoupper($this->productoMod['codigo']));
            $producto->descripcion = trim(mb_strtoupper($this->productoMod['descripcion']));
            $producto->precio_costo = $this->productoMod['precioCosto'];
            $producto->precio_venta = $this->productoMod['precioVenta'];
            $producto->precio_mayoreo = $this->productoMod['precioMayoreo'];
            $producto->inventario = $this->productoMod['inventario'];
            $producto->inventario_minimo = $this->productoMod['inventarioMinimo'];
            $producto->id_departamento = $this->productoMod['idDepartamento'];
            $producto->disponible = 1;
            $producto->save();

            $movimiento = new MovimientoInventario();
            $movimiento->id_tipo_movimiento = 1;
            $movimiento->codigo_producto = $this->productoMod['codigo'];
            $movimiento->existencia_anterior = 0;
            $movimiento->existencia_movimiento = $this->productoMod['inventario'];
            $movimiento->existencia_minima_anterior = 0;
            $movimiento->existencia_minima_movimiento = $this->productoMod['inventarioMinimo'];
            $movimiento->precio_costo_anterior = 0;
            $movimiento->precio_costo_movimiento = $this->productoMod['precioCosto'];
            $movimiento->precio_venta_anterior = 0;
            $movimiento->precio_venta_movimiento = $this->productoMod['precioVenta'];
            $movimiento->precio_mayoreo_anterior = 0;
            $movimiento->precio_mayoreo_movimiento = $this->productoMod['precioMayoreo'];
            $movimiento->save();

            DB::commit();

            $this->showModalErrors = false;
            $this->showMainErrors = true;
    
            session()->flash('success', 'El PRODUCTO se ha agregado correctamente.');
    
            $this->resetModal();
    
            $this->dispatch('cerrarModalNuevoProducto');
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e->getMessage());
            // Manejar la excepción según sea necesario
        }
    }

    public function resetModal()
    {
        $this->productoMod = [
            'codigo' => '',
            'descripcion' => '',
            'precioCosto' => 0,
            'precioVenta' => 0,
            'precioMayoreo' => 0,
            'idDepartamento' => 1,
            'inventario' => 0,
            'inventarioMinimo' => 0
        ];

        $this->inventarioMod = [
            'codigo' => '',
            'descripcion' => '',
            'precioCosto' => 0,
            'precioVenta' => 0,
            'precioMayoreo' => 0,
            'existencia' => 0,
            'existenciaMinima' => 0
        ];

        $this->resetValidation();
    }

    public function editaProducto($codigoProducto)
    {

        $producto = Producto::findOrFail($codigoProducto);

        $this->productoMod = [
            'codigo' => $producto->codigo,
            'descripcion' => $producto->descripcion,
            'precioCosto' => $producto->precio_costo,
            'precioVenta' => $producto->precio_venta,
            'precioMayoreo' => $producto->precio_mayoreo,
            'idDepartamento' => $producto->id_departamento,
            'inventario' => $producto->inventario,
            'inventarioMinimo' => $producto->inventario_minimo
        ];
    }

    public function cierraEditarProductoModal()
    {
        $this->resetModal();
    }

    public function actualizaProducto()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate();

        $producto = Producto::findOrFail($this->productoMod['codigo']);
        $producto->descripcion = trim(mb_strtoupper($this->productoMod['descripcion']));
        $producto->id_departamento = $this->productoMod['idDepartamento'];
        $producto->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        session()->flash('success', 'El PRODUCTO se ha actualizado correctamente.');

        $this->resetModal();

        $this->dispatch('cerrarModalEditarProducto');
    }

    public function invertirEstatusProducto($codigoProducto)
    {
        $producto = Producto::findOrFail($codigoProducto);

        $producto->disponible = !$producto->disponible;
        $producto->save();
    } 

    public function modificaInventario($codigoProducto)
    {
        $producto = Producto::findOrFail($codigoProducto);

        $this->inventarioMod = [
            'codigo' => $producto->codigo,
            'descripcion' => $producto->descripcion,
            'precioCosto' => $producto->precio_costo,
            'precioVenta' => $producto->precio_venta,
            'precioMayoreo' => $producto->precio_mayoreo,
            'existencia' => $producto->inventario,
            'existenciaMinima' => $producto->inventario_minimo
        ];

    }

    public function actualizaInventario()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->productoMod = [
            'codigo' => '0',
            'descripcion' => '_INVENTARIO_',
            'precioCosto' => 0,
            'precioVenta' => 0,
            'precioMayoreo' => 0,
            'idDepartamento' => 1,
            'inventario' => 0,
            'inventarioMinimo' => 0
        ];

        $this->validate();

        DB::beginTransaction();

        try {
            $producto = Producto::findOrFail($this->inventarioMod['codigo']);
        
            $movimiento = new MovimientoInventario();
            $movimiento->id_tipo_movimiento = 3;
            $movimiento->codigo_producto = $this->inventarioMod['codigo'];
            $movimiento->existencia_anterior = $producto->inventario;
            $movimiento->existencia_movimiento = $this->inventarioMod['existencia'];
            $movimiento->existencia_minima_anterior = $producto->inventario_minimo;
            $movimiento->existencia_minima_movimiento = $this->inventarioMod['existenciaMinima'];
            $movimiento->precio_costo_anterior = $producto->precio_costo;
            $movimiento->precio_costo_movimiento = $this->inventarioMod['precioCosto'];
            $movimiento->precio_venta_anterior = $producto->precio_venta;
            $movimiento->precio_venta_movimiento = $this->inventarioMod['precioVenta'];
            $movimiento->precio_mayoreo_anterior = $producto->precio_mayoreo;
            $movimiento->precio_mayoreo_movimiento = $this->inventarioMod['precioMayoreo'];
            $movimiento->save();
        
            $producto->precio_costo = $this->inventarioMod['precioCosto'];
            $producto->precio_venta = $this->inventarioMod['precioVenta'];
            $producto->precio_mayoreo = $this->inventarioMod['precioMayoreo'];
            $producto->inventario = $this->inventarioMod['existencia'];
            $producto->inventario_minimo = $this->inventarioMod['existenciaMinima'];
            $producto->save();
        
            DB::commit();

            $this->showModalErrors = false;
            $this->showMainErrors = true;
    
            session()->flash('success', 'El INVENTARIO DEL PRODUCTO se ha actualizado correctamente.');
    
            $this->resetModal();
    
            $this->dispatch('cerrarModalModificarInventario');
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e->getMessage());
            // Manejar la excepción según sea necesario
        }

   
    }

    public function cierraModificarInventarioModal()
    {
        $this->resetModal();
    }
}
