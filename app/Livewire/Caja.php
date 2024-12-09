<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\CobroTaller;
use App\Models\CobroTallerCredito;
use App\Models\VentaDetalle;
use App\Models\EstatusEquipo;
use App\Models\VentaCredito;
use App\Models\VentaCreditoDetalle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    public $muestraDivAbono, $detallesCredito, $datosCargados;
    public $sumaAbonos, $montoLiquidar;
    public $consecutivoComun, $cantidadProductoComun, $descripcionProductoComun, $precioProductoComun, $montoProductoComun;

    public $corteCaja = [
        'fechaInicial',
        'fechaFinal',
        'cajero',
        'idUsuario',
        'chkCobrosTaller',
        'chkAbonos'
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

    public $ventaCredito = 
    [
        'nombreCliente' => null,
        'id' => null,
        'idEstatus' => null,
        'estatus' => null,
        'monto' => null,
        'abono' => 0,
        'idAbonoSeleccionado' => null
    ];

    // Reglas de validación
    protected $rules = [
        'cantidadProductoComun' => 'required|integer|min:1',
        'descripcionProductoComun' => 'required|string|min:1',
        'montoProductoComun' => 'required|numeric|min:0.01',
        // Agrega más reglas aquí para otros campos de la ventana principal
    ];

    // Mensajes de error personalizados
    protected $messages = [
        'cantidadProductoComun.required' => 'La CANTIDAD es obligatoria.',
        'cantidadProductoComun.integer' => 'La CANTIDAD debe ser un número entero.',
        'cantidadProductoComun.min' => 'La CANTIDAD debe ser al menos 1.',
        'descripcionProductoComun.required' => 'La DESCRIPCIÓN es obligatoria.',
        'descripcionProductoComun.min' => 'La DESCRIPCIÓN no puede estar vacía.',
        'montoProductoComun.required' => 'El IMPORTE es obligatorio.',
        'montoProductoComun.numeric' => 'El IMPORTE debe ser un número.',
        'montoProductoComun.min' => 'El IMPORTE debe ser mayor que 0.',
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

        $fechaInicial = Carbon::parse($this->corteCaja['fechaInicial'])->startOfDay();
        $fechaFinal = Carbon::parse($this->corteCaja['fechaFinal'])->endOfDay();

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL DÍA :: ' .  $this->formatearFecha($this->corteCaja['fechaInicial']);
        }
        else
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL ' .  $this->formatearFecha($this->corteCaja['fechaInicial']) . ' AL ' . $this->formatearFecha($this->corteCaja['fechaFinal']);
        }

        $cajeroSeleccionado = $this->corteCaja['idUsuario'] != 0 ? true : false ;


        if ($this->corteCaja['chkAbonos'])
        {
            $ventas = Venta::with([
                'cliente',
                'usuario',
                'ventaCredito.ventaCreditoDetalles' => function ($query) use ($fechaInicial, $fechaFinal) {
                    $query->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                          ->where('abono', '>', 0);
                },
            ])
            ->when($cajeroSeleccionado, function ($query) {
                return $query->where('id_usuario', $this->corteCaja['idUsuario']);
            })
            ->where('cancelada', 0)
            ->where(function($query) use ($fechaInicial, $fechaFinal) {
                // Condición para ventas sin VentaCredito
                $query->whereDoesntHave('ventaCredito')
                      ->whereBetween('created_at', [$fechaInicial, $fechaFinal])            
                      // Condición para ventas con VentaCredito que tienen detalles válidos
                      ->orWhereHas('ventaCredito.ventaCreditoDetalles', function ($query) use ($fechaInicial, $fechaFinal) {
                          $query->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->where('abono', '>', 0);
                      });
            })
            ->orderBy('created_at')
            ->get()
            ->flatMap(function ($venta) {
                $resultado = collect();
            
                // Si no hay VentaCredito, incluir la venta
                if (!$venta->ventaCredito) {
                    $resultado->push([
                        'id' => $venta->id,
                        'created_at' => $venta->created_at,
                        'nombre_cliente' => $venta->cliente->nombre,
                        'monto' => $venta->total,
                        'cajero' => $venta->usuario->name,
                        'tipo' => 'VENTA',
                    ]);
                }
            
                // Si hay VentaCredito, incluir únicamente los detalles válidos
                if ($venta->ventaCredito) {
                    $venta->ventaCredito->ventaCreditoDetalles
                        ->each(function ($detalle) use ($resultado, $venta) {
                            $resultado->push([
                                'id' => $detalle->id,
                                'created_at' => $detalle->created_at,
                                'nombre_cliente' => $venta->cliente->nombre,
                                'monto' => $detalle->abono,
                                'cajero' => $detalle->usuario->name ?? 'N/A',
                                'tipo' => 'ABONO_VENTA',
                            ]);
                        });
                }
                return $resultado;
            });
        }
        else
        {
            $ventas = Venta::with('cliente')
                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                ->when($cajeroSeleccionado, function ($query) {
                    return $query->where('id_usuario', $this->corteCaja['idUsuario']);
                })
                ->where('cancelada', 0)
                ->where(function($query) {
                    $query->whereDoesntHave('ventaCredito')
                          ->orWhereHas('ventaCredito', function ($query) {
                              $query->where('id_estatus', 2);
                          });
                })
                ->get()
                ->map(function($venta) {
                    return [
                        'id' => $venta->id,
                        'created_at' => $venta->created_at,
                        'nombre_cliente' => $venta->cliente->nombre,
                        'monto' => $venta->total,
                        'cajero' => $venta->usuario->name,
                        'tipo' => 'VENTA'
                    ];
                });
        }

        // Inicializar $cobrosTaller como una colección vacía 
        $cobrosTaller = collect();

        if ($this->corteCaja['chkCobrosTaller'])
        { 
            if ($this->corteCaja['chkAbonos'])
            {                
                // 1. Obtener los detalles de CobroTallerCredito
                $cobrosTallerCredito = CobroTallerCredito::with([
                    'detalles' => function ($query) use ($fechaInicial, $fechaFinal) {
                        $query->where('abono', '>', 0)
                            ->whereBetween('created_at', [$fechaInicial, $fechaFinal]);
                    }
                ])
                ->whereHas('detalles', function ($query) use ($fechaInicial, $fechaFinal, $cajeroSeleccionado) {
                    $query->where('abono', '>', 0)
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal]);
                    if ($cajeroSeleccionado) 
                    { 
                        $query->where('id_usuario_cobro', $this->corteCaja['idUsuario']); 
                    }
                })
                ->get()
                ->flatMap(function ($credito) {
                    // Transformar los detalles válidos
                    return $credito->detalles->map(function ($detalle) use ($credito) {
                        return [
                            'id' => $detalle->num_orden,
                            'created_at' => $detalle->created_at,
                            'monto' => $detalle->abono,
                            'nombre_cliente' => $detalle->cobroCredito->cliente->nombre ?? "N/A",
                            'cajero' => $detalle->usuario->name ?? "N/A",
                            'credito_id' => $credito->num_orden,
                            'tipo' => 'ABONO_TALLER',
                        ];
                    });
                });

                // 2. Obtener los registros de CobroTaller que no tienen CobroTallerCredito
                $cobrosTallerAux = CobroTaller::with([
                    'equipoTaller.equipo.cliente',
                    'equipoTaller.usuario',
                ])
                ->whereDoesntHave('credito') // Filtra los que no tienen CobroTallerCredito
                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                ->when($cajeroSeleccionado, function ($query) {
                    $query->where('id_usuario_cobro', $this->corteCaja['idUsuario']);
                })
                ->orderBy('created_at')
                ->get()
                ->map(function ($cobro) {
                    // Transformar los registros de CobroTaller
                    return [
                        'id' => $cobro->num_orden,
                        'created_at' => $cobro->created_at,
                        'monto' => $cobro->cobro_realizado,
                        'nombre_cliente' => $cobro->equipoTaller->equipo->cliente->nombre ?? "N/A",
                        'cajero' => $cobro->usuario->name ?? "N/A",
                        'tipo' => 'TALLER',
                    ];
                });

                // 3. Combinar los resultados
                $cobrosTaller = $cobrosTallerAux->merge($cobrosTallerCredito);
            }
            else
            {
                $cobrosTaller = CobroTaller::with(['equipoTaller.equipo.cliente', 'equipoTaller.usuario'])
                ->whereDoesntHave('credito')
                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                ->where('cobro_realizado', '>', 0)
                ->when($cajeroSeleccionado, function ($query) {
                    return $query->whereHas('equipoTaller', function ($query) {
                        $query->where('id_usuario_recibio', $this->corteCaja['idUsuario']);
                    });
                })
                ->orderBy('created_at')
                ->get()
                ->map(function($cobro) {
                    return [
                        'id' => $cobro->num_orden,
                        'created_at' => $cobro->created_at,
                        'nombre_cliente' => $cobro->equipoTaller->equipo->cliente->nombre,
                        'monto' => $cobro->cobro_realizado,
                        'cajero' => $cobro->equipoTaller->usuario->name,
                        'tipo' => 'TALLER'
                    ];
                });
            }
        }

        $ventas = collect($ventas); 
        $cobrosTaller = collect($cobrosTaller); 

        // Unión de ambas colecciones
        $registros = $ventas->merge($cobrosTaller);

        // Conversión de resultado a colección de objetos
        $registros = $registros->map(function($item) { return (object) $item; });


        $pdf = SnappyPdf::loadView('livewire.corte-caja', ['corteCaja' => $this->corteCaja, 'registros' => $registros])
        ->setOption('page-size', 'Letter')
        ->setOption('margin-top', 30)
        ->setOption('header-html', view('livewire.pdf.encabezado', compact('tituloCorteCaja'))->render())
        ->setOption('header-spacing', 5)
        ->setOption('footer-center', 'Página [page] de [topage]')
        ->setOption('footer-font-size', '8')
        ->setOption('footer-font-name', 'Montserrat')
        ->setOption('enable-local-file-access', true); // Esta opción es crucial

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
        $this->nombreClienteModal = '';
        $this->clientesModal = null;
    }

    public function cierraModalBuscarCliente()
    {

    }

    public function updatedNombreClienteModal($value)
    {
        if (strlen($value) == 0) $this->clientesModal = null;
        else $this->clientesModal = Cliente::where('nombre', 'like', '%' . $value . '%')
        ->where('telefono', '!=', '0000000000')->where('disponible', 1)
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

        $this->nombreClienteModal = '';
        $this->clientesModal = null;
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

        $this->nombreClienteModal = '';

        $cliente = $this->regresacliente('0000000000');

        $this->usuariosModal = User::where('disponible', 1)->get();

        $this->corteCaja = [
            'fechaInicial' => now()->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'cajero' => Auth::user()->name,
            'idUsuario' => 0,
            'chkCobrosTaller' => true,
            'chkAbonos' => true
        ];

        $this->consecutivoComun = 1;

        $this->showModalErrors = false;
        $this->showMainErrors = !$this->showModalErrors;
    }

    public function cierraVentaCreditoModal()
    {

    }

    public function muestraDivAgregaAbono()
    {
        $this->muestraDivAbono = true;
    }

    public function agregaAbono()
    {
        if (floatval($this->ventaCredito['abono']) > 0)  //Si el abono es mayor que cero
        {
            //Para saber si se sobrepasa el monto a pagar
            $acumulado =$this->sumaAbonos + $this->ventaCredito['abono'];  
            $idVenta = $this->ventaCredito['id'];

            if ($acumulado > $this->ventaCredito['monto'])
            {
                $this->muestraDivAbono = false;
                $this->ventaCredito['abono'] = null;
                $this->addError('abono', 'Debes capturar un monto menor en el abono.');
                $this->dispatch('muestraBotonAgregarPago');
            }
            else
            {
                try 
                {
                    DB::transaction(function () use ($idVenta, $acumulado) 
                    {
                        $ultimoIdAbono = VentaCreditoDetalle::where('id', $idVenta)->max('id_abono');

                        $ventaCreditoDetalles = new VentaCreditoDetalle();
                        $ventaCreditoDetalles->id = $idVenta;
                        $ventaCreditoDetalles->id_abono = $ultimoIdAbono + 1;
                        $ventaCreditoDetalles->abono = $this->ventaCredito['abono'];
                        $ventaCreditoDetalles->id_usuario_venta = Auth::id();
                        $ventaCreditoDetalles->save();

                        $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                        ->where('id_abono', '>', 0)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

                        if ($acumulado == $this->ventaCredito['monto'])
                        {
                            VentaCredito::where('id', $idVenta)->update(['id_estatus' => 2]);
                            $this->ventaCredito['estatus'] = $ventaCreditoDetalles->first()->ventaCredito->estatus->descripcion;
                            $this->ventaCredito['idEstatus'] = 2;
                        }
                    });
                } catch (\Exception $e)
                {
                        // Manejo de errores si ocurre una excepción
                        dd($e);
                }
                $this->muestraDivAbono = false;
                $this->ventaCredito['abono'] = null;

                session()->flash('success', 'El ABONO ha sido agregado exitosamente.');
            }
        }
        else
        {
            if (strlen(trim($this->ventaCredito['abono'])) == 0)
            {
                $this->addError('abono', 'Debes capturar el abono.');
            }
            else
            {
                $this->addError('abono', 'El abono debe ser mayor que cero.');
            }
        }
    }

    public function cobroCredito()
    {
        try 
        {
            DB::transaction(function ()
            {
                if ($this->cantidadProductosCarrito)
                {
                    {
                        $this->totalCarrito = $this->totalCarritoDescuento;
                        // Crear una nueva instancia del modelo Venta
                        $venta = new Venta();

                        // Asignar valores a las propiedades del modelo
                        $venta->id_cliente = $this->cliente['id'];
                        $venta->total = $this->totalCarrito;
                        $venta->id_usuario = Auth::id();

                        // Guardar la instancia del modelo en la base de datos
                        $venta->save();

                        $idVenta = $venta->id;

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

                        $this->dispatch('mostrarToast', 'Venta a crédito realizada con éxito!!!');
                    }
                }

                $idCliente = $this->cliente['id'];

                $ventaCredito = new VentaCredito();
                $ventaCredito->id = $idVenta;
                $ventaCredito->id_estatus = 1;
                $ventaCredito->save();

                //Inserta un abono de $0 en el id_abono 0 para indicar que es CRÉDITO
                $ventaCreditoDetalle = new VentaCreditoDetalle();
                $ventaCreditoDetalle->id = $idVenta;
                $ventaCreditoDetalle->abono = 0;
                $ventaCreditoDetalle->id_usuario_venta = Auth::id();

                $this->sumaAbonos = 0;
                $this->montoLiquidar = $this->totalCarrito;

                $ventaCreditoDetalle->save();

                $this->ventaCredito['nombreCliente'] = $venta->cliente->nombre;
                $this->ventaCredito['id'] = $idVenta;
                $this->ventaCredito['idEstatus'] = 1;
                $this->ventaCredito['estatus'] = "SIN LIQUIDAR";
                $this->ventaCredito['monto'] = $this->montoLiquidar;

                $this->muestraDivAbono = false;

                $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)->where('id_abono', '>', 0)->get();

                $this->dispatch('abreVentaCreditoModal');

                $this->datosCargados = true;
            });
        } catch (\Exception $e)
        {
                // Manejo de errores si ocurre una excepción
                // Puedes agregar logs o notificaciones aquí
                dd($e);
        }
            
    }

    public function executeRender()
    {
        $this->render();
    }

    public function render()
    {
        $productosModal = null;
  
        if (strlen($this->descripcionProductoModal) > 0)
        {
            $productosModal = Producto::where('descripcion', 'like', '%' . $this->descripcionProductoModal . '%')
            ->where('disponible', '=', 1)
            ->whereNotIn('codigo', ['COM01', 'COM02', 'COM03', 'COM04', 'COM05', 'COM06', 'COM07', 'COM08', 'COM09'])
            ->orderBy('descripcion')
            ->paginate(10);

            $this->resetPage();
        }

        return view('livewire.caja', compact('productosModal'));
    }

    public function agregaProductoComun()
    {
        $this->validate();

        $this->cantidadProductoCapturado = $this->cantidadProductoComun;
        $producto = Producto::where('codigo', 'COM0' . $this->consecutivoComun)->first();
        $this->agregaAlCarrito($producto);
    }

    //QUIERO QUE CUANDO EL INVENTARIO SEA -1 NO RESTE INVENTARIO NI VALIDE SI HAY EN EXISTENCIA
    public function agregaProducto()
    {
        if (strlen($this->codigoProductoCapturado) > 0 and $this->codigoProductoCapturado == '0')
        {
            $this->cantidadProductoComun = 1;
            $this->descripcionProductoComun = '';
            $this->montoProductoComun = 0;

            $this->dispatch('abrirModalProductoComun');

            return 0;
        }

        if (strlen($this->codigoProductoCapturado) == 0)
        {
            $this->dispatch('abrirModalBuscarProducto');

            return 0;
        }

        if ($this->codigoProductoCapturado == 'COM01'
        || $this->codigoProductoCapturado == 'COM02'
        || $this->codigoProductoCapturado == 'COM03'
        || $this->codigoProductoCapturado == 'COM04'
        || $this->codigoProductoCapturado == 'COM05'
        || $this->codigoProductoCapturado == 'COM06'
        || $this->codigoProductoCapturado == 'COM07'
        || $this->codigoProductoCapturado == 'COM08'
        || $this->codigoProductoCapturado == 'COM09')
        {
            $this->dispatch('mostrarToastError', 'Código RESERVADO del sistema. Intenta con otro!!!');

            return 0;
        }

        $this->cantidadProductoCapturado = 1;
        $producto = Producto::whereRaw("TRIM(codigo) = ?", trim($this->codigoProductoCapturado))->first();

        if ($producto)
        {
            if ($producto->inventario == -1)  //SI EL PRODUCTO NO REQUIERE INVENTARIO
            {
                $inventarioDisponible = 1;   //ASIGNACION ARBITRARIA PARA QUE SIEMPRE TENGA INVENTARIO
            }
            else
            {
                $cantidadSolicitada = $this->cantidadProductoCapturado;
                $inventarioActual = $producto->inventario;
                $inventarioDisponible = $inventarioActual - $cantidadSolicitada;
            }

            if ($inventarioDisponible >= 0)
            {
                if ($producto->disponible)
                {
                $this->agregaAlCarrito($producto);
                }
                else
                {
                    $this->codigoProductoCapturado = '';
                    $this->dispatch('mostrarToastErrorRouteInventario', 'El producto ' . $producto->descripcion . ' no está DISPONIBLE para su venta.', '<i class="fa-solid fa-boxes-stacked"></i> Ir a inventario');
                }
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
                if ($producto->inventario == -1)  //SI NO MANEJA INVENTARIO
                {
                    $inventarioDisponible = 1; //PARA ASEGURARSE QUE SIEMPRE HAYA INVENTARIO DISPONIBLE
                }
                else
                {
                    $inventarioActual = $producto->inventario;
                    $inventarioDisponible = $inventarioActual - $value;
                }

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
        // if ($this->carrito[$index]['esProductoComun'])
        // {
        //     if ($this->consecutivoComun > 1) $this->consecutivoComun--;
        // }

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
                $comunCodigos = [
                    'COM01', 'COM02', 'COM03', 'COM04', 
                    'COM05', 'COM06', 'COM07', 'COM08', 'COM09'
                ];
                
                if (in_array($producto->codigo, $comunCodigos)) {
                    $subTotal = $this->montoProductoComun;
                    $item = [
                        'esProductoComun' => true,
                        'descripcionProductoComun' => trim(mb_strtoupper($this->descripcionProductoComun)),
                        'precioProductoComun' => $this->montoProductoComun / $this->cantidadProductoComun,
                    ];
                    $this->consecutivoComun++;
                } else {
                    $subTotal = $producto->precio_venta * $this->cantidadProductoCapturado;
                    $item = [
                        'esProductoComun' => false,
                    ];
                }

                // Agregar los elementos comunes a todos los casos al array $item
                $item = array_merge($item, [
                    'producto' => $producto,
                    'cantidad' => $this->cantidadProductoCapturado,
                    'subTotal' => number_format($subTotal, 2, '.', ','),
                    'cantidadVieja' => $this->cantidadProductoCapturado
                ]);

                $this->carrito->push($item); 

                if ($item['esProductoComun']) 
                { 
                    $this->dispatch('cerrarModalProductoComun');
                }
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

        if ($producto->inventario != -1)   //SOLO SI EL PRODUCTO MANEJA INVENTARIO
        {
            $inventarioRestante = $producto->inventario - $cantidad;
            $producto->inventario = $inventarioRestante;
        }

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

                    // Crear una nueva instancia del modelo Venta
                    $venta = new Venta();

                    // Asignar valores a las propiedades del modelo
                    $venta->id_cliente = $this->cliente['id'];
                    $venta->total = $this->totalCarrito;
                    $venta->id_usuario = Auth::id();

                    // Guardar la instancia del modelo en la base de datos
                    $venta->save();
                    
                    foreach ($this->carrito as $item)
                    {
                        $cantidad = $item['cantidad'];
                        $codigoProducto = $item['producto']->codigo;

                        if ($item['esProductoComun'])
                        { 
                            $subTotal = $item['precioProductoComun'] * $cantidad;
                        }
                        else
                        {
                            $subTotal = $item['producto']->precio_venta * $cantidad;
                        }

                        $venta->detalles()->createMany([
                            [
                            'codigo_producto' => $item['producto']->codigo,
                            'cantidad' => $cantidad,
                            'importe' => $subTotal
                            ],
                        ]);
                        
                        if ($item['esProductoComun'])  //GUARDA el producto COMUN
                        {
                            $venta->productosComun()->createMany([
                                [
                                'codigo_producto' => $item['producto']->codigo,
                                'descripcion_producto' => $item['descripcionProductoComun'],
                                ],
                            ]);
                        }
                        else
                        {
                            $this->restaInventario($codigoProducto, $cantidad);
                        }
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
