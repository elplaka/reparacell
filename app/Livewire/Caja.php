<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\EstatusEquipo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class Caja extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $codigoProductoCapturado;
    public $cantidadProductoCapturado, $cantidadProductosCarrito;
    public $carrito, $totalCarrito, $totalCarritoDescuento;
    public $showMainErrors, $showModalErrors;
    public $clientesModal, $nombreClienteModal, $usuariosModal;
    public $descripcionProductoModal;

    public $corteCaja = [
        'fechaInicial',
        'fechaFinal',
        'cajero',
        'idUsuario'
    ];

    protected $listeners = [
        'f4-pressed' => 'cobrar',
        'f9-pressed' => 'abrirCaja', 
        'f10-pressed' => 'abrirCorteCaja', 
    ];    

    public $cliente = [
        'id',
        'nombre',              
        'telefono',  
        'direccion',
        'telefonoContacto',
        'publicoGeneral'    
    ];

    public $producto = [
        'codigo',
        'descripcion',
        'precioCosto',
        'departamento'
    ];

    public function abrirCorteCaja()
    {

    }

    public function irCorteCaja()
    {
        Session::put('corteCaja', $this->corteCaja);

        $this->dispatch('abrirPestanaCorteCaja');
    }

    function formatearFecha($fecha) {
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO',
            4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO',
            7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
    
        $fechaFormateada = \DateTime::createFromFormat('Y-m-d', $fecha)->format('d - ');
    
        $numeroMes = \DateTime::createFromFormat('Y-m-d', $fecha)->format('n');
        $nombreMes = $meses[$numeroMes];
    
        $fechaFormateada .= strtoupper($nombreMes) . ' - ' . \DateTime::createFromFormat('Y-m-d', $fecha)->format('Y');
    
        return $fechaFormateada;
    }
    
    public function generaCorteCajaPDF()
    {
        $this->corteCaja = Session::get('corteCaja');
        $cajeroSeleccionado = false;

        if ($this->corteCaja['idUsuario'] != 0)
        {
            $this->corteCaja['cajero'] = User::findOrFail($this->corteCaja['idUsuario']);
            $cajeroSeleccionado = true;
        }

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL DÍA :: ' .  $this->formatearFecha($this->corteCaja['fechaInicial']);
        }
        else
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL ' .  $this->formatearFecha($this->corteCaja['fechaInicial']) . ' AL ' . $this->formatearFecha($this->corteCaja['fechaFinal']);
        }

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            if ($cajeroSeleccionado)
            {
                $ventasCorteCaja = Venta::whereDate('created_at', '=', $this->corteCaja['fechaInicial'])->where('id_usuario', $this->corteCaja['idUsuario'])
                ->get();
            }
            else
            {
                $ventasCorteCaja = Venta::whereDate('created_at', '=', $this->corteCaja['fechaInicial'])
                ->get();
            }
        }
        else
        {
            if ($cajeroSeleccionado)
            {
                $ventasCorteCaja = Venta::whereDate('created_at', '>=', $this->corteCaja['fechaInicial'])
                ->whereDate('created_at', '<=', $this->corteCaja['fechaFinal'])
                ->get();
            }
            else
            {
                $ventasCorteCaja = Venta::whereDate('created_at', '>=', $this->corteCaja['fechaInicial'])
                ->whereDate('created_at', '<=', $this->corteCaja['fechaFinal'])->where('id_usuario', $this->corteCaja['idUsuario'])
                ->get();
            }
        }

        $pdf = SnappyPdf::loadView('livewire.corte-caja', ['corteCaja' => $this->corteCaja, 'ventas' => $ventasCorteCaja])
        ->setOption('page-size', 'Letter')
        ->setOption('margin-top', 30)
        ->setOption('header-html', view('livewire.pdf.encabezado', compact('tituloCorteCaja'))->render())
        ->setOption('header-spacing', 5)
        ->setOption('footer-center', 'Página [page] de [topage]')
        // ->setOption('footer-right', $this->corteCaja['cajero'])
        ->setOption('footer-font-size', '8')
        ->setOption('footer-font-name', 'Montserrat');


        return $pdf->stream('test.pdf');
    }
    

    // return redirect('/test-pdf');


    public function cierraCorteCajaModal()
    {

    }

    public function abrirCaja()
    {
        dd('abre caja');

        $printer_name = "Ticket";
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->pulse();
        $printer->close();
    }

    public function cierraBuscarProductoModal()
    {

    }

    public function hazDescuento($descuento)
    {
        $this->totalCarritoDescuento = $this->totalCarrito * (1 - ($descuento/100));
    }

    public function gotoPageAndCapture($codigo, $page)
    {
        $this->gotoPage($page);
        $this->capturarFilaBuscarProducto($codigo);
    }
    
    public function capturarFilaBuscarProducto($codigoProducto)   //Selecciona un cliente de la tabla de buscar clientes
    {
        $producto = Producto::where('codigo', 'LIKE', $codigoProducto)
        ->whereRaw('LOWER(codigo) = LOWER(?)', [$codigoProducto])
        ->first();

        $this->producto['codigo'] = $codigoProducto;
        $this->producto['descripcion'] = $producto->descripcion; 
        $this->producto['precioCosto'] = $producto->precio_costo;
        $this->producto['departamento'] = $producto->departamento->nombre;

        $this->dispatch('cerrarModalBuscarProducto');

        $this->codigoProductoCapturado = $this->producto['codigo'];
        $this->descripcionProductoModal = '';
        $this->agregaProducto();
    }

    public function regresaCliente($telefono)
    {
        $cliente = Cliente::where('telefono', $telefono)->first();

        $this->cliente['id'] = $cliente->id;
        $this->cliente['nombre'] = $cliente->nombre;
        $this->cliente['telefono'] = $cliente->telefono;
        $this->cliente['direccion'] = $cliente->direccion;
        $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;
        $this->cliente['publicoGeneral'] = $telefono == '0000000000' ? 1 : 0;
    }

    public function cierraBuscarClienteModal()
    {
        
    }

    public function cierraModalBuscarCliente()
    {

    }

    public function updatedNombreClienteModal($value)
    {
        if (strlen($value) == 0) $this->clientesModal = null;
        else $this->clientesModal = Cliente::where('nombre', 'like', '%' . $value . '%')
        ->where('telefono', '!=', '0000000000')
        ->get();

        if (!is_null($this->clientesModal) && $this->clientesModal->count() == 0) 
        {
            $this->clientesModal = null;
        }
    }

    public function capturarFila($clienteId)   //Selecciona un cliente de la tabla de buscar clientes
    {
        $this->cliente['id'] = $clienteId;

        $cliente = Cliente::findOrFail($clienteId);
        $this->cliente['estatus'] = 2;  //Cliente solo lectura
        $this->cliente['telefono'] = $cliente->telefono;
        $this->cliente['nombre'] = $cliente->nombre;
        $this->cliente['direccion'] = $cliente->direccion;
        $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;

        $this->dispatch('cerrarModalBuscarCliente');

        $this->cliente['publicoGeneral'] = false;
    }

    public function mount()
    {
        $this->carrito = collect(); // Inicializa $carrito como una colección vacía

        $this->usuariosModal = User::all();

        $this->cliente = [
            'id'                => "0000000000",
            'estatus'           => 2,
            'nombre'            => '',
            'telefono'          => '',
            'direccion'         => '',
            'telefonoContacto' => '',
            'publicoGeneral'    => true,
        ];

        $this->corteCaja = [
            'fechaInicial' => now()->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'cajero' => Auth::user()->name,
            'idUsuario' => 0
        ];

        $this->showModalErrors = false;
        $this->showMainErrors = !$this->showModalErrors;

        $this->nombreClienteModal = '';

        $cliente = $this->regresacliente('0000000000');
    }

    public function render()
    {
        $productosModal = null;
  
        if (strlen($this->descripcionProductoModal) > 0)
        {
            $productosModal = Producto::where('descripcion', 'like', '%' . $this->descripcionProductoModal . '%')
            ->where('disponible', '=', 1)
            ->orderBy('descripcion')
            ->paginate(10);

            $this->setPage(1);
        }

        return view('livewire.caja', compact('productosModal'));
    }

    public function agregaProducto()
    {
        $this->cantidadProductoCapturado = 1;
        $producto = Producto::whereRaw("TRIM(codigo) = ?", trim($this->codigoProductoCapturado))->first();

        if ($producto)
        {
            $cantidadSolicitada = $this->cantidadProductoCapturado;
            $inventarioActual = $producto->inventario;
            $inventarioDisponible = $inventarioActual - $cantidadSolicitada; 

            if ($inventarioDisponible >= 0)
            {
                $this->agregaAlCarrito($producto);
            }
            else
            {
                $this->codigoProductoCapturado = '';
                $this->dispatch('mostrarToastErrorRouteInventario', 'El producto ' . $producto->descripcion . ' no tiene inventario.', '<i class="fa-solid fa-boxes-stacked"></i> Ir a inventario');
            }
        }
        else
        {
            $this->dispatch('mostrarToastError', 'Código no existente. Intenta con otro!!!');
        }
    }

    public function updatedCarrito($value, $key)
    {
        if (substr($key, -8) === 'cantidad')
        {
            // Obtener el índice del carrito
            $index = (int) strtok($key, '.');
            $producto = $this->carrito[$index]['producto'];

            if ($value > 0)  //El valor de la CANTIDAD siempre debe ser mayor que 0
            {
                // Realizar acciones adicionales específicas para la cantidad
                // Por ejemplo, recalcular el subtotal
                $inventarioActual = $producto->inventario;
                $inventarioDisponible = $inventarioActual - $value; 

                if ($inventarioDisponible >= 0)
                {
                    $subTotal = $producto->precio_venta * $value;

                    // Actualizar el subTotal utilizando transform
                    $this->carrito->transform(function ($item, $key) use ($index, $subTotal, $value) {
                        if ($key === $index) {
                            $item['subTotal'] = number_format($subTotal, 2, '.', ',');
                            $item['cantidadVieja'] = $value;
                        }
                        return $item;
                    });

                    $this->cuentaCantidadProductosCarrito();
                }
                else
                {
                    $cantidadVieja = $this->carrito[$index]['cantidadVieja'];
                    $this->carrito->transform(function ($item, $key) use ($index, $cantidadVieja) {
                        if ($key === $index) {
                            $item['cantidad'] = $cantidadVieja;
                        }
                        return $item;
                    });

                    $this->dispatch('mostrarToastErrorRouteInventario', 'El producto ' . $producto->descripcion . ' no tiene suficiente inventario.', '<i class="fa-solid fa-boxes-stacked"></i> Ir a inventario');
                }
            }
            else //Si la CANTIDAD no es mayor que 0
            {
                if ($value != '')  //Si no está en blanco
                {
                    $subTotal = $producto->precio_venta;  //El SUBTOTAL se inicializa al valor del precio de venta porque la cantidad que se pone por default es 1

                    $this->carrito->transform(function ($item, $key) use ($index, $subTotal) {
                        if ($key === $index) {
                            $item['cantidad'] = 1;
                            $item['subTotal'] = number_format($subTotal, 2, '.', ',');
                        }
                        return $item;
                    });

                    $this->cuentaCantidadProductosCarrito();
                }
            }
        }
    }

    public function eliminaDelCarrito($index)
    {
        $this->carrito->forget($index);
        $this->carrito = $this->carrito->values();
        $this->cuentaCantidadProductosCarrito();
    }

    public function agregaAlCarrito($producto)
    {
        if ($producto) {
            // Verificar si el producto ya está en el carrito
            $productoEnCarrito = collect($this->carrito)->first(function ($item) use ($producto) {
                return $item['producto']->codigo === $producto->codigo;
            });

            // Si el producto no está en el carrito, agrégalo
            if (!$productoEnCarrito) {
                $subTotal = $producto->precio_venta * $this->cantidadProductoCapturado;

                $item = [
                    'producto' => $producto,
                    'cantidad' => $this->cantidadProductoCapturado,
                    'subTotal' => number_format($subTotal, 2, '.', ','),
                    'cantidadVieja' => $this->cantidadProductoCapturado
                ];

                $this->carrito->push($item);                
            }
        }
        $this->cuentaCantidadProductosCarrito();
        $this->reset('codigoProductoCapturado');
    }

    public function cuentaCantidadProductosCarrito()
    {
        $cant_productos = 0;
        $total_carrito = 0;
        
        foreach ($this->carrito as $item)
        {
            $cant_productos += $item['cantidad'];
            $subTotalFloat = floatval(str_replace(',', '', $item['subTotal']));
            $total_carrito += $subTotalFloat;
        }
        $this->cantidadProductosCarrito = $cant_productos;
        $this->totalCarrito = $total_carrito;
        $this->totalCarritoDescuento = $total_carrito; 
    }

    public function restaInventario($codigoProducto, $cantidad)
    {
        $producto = Producto::where('codigo', $codigoProducto)->first();

        $inventarioRestante = $producto->inventario - $cantidad;
        $producto->inventario = $inventarioRestante;

        $producto->save();
    }

    public function cobrar()
    {
        if ($this->cantidadProductosCarrito)
        {
            try 
            {
                DB::transaction(function ()
                {
                    $this->totalCarrito = $this->totalCarritoDescuento;
                    $venta = Venta::create([
                        'id_cliente' => $this->cliente['id'],
                        'total' => $this->totalCarrito 
                    ]);

                    foreach ($this->carrito as $item)
                    {
                        $codigoProducto = $item['producto']->codigo;
                        $cantidad = $item['cantidad'];
                        $this->restaInventario($codigoProducto, $cantidad);

                        $subTotal = $item['producto']->precio_venta * $cantidad;

                        $venta->detalles()->createMany([
                            [
                            'codigo_producto' => $item['producto']->codigo,
                            'cantidad' => $cantidad,
                            'importe' => $subTotal
                            ],
                        ]);
                    }

                    $this->carrito = collect(); // Inicializa $carrito como una colección vacía
                    $this->cantidadProductosCarrito = 0;
                    $cliente = $this->regresacliente('0000000000');

                    $this->dispatch('mostrarToast', 'Venta realizada con éxito!!!');
                });
            } catch (\Exception $e)
            {
                    // Manejo de errores si ocurre una excepción
                    // Puedes agregar logs o notificaciones aquí
                    dd($e);
            }
        }
    }
}
