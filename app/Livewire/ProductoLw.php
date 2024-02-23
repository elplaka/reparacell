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
            
            $this->goToPage(1);

            $productos = $productosQuery->orderBy('descripcion')->paginate(10);
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

        try {
            $this->validate();

            if ($this->yaExisteCodigoProducto($this->productoMod['codigo']))
            {
                $this->addError('productoMod.codigo', 'El Código del producto ya existe. Intenta con otro.');
                return;
            }

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

            $this->showModalErrors = false;
            $this->showMainErrors = true;

            session()->flash('success', 'El PRODUCTO se ha agregado correctamente.');

            $this->resetModal();

            $this->dispatch('cerrarModalNuevoProducto');
        }
        catch (\Exception $e)
        {
            dd($e->getMessage()); // Muestra el mensaje de error en caso de una excepción
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

    }

    public function actualizaProducto()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        try {
            $this->validate();

            $producto = Producto::findOrFail($this->productoMod['codigo']);
            $producto->descripcion = trim(mb_strtoupper($this->productoMod['descripcion']));
            $producto->precio_costo = $this->productoMod['precioCosto'];
            $producto->precio_venta = $this->productoMod['precioVenta'];
            $producto->precio_mayoreo = $this->productoMod['precioMayoreo'];
            $producto->inventario = $this->productoMod['inventario'];
            $producto->inventario_minimo = $this->productoMod['inventarioMinimo'];
            $producto->id_departamento = $this->productoMod['idDepartamento'];
            $producto->save();
    
            $this->showModalErrors = false;
            $this->showMainErrors = true;
    
            session()->flash('success', 'El PRODUCTO se ha actualizado correctamente.');
    
            $this->resetModal();
    
            $this->dispatch('cerrarModalEditarProducto');
        } catch (\Exception $e)
        {
            dd($e->getMessage()); // Muestra el mensaje de error en caso de una excepción
        }

    
    }

    public function invertirEstatusProducto($codigoProducto)
    {
        $producto = Producto::findOrFail($codigoProducto);

        $producto->disponible = !$producto->disponible;
        $producto->save();
    } 
}
