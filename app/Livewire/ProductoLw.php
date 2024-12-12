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
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse; 
use Illuminate\Support\Facades\Schema;

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
        'departamento',
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

    public $departamentos, $codigoRepetido;
    public $showMainErrors, $showModalErrors;
    public $datosCargados, $productoConInventario;

    protected $baseRules = [
        'productoMod.codigo' => 'required|string',
        'productoMod.descripcion' => 'required|string',
        'productoMod.precioCosto' => 'required|numeric|min:0',
        'productoMod.precioVenta' => 'required|numeric|min:0',
        'productoMod.precioMayoreo' => 'required|numeric|min:0',
        'productoMod.idDepartamento' => 'required|exists:departamentos_productos,id',
        // 'productoMod.inventario' => 'required|integer|min:0',
        // 'productoMod.inventarioMinimo' => 'required|integer|min:0',

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

    public function rules()
    {
        $rules = $this->baseRules;

        if ($this->productoMod['inventario'] != -1) {
            $rules['productoMod.inventario'] = 'required|integer|min:0';
            $rules['productoMod.inventarioMinimo'] = 'required|integer|min:0';
        }

        return $rules;
    }


    public function mount()
    {
              
        $this->resetModal();

        $this->filtrosProductos = [
            'codigo' => '',
            'descripcion' => '',
            'idDepartamento' => [],
            'disponible' => -1
        ];

        $this->departamentos = DepartamentoProducto::where('disponible', 1)->orderBy('nombre')->get();
    }

    public function render()
    {
        // if ($this->filtrosProductos['codigo'] == '' && $this->filtrosProductos['descripcion'] == '' && $this->filtrosProductos['idDepartamento'] == [] && $this->filtrosProductos['disponible'] == -1)
        // {
        //     $productos = Producto::orderBy('descripcion')->paginate(10);

        //     $this->gotoPage($productos->currentPage());
        // }
        // else
        // {
        //     $productosQuery = Producto::query();

        //     if ($this->filtrosProductos['codigo'] != '') {
        //         $productosQuery->where('codigo', 'like', '%'.  $this->filtrosProductos['codigo'] . '%');
        //     }

        //     if ($this->filtrosProductos['descripcion'] != '') {
        //         $productosQuery->where('descripcion', 'like', '%'.  $this->filtrosProductos['descripcion'] . '%');
        //     }

        //     if ($this->filtrosProductos['idDepartamento'] != []) {
        //         $productosQuery->whereIn('id_departamento', $this->filtrosProductos['idDepartamento']);
        //     }

        //     if ($this->filtrosProductos['disponible'] != -1) {
        //         $productosQuery->where('disponible', $this->filtrosProductos['disponible']);
        //     }

        //     $productos = $productosQuery->orderBy('descripcion')->paginate(10);
        //     $this->goToPage(1);
        // }

        $productos = collect();

        if ($this->filtrosProductos['codigo'] == '' && $this->filtrosProductos['descripcion'] == '' && $this->filtrosProductos['idDepartamento'] == [] && $this->filtrosProductos['disponible'] == -1)
        {
            $productos = Producto::whereNotIn('codigo', ['COM01', 'COM02', 'COM03', 'COM04', 'COM05', 'COM06', 'COM07', 'COM08', 'COM09'])
                                ->orderBy('descripcion')
                                ->paginate(10);

            $this->gotoPage($productos->currentPage());
        }
        else
        {
            $productosQuery = Producto::query();

            $productosQuery->whereNotIn('codigo', ['COM01', 'COM02', 'COM03', 'COM04', 'COM05', 'COM06', 'COM07', 'COM08', 'COM09']);

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

    public function updatedProductoModCodigo()
    {
        $this->codigoRepetido = Producto::where('codigo', $this->productoMod['codigo'])->exists();
    }

    public function abreAgregaProducto()
    {
        $this->productoConInventario = true;
        $this->departamentos = DepartamentoProducto::where('disponible', 1)->orderBy('nombre')->get();
        $this->datosCargados = true;
    }

    public function cierraNuevoProductoModal()
    {
        $this->resetModal();
        $this->datosCargados = false;
    }

    public function yaExisteCodigoProducto($codigoProducto)
    {
        $producto = Producto::find($codigoProducto);
        return $producto ? true : false;
    }

    public function productoYaExiste($idDepartamento, $descripcion)
    {
        $producto = Producto::where('id_departamento', $idDepartamento)->where('descripcion', $descripcion)->first();

        if (is_null($producto))
        {
            return 0;   //Para indicar que el nombre del producto no existe
        }
        else
        {
          $this->productoMod['departamento'] = $producto->departamento->nombre;
          return $producto->disponible ? 1 : 2;  //Si el producto está disponible regresa 1 si no regresa 2
        }
    }

    public function esCodigoReservado($codigoProducto)
    {
        // Lista de códigos reservados
        $codigosReservados = array("0", "COM01", "COM02", "COM03", "COM04", "COM05", "COM06", "COM07", "COM08", "COM09");
    
        // Verificar si el código está en la lista de códigos reservados
        return in_array($codigoProducto, $codigosReservados);
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

        if ($this->esCodigoReservado($this->productoMod['codigo']))
        {
            $this->addError('productoMod.codigo', 'El Código del producto está reservado para el sistema. Intenta con otro.');
            return;
        }

        $estatusProducto = $this->productoYaExiste($this->productoMod['idDepartamento'], trim(mb_strtoupper($this->productoMod['descripcion'])));

        if ($estatusProducto == 1)
        {
            $this->dispatch('mostrarToastError', 'El producto ' . trim(mb_strtoupper($this->productoMod['descripcion'])) . ' ya existe para el departamento ' . $this->productoMod['departamento'] . '. Intenta con otra descripción.');

        }
        elseif ($estatusProducto == 2)
        {
            $this->dispatch('mostrarToastError', 'El producto ' . trim(mb_strtoupper($this->productoMod['descripcion'])) . ' ya existe para el departamento ' . $this->productoMod['departamento'] . '. Pero tiene estatus NO DISPONIBLE. Intenta con otra descripción.');
        }
        else
        {    
            DB::beginTransaction();

            try {
                $producto = new Producto();
                $producto->codigo = trim(mb_strtoupper($this->productoMod['codigo']));
                $producto->descripcion = trim(mb_strtoupper($this->productoMod['descripcion']));
                $producto->precio_costo = $this->productoMod['precioCosto'];
                $producto->precio_venta = $this->productoMod['precioVenta'];
                $producto->precio_mayoreo = $this->productoMod['precioMayoreo'];
                $producto->inventario = $this->productoConInventario ? $this->productoMod['inventario'] : -1;
                $producto->inventario_minimo = $this->productoConInventario ? $this->productoMod['inventarioMinimo'] : -1;
                $producto->id_departamento = $this->productoMod['idDepartamento'];
                $producto->disponible = 1;
                $producto->save();
    
                $movimiento = new MovimientoInventario();
                $movimiento->id_tipo_movimiento = 1;
                $movimiento->codigo_producto = $this->productoMod['codigo'];
                $movimiento->existencia_anterior = 0;
                $movimiento->existencia_movimiento = $producto->inventario;
                $movimiento->existencia_minima_anterior = 0;
                $movimiento->existencia_minima_movimiento = $producto->inventario_minimo;
                $movimiento->precio_costo_anterior = 0;
                $movimiento->precio_costo_movimiento = $this->productoMod['precioCosto'];
                $movimiento->precio_venta_anterior = 0;
                $movimiento->precio_venta_movimiento = $this->productoMod['precioVenta'];
                $movimiento->precio_mayoreo_anterior = 0;
                $movimiento->precio_mayoreo_movimiento = $this->productoMod['precioMayoreo'];
                $movimiento->id_usuario_movimiento = Auth::id();
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

    public function productoYaExisteActualizar($idDepartamento, $descripcion, $codigo)
    {
        $producto = Producto::where('id_departamento', $idDepartamento)->where('descripcion', $descripcion)->first();

        if (is_null($producto))
        {
            return 0;
        }
        else
        {
            if ($codigo != $producto->codigo)
            {
                $this->productoMod['departamento'] = $producto->departamento->nombre;
                return $producto->disponible ? 1 : 2;  //Si el producto está disponible regresa 1 si no regresa 2
            }
            else
            {
                return 0;
            }
        }
    }

    public function actualizaProducto()
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

        $estatusProducto = $this->productoYaExisteActualizar($this->productoMod['idDepartamento'], trim(mb_strtoupper($this->productoMod['descripcion'])), $this->productoMod['codigo']);

        if ($estatusProducto == 1)
        {
            $this->dispatch('mostrarToastError', 'El producto ' . trim(mb_strtoupper($this->productoMod['descripcion'])) . ' ya existe para el departamento ' . $this->productoMod['departamento'] . '. Intenta con otra descripción.');

        }
        elseif ($estatusProducto == 2)
        {
            $this->dispatch('mostrarToastError', 'El producto ' . trim(mb_strtoupper($this->productoMod['descripcion'])) . ' ya existe para el departamento ' . $this->productoMod['departamento'] . '. Pero tiene estatus NO DISPONIBLE. Intenta con otra descripción.');
        }
        else
        {    
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
    }

    public function invertirEstatusProducto($codigoProducto)
    {
        $producto = Producto::findOrFail($codigoProducto);

        $activacion = $producto->disponible ? true : false; 

        DB::beginTransaction();

        try {
            $producto->disponible = !$producto->disponible;
            $producto->save();
            
            $movimiento = new MovimientoInventario();
            $movimiento->id_tipo_movimiento = $activacion ? 2 : 4;
            $movimiento->codigo_producto = $producto->codigo;
            $movimiento->existencia_anterior = $producto->inventario;
            $movimiento->existencia_movimiento = $producto->inventario;
            $movimiento->existencia_minima_anterior = $producto->inventario_minimo;
            $movimiento->existencia_minima_movimiento = $producto->inventario_minimo;
            $movimiento->precio_costo_anterior = $producto->precio_costo;
            $movimiento->precio_costo_movimiento = $producto->precio_costo;
            $movimiento->precio_venta_anterior = $producto->precio_venta;
            $movimiento->precio_venta_movimiento = $producto->precio_venta;
            $movimiento->precio_mayoreo_anterior = $producto->precio_mayoreo;
            $movimiento->precio_mayoreo_movimiento = $producto->precio_mayoreo;
            $movimiento->id_usuario_movimiento = Auth::id();
            $movimiento->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e->getMessage());
            // Manejar la excepción según sea necesario
        }
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

        $this->productoConInventario = $producto->inventario != -1 ? true : false;
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
            'inventario' => 1,
            'inventarioMinimo' => 1
        ];

        if (!$this->productoConInventario)
        {
            $this->inventarioMod['existencia'] = 10256;
            $this->inventarioMod['existenciaMinima'] = 10256;
        } 

        $this->validate();

        DB::beginTransaction();

        try {
            $producto = Producto::findOrFail($this->inventarioMod['codigo']);
        
            $movimiento = new MovimientoInventario();
            $movimiento->id_tipo_movimiento = 3;
            $movimiento->codigo_producto = $this->inventarioMod['codigo'];
            $movimiento->existencia_anterior = $producto->inventario;
            $movimiento->existencia_movimiento = $this->productoConInventario ? $this->inventarioMod['existencia'] : -1;
            $movimiento->existencia_minima_anterior = $producto->inventario_minimo;
            $movimiento->existencia_minima_movimiento = $this->productoConInventario ? $this->inventarioMod['existenciaMinima'] : -1;
            $movimiento->precio_costo_anterior = $producto->precio_costo;
            $movimiento->precio_costo_movimiento = $this->inventarioMod['precioCosto'];
            $movimiento->precio_venta_anterior = $producto->precio_venta;
            $movimiento->precio_venta_movimiento = $this->inventarioMod['precioVenta'];
            $movimiento->precio_mayoreo_anterior = $producto->precio_mayoreo;
            $movimiento->precio_mayoreo_movimiento = $this->inventarioMod['precioMayoreo'];
            $movimiento->id_usuario_movimiento = Auth::id();
            $movimiento->save();
        
            $producto->precio_costo = $this->inventarioMod['precioCosto'];
            $producto->precio_venta = $this->inventarioMod['precioVenta'];
            $producto->precio_mayoreo = $this->inventarioMod['precioMayoreo'];
            $producto->inventario = $this->productoConInventario ? $this->inventarioMod['existencia'] : -1;
            $producto->inventario_minimo = $this->productoConInventario ? $this->inventarioMod['existenciaMinima'] : -1;
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

    public function exportarCsv()
{
    $productos = Producto::all();
    $nombreArchivo = 'productos.csv';

    // Obtener nombres de columnas dinámicamente
    $columnas = Schema::getColumnListing((new Producto)->getTable());

    $callback = function() use ($productos, $columnas) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columnas);

        foreach ($productos as $producto) {
            $fila = [];
            foreach ($columnas as $columna) {
                $valor = $producto->{$columna};

                // Convertir los valores numéricos largos a texto
                if (is_numeric($valor) && strlen($valor) > 10) {
                    $valor = "\t" . $valor;
                }

                $fila[] = $valor;
            }
            fputcsv($file, $fila);
        }

        fclose($file);
    };

    return new StreamedResponse($callback, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
    ]);
}

}
