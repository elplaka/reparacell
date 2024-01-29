<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\EstatusEquipo;
use Illuminate\Support\Facades\DB;

class Caja extends Component
{
    public $codigoProductoCapturado;
    public $cantidadProductoCapturado, $cantidadProductosCarrito;
    public $carrito, $totalCarrito;
    public $showMainErrors, $showModalErrors;
    public $clientesModal, $nombreClienteModal;


    protected $listeners = ['f2-pressed' => 'cobrar'];

    public $cliente = [
        'id',
        'nombre',              
        'telefono',  
        'direccion',
        'telefonoContacto',
        'publicoGeneral'    
    ];

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

        $this->cliente = [
            'id'                => "0000000000",
            'estatus'           => 2,
            'nombre'            => '',
            'telefono'          => '',
            'direccion'         => '',
            'telefonoContacto' => '',
            'publicoGeneral'    => true,
        ];

        $this->showModalErrors = false;
        $this->showMainErrors = !$this->showModalErrors;

        $this->nombreClienteModal = '';

        $cliente = $this->regresacliente('0000000000');
    }

    public function render()
    {
        return view('livewire.caja');
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

            if ($inventarioDisponible > 0)
            {
                $this->agregaAlCarrito($producto);
            }
            else
            {
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

                $this->reset('codigoProductoCapturado');
            }
        }
        $this->cuentaCantidadProductosCarrito();
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

                    $venta = Venta::create([
                        'id_cliente' => $this->cliente['id'],
                        'fecha' => now(),
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
