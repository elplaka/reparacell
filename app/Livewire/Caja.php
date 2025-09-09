<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\CobroTaller;
use App\Models\CobroTallerCredito;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\MovimientoCaja;
use App\Models\VentaCredito;
use App\Models\VentaCreditoDetalle;
use App\Models\ModoPago;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\MovimientoCajaTrait; 
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector; //Funciones globales de MOVIMIENTOS EN CAJA
use Livewire\Attributes\On;

class Caja extends Component
{
    use MovimientoCajaTrait;

    use WithPagination;
    public $numberOfPaginatorsRendered = [];
    public $codigoProductoCapturado;
    public $cantidadProductoCapturado, $cantidadProductosCarrito;
    public $carrito, $totalCarrito, $totalCarritoDescuento;
    public $showMainErrors, $showModalErrors;
    public $nombreClienteModal, $usuariosModal, $modosPagoModal, $idModoPagoA;
    public $descripcionProductoModal;
    public $muestraDivAbono, $detallesCredito, $datosCargados;
    public $sumaAbonos, $montoLiquidar;
    public $consecutivoComun, $cantidadProductoComun, $descripcionProductoComun, $precioProductoComun, $montoProductoComun;
    public $selectModoPago, $ayerCaja, $saldoCajaActual;
    public $tipoPrecio = [];
    public $cambio, $pagoCon;

    public $corteCaja = [
        'fechaInicial',
        'fechaFinal',
        'cajero',
        'idUsuario',
        'chkCobrosTaller',
        'chkAbonos',
        'chkAgrupar',
        'idModoPago'
    ];

    protected $listeners = [
        // 'f4-pressed' => 'cobrar',
        'f9-pressed' => 'abrirCaja', 
        'f10-pressed' => 'abrirCorteCaja', 
        'lisLiquidarVentaCredito' => 'liquidarVentaCredito',
        'lisBorraAbono' => 'borraAbono'
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
        'idAbonoSeleccionado' => null,
        'idModoPago' => 1
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
        $idModoPago = $this->corteCaja['idModoPago'];

        if ($this->corteCaja['fechaInicial'] == $this->corteCaja['fechaFinal'])
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL DÍA :: ' .  $this->formatearFecha($this->corteCaja['fechaInicial']);
        }
        else
        {
            $tituloCorteCaja = 'CORTE DE CAJA DEL ' .  $this->formatearFecha($this->corteCaja['fechaInicial']) . ' AL ' . $this->formatearFecha($this->corteCaja['fechaFinal']);
        }

        $cajeroSeleccionado = $this->corteCaja['idUsuario'] != 0 ? true : false ;

        $movimientoCaja = MovimientoCaja::
        whereBetween('fecha', [$fechaInicial, $fechaFinal])
        ->where('id_tipo', 4)
        ->orderByDesc('fecha')
        ->first();

        // $esMismaFecha = Carbon::parse($fechaInicial)->toDateString() === Carbon::parse($fechaFinal)->toDateString();

        // $consulta = MovimientoCaja::whereBetween('fecha', [$fechaInicial, $fechaFinal])
        //     ->where('id_tipo', 4);

        // $movimientoCaja = $esMismaFecha
        //     ? $consulta->orderByDesc('fecha')->first() // más reciente
        //     : $consulta->orderBy('fecha')->first();    // más antiguo


        $entradasManuales = MovimientoCaja::
        whereBetween('fecha', [$fechaInicial, $fechaFinal])
        ->where('id_tipo', 5)
        ->get();

        $salidasManuales = MovimientoCaja::
        whereBetween('fecha', [$fechaInicial, $fechaFinal])
        ->where('id_tipo', 6)
        ->get();

        if ($this->corteCaja['chkAbonos'])   //Si se quieren ver los ABONOS
        {
            $ventas = Venta::with([
                'cliente',
                'usuario',
                'ventaCredito.ventaCreditoDetalles' => function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                    $query->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                          ->where('abono', '>', 0)
                          ->where('id_modo_pago', $idModoPago);
                }, 
            ])
            ->when($cajeroSeleccionado, function ($query) {
                return $query->where('id_usuario', $this->corteCaja['idUsuario']);
            })
            ->where('cancelada', 0)
            ->where(function($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                // Condición para ventas sin VentaCredito
                $query->where(function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                    $query->whereDoesntHave('ventaCredito')
                          ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                          ->where('id_modo_pago', $idModoPago);            
                })
                // Condición para ventas con VentaCredito que tienen detalles válidos
                ->orWhereHas('ventaCredito.ventaCreditoDetalles', function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                    $query->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                          ->where('abono', '>', 0)
                          ->where('id_modo_pago', $idModoPago);
                });
            })
            ->orderBy('created_at')
            ->get()
            ->flatMap(function ($venta) use($movimientoCaja) {
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
                        'id_modo_pago' => $venta->id_modo_pago,
                        'detalles' => $venta->detalles
                    ]);
                }
            
                // Si hay VentaCredito, incluir únicamente los detalles válidos
                if ($venta->ventaCredito) {
                    $venta->ventaCredito->ventaCreditoDetalles
                        ->each(function ($detalle) use ($resultado, $venta, $movimientoCaja) {
                            $resultado->push([
                                'id' => $detalle->id,
                                'created_at' => $detalle->created_at,
                                'nombre_cliente' => $venta->cliente->nombre,
                                'monto' => $detalle->abono,
                                'cajero' => $detalle->usuario->name ?? 'N/A',
                                'tipo' => 'ABONO_VENTA',
                                'id_modo_pago' => $detalle->id_modo_pago,
                                'detalles' => $venta->ventaCredito->ventaCreditoDetalles
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
            ->where('id_modo_pago', $idModoPago)
            ->where(function($query) {
                $query->whereDoesntHave('ventaCredito')
                        ->orWhereHas('ventaCredito', function ($query) {
                            $query->where('id_estatus', 2);
                        });
            })
            ->get()
            ->map(function($venta) use($movimientoCaja) {
                return [
                    'id' => $venta->id,
                    'created_at' => $venta->created_at,
                    'nombre_cliente' => $venta->cliente->nombre,
                    'monto' => $venta->total,
                    'cajero' => $venta->usuario->name,
                    'tipo' => 'VENTA',
                    'id_modo_pago' => $venta->id_modo_pago,
                    'detalles' => $venta->detalles
                ];
            });
        }

        if ($this->corteCaja['chkAgrupar'])
        {
            $idsVentas = collect($ventas)->pluck('id');
            $ventasCredito = Venta::with('ventaCredito') 
                ->whereIn('id', $idsVentas)
                ->get();

            list($ventasConCredito, $ventasSinCredito) = collect($ventasCredito)->partition(function ($venta) {
                return $venta->ventaCredito !== null;
            });

            $idsVentasSinCredito = collect($ventasSinCredito)->pluck('id');

            // Obtén los detalles desde la base de datos
            $detalles = VentaDetalle::with('producto')
                ->whereIn('id_venta', $idsVentasSinCredito)
                ->get();            

            $productosAgrupados = $detalles
                ->groupBy(function ($item) {
                    // Agrupa por descripción del productoComun si existe, si no por la del producto normal
                    if ($item->productoComun && $item->productoComun->descripcion_producto) {
                        return $item->productoComun->descripcion_producto;
                    }
                    return $item->codigo_producto;
                })
                ->map(function ($items) use ($movimientoCaja) {
                    $primerItem = $items->first();

                    // Verifica si tiene relación con productoComun y si tiene descripción
                    $descripcion = $primerItem->productoComun && $primerItem->productoComun->descripcion_producto
                        ? $primerItem->productoComun->descripcion_producto
                        : ($primerItem->producto->descripcion ?? '—');

                    return [
                        'cantidad' => $items->sum('cantidad'),
                        'prod_serv' => $descripcion,
                        'subtotal' => $items->sum('importe'),
                        'tipo' => 'VENTA_AGRUPADA',
                    ];
                })
                ->values();

            if ($this->corteCaja['chkAbonos'])
            {
                $idsVentasConCredito = collect($ventasConCredito)->pluck('id');

                // Obtén los detalles desde la base de datos
                $detallesCredito = VentaCreditoDetalle::
                whereIn('id', $idsVentasConCredito)->where('abono', '>', 0)
                ->get();
                    
                if ($detallesCredito->count() > 0) {
                        $productosAgrupados->push([
                            'cantidad' => $detallesCredito->count(),
                            'prod_serv' => 'ABONO A VENTA',
                            'subtotal' => $detallesCredito->sum('abono'),
                            'tipo' => 'ABONOS_AGRUPADOS',
                        ]);
                    }
            }

            $ventas = $productosAgrupados;
        }

        // Inicializar $cobrosTaller como una colección vacía 
        $cobrosTaller = collect();
        if ($this->corteCaja['chkCobrosTaller'])
        { 
            if ($this->corteCaja['chkAbonos'])
            {                
                // 1. Obtener los detalles de CobroTallerCredito
                $cobrosTallerCredito = CobroTallerCredito::with([
                    'detalles' => function ($query) use ($fechaInicial, $fechaFinal, $idModoPago) {
                        $query->where('abono', '>', 0)
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->where('id_modo_pago', $idModoPago);            
                    }
                ])
                ->whereHas('detalles', function ($query) use ($fechaInicial, $fechaFinal, $cajeroSeleccionado, $idModoPago) {
                    $query->where('abono', '>', 0)
                    ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->where('id_modo_pago', $idModoPago);
                    if ($cajeroSeleccionado) 
                    { 
                        $query->where('id_usuario_cobro', $this->corteCaja['idUsuario']); 
                    }
                })
                ->get()
                ->flatMap(function ($credito) use($movimientoCaja) {
                    // Transformar los detalles válidos
                    return $credito->detalles->map(function ($detalle) use ($credito, $movimientoCaja) {
                        return [
                            'id' => $detalle->num_orden,
                            'created_at' => $detalle->created_at,
                            'monto' => $detalle->abono,
                            'nombre_cliente' => $detalle->cobroCredito->cliente->nombre ?? "N/A",
                            'cajero' => $detalle->usuario->name ?? "N/A",
                            'credito_id' => $credito->num_orden,
                            'tipo' => 'ABONO_TALLER',
                            'id_modo_pago' => $detalle->id_modo_pago,
                        ];
                    });
                });

                // 2. Obtener los registros de CobroTaller que no tienen CobroTallerCredito
                $cobrosTallerAux = CobroTaller::with([
                    'equipoTaller.equipo.cliente',
                    'equipoTaller.usuario',
                ])
                ->whereDoesntHave('credito') // Filtra los que no tienen CobroTallerCredito
                ->where('cobro_realizado', '>', 0)
                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                ->where('id_modo_pago', $idModoPago)         
                ->when($cajeroSeleccionado, function ($query) {
                    $query->where('id_usuario_cobro', $this->corteCaja['idUsuario']);
                })
                ->orderBy('created_at')
                ->get()
                ->map(function ($cobro) use($movimientoCaja) {
                    // Transformar los registros de CobroTaller
                    return [
                        'id' => $cobro->num_orden,
                        'created_at' => $cobro->created_at,
                        'monto' => $cobro->cobro_realizado,
                        'nombre_cliente' => $cobro->equipoTaller->equipo->cliente->nombre ?? "N/A",
                        'cajero' => $cobro->usuario->name ?? "N/A",
                        'tipo' => 'TALLER',
                        'id_modo_pago' => $cobro->id_modo_pago,
                    ];
                });

                $cobrosTallerAux = collect($cobrosTallerAux->all()); 

                 // 3. Combinar los resultados
                $cobrosTaller = $cobrosTallerAux->merge($cobrosTallerCredito);
            }
            else
            {
                $cobrosTaller = CobroTaller::with(['equipoTaller.equipo.cliente', 'equipoTaller.usuario'])
                ->whereDoesntHave('credito')
                ->where('id_modo_pago', $idModoPago)         
                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                ->where('cobro_realizado', '>', 0)
                ->when($cajeroSeleccionado, function ($query) {
                    return $query->whereHas('equipoTaller', function ($query) {
                        $query->where('id_usuario_recibio', $this->corteCaja['idUsuario']);
                    });
                })
                ->orderBy('created_at')
                ->get()
                ->map(function($cobro) use($movimientoCaja) {
                    return [
                        'id' => $cobro->num_orden,
                        'created_at' => $cobro->created_at,
                        'nombre_cliente' => $cobro->equipoTaller->equipo->cliente->nombre,
                        'monto' => $cobro->cobro_realizado,
                        'cajero' => $cobro->equipoTaller->usuario->name,
                        'tipo' => 'TALLER',
                        'id_modo_pago' => $cobro->id_modo_pago,
                    ];
                });                
            }

            if ($this->corteCaja['chkAgrupar'])
                {                    
                    $numerosOrden = collect($cobrosTaller)->pluck('id');

                    $cobrosCredito = CobroTaller::with('credito') 
                    ->whereIn('num_orden', $numerosOrden)
                    ->get();

                    list($cobrosConCredito, $cobrosSinCredito) = collect($cobrosCredito)->partition(function ($cobro) {
                    return $cobro->credito !== null;
                    });

                    $numerosOrdenSinCredito = collect($cobrosSinCredito)->pluck('num_orden');

                    $detalles = CobroTaller::
                        whereIn('num_orden', $numerosOrdenSinCredito)
                        ->get();
                    
                    $cobrosAgrupados = collect();
                    if ($detalles->count() > 0)
                    {
                        $cobrosAgrupados = collect([[
                            'cantidad' => $detalles->count(),
                            'prod_serv' => 'REPARACIÓN EN TALLER',
                            'subtotal' => $detalles->sum('cobro_realizado'),
                            'tipo' => 'TALLER_AGRUPADO',
                        ]]);
                    }

                    if ($this->corteCaja['chkAbonos'])
                    {
                        $numerosOrdenConCredito = collect($cobrosConCredito)->pluck('num_orden');

                        $detalles = CobroTallerCreditoDetalle::
                            whereIn('num_orden', $numerosOrdenConCredito)
                             ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                            ->where('abono', '>', 0)
                            ->get();

                        if ($detalles->count() > 0) 
                        {
                            $cobrosAgrupados->push([
                                'cantidad' => $detalles->count(),
                                'prod_serv' => 'ABONO TALLER',
                                'subtotal' => $detalles->sum('abono'),
                                'tipo' => 'ABONO_TALLER_AGRUPADO',
                            ]);
                        }
                    }
                    $cobrosTaller = $cobrosAgrupados;
                }
        }

        $ventas = collect($ventas);
        $cobrosTaller = collect($cobrosTaller); 

        // Unión de ambas colecciones
        $registros = $ventas->merge($cobrosTaller);
        if ($this->corteCaja['idModoPago'] == 1)
        {
            if ($this->corteCaja['chkAgrupar'])
            {
                $registros->push([
                    'cantidad' => 1,
                    'prod_serv' => 'INICIALIZACION',
                    'subtotal' => $movimientoCaja->saldo_caja,
                    'tipo' => 'INICIALIZACION',
                ]);
                $registros->push([
                    'cantidad' => $entradasManuales->count(),
                    'prod_serv' => 'ENTRADA MANUAL',
                    'subtotal' => $entradasManuales->sum('monto'),
                    'tipo' => 'ENTRADA_MANUAL_AGRUPADO',
                ]);
                $registros->push([
                    'cantidad' => $salidasManuales->count(),
                    'prod_serv' => 'SALIDA MANUAL',
                    'subtotal' => $salidasManuales->sum('monto'),
                    'tipo' => 'SALIDA_MANUAL_AGRUPADO',
                ]);
            }
            else
            {
                $registros->push([
                    'id' => $movimientoCaja->referencia,
                    'created_at' => $movimientoCaja->fecha,
                    'nombre_cliente' => '-',
                    'monto' => $movimientoCaja->saldo_caja,
                    'cajero' => $movimientoCaja->usuario->name,
                    'tipo' => 'INICIALIZACION',
                    'id_modo_pago' => 1,
                    'detalles' => null
                ]);
                if ($entradasManuales)
                { 
                    foreach($entradasManuales as $entrada)
                    { 
                        $registros->push([
                            'id' => $entrada->referencia,
                            'created_at' => $entrada->fecha,
                            'nombre_cliente' => '-',
                            'monto' => $entrada->monto,
                            'cajero' => $entrada->usuario->name,
                            'tipo' => 'ENTRADA_MANUAL',
                            'id_modo_pago' => 1,
                            'detalles' => null
                        ]);
                    }
                }
                if ($salidasManuales)
                { 
                    foreach($salidasManuales as $salida)
                    { 
                        $registros->push([
                            'id' => $salida->referencia,
                            'created_at' => $salida->fecha,
                            'nombre_cliente' => '-',
                            'monto' => $salida->monto,
                            'cajero' => $salida->usuario->name,
                            'tipo' => 'SALIDA_MANUAL',
                            'id_modo_pago' => 1,
                            'detalles' => null
                        ]);
                    }
                }
            }
        }
        // Conversión de resultado a colección de objetos
        $registros = $registros->map(function($item) { return (object) $item; });

        //dd($registros);

        $pdf = SnappyPdf::loadView('livewire.corte-caja', ['corteCaja' => $this->corteCaja, 'registros' => $registros])
        ->setOption('page-size', 'Letter')
        ->setOption('margin-top', 30)
        ->setOption('header-html', view('livewire.pdf.encabezado', compact('tituloCorteCaja'))->render())
        ->setOption('header-spacing', 5)
        ->setOption('footer-center', 'Página [page] de [topage]')
        ->setOption('footer-font-size', '8')
        ->setOption('footer-font-name', 'Montserrat')
        ->setOption('enable-local-file-access', true); // Esta opción es crucial

        return $pdf->stream('CorteCaja.pdf');
    }
    

    // return redirect('/test-pdf');


    public function cierraCorteCajaModal()
    {
        $this->corteCaja['idModoPago'] = 1;
    }

    public function cierraCobroCambioModal()
    {
        $this->cambio = 0;
        $this->pagoCon = '';        
    }

    public function abrirCaja()
    {
        $printer_name = "Ticket";
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->pulse();
        $printer->close();

        return redirect()->route('caja.movimientos');
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

if ($cliente) {
    $this->cliente['id'] = $cliente->id;
    $this->cliente['nombre'] = $cliente->nombre;
    $this->cliente['telefono'] = $cliente->telefono;
    $this->cliente['direccion'] = $cliente->direccion;
    $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;
    $this->cliente['publicoGeneral'] = $telefono == '0000000000' ? 1 : 0;
} else {
    $this->cliente['id'] = null; // O cualquier otro valor por defecto
    $this->cliente['nombre'] = '';
    $this->cliente['telefono'] = $telefono;
    $this->cliente['direccion'] = '';
    $this->cliente['telefonoContacto'] = '';
    $this->cliente['publicoGeneral'] = $telefono == '0000000000' ? 1 : 0;
}
    }

    public function cierraBuscarClienteModal()
    {
        $this->nombreClienteModal = '';
        // $this->clientesModal = null;
    }

    public function cierraModalBuscarCliente()
    {

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
        // $this->clientesModal = null;
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
            'chkAbonos' => true,
            'chkAgrupar' => false,
            'idModoPago' => 1,
        ];

        $this->consecutivoComun = 1;

        $this->showModalErrors = false;
        $this->showMainErrors = !$this->showModalErrors;

        $this->idModoPagoA = 1;  
        
        $this->cambio = 0;
        $this->pagoCon = '';
    }

    public function cierraVentaCreditoModal()
    {

    }

    public function rendered()
    {
    
    }

    public function preguntaBorraAbono($idVenta, $idAbono)
    {
        $this->ventaCredito['idAbonoSeleccionado'] = $idAbono;
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas eliminar el abono seleccionado?', 'lisBorraAbono');
    }

    public function borraAbono()
    {
        $idVenta = $this->ventaCredito['id'];
        $idAbono = $this->ventaCredito['idAbonoSeleccionado'];

        try 
        {
            DB::transaction(function () use ($idVenta, $idAbono) 
            {
                $detCredito =  VentaCreditoDetalle::where('id', $idVenta)
                ->where('id_abono', $idAbono)
                ->first();

                $detalleCredito =  VentaCreditoDetalle::where('id', $idVenta)
                ->where('id_abono', $idAbono)
                ->delete();

                if ($detalleCredito)
                {
                    $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                                        ->where('id_abono', '>', 0)->get();

                    $this->sumaAbonos = $this->detallesCredito->sum('abono');
                    $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

                    VentaCredito::where('id', $idVenta)->update(['id_estatus' => 1]);

                    if ($this->ventaCredito['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                    { 
                        $idRef = $idVenta % 1000;
                        $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
                        $monto = $detCredito->abono;
                        $tipoMov = 7;
     
                        $movimiento = new MovimientoCaja();
                        $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                        $movimiento->id_tipo = $tipoMov;
                        $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                        $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                        $movimiento->id_usuario = Auth::id();
                        $movimiento->save();
                    }

                    $this->ventaCredito['idEstatus'] = 1;
                    $this->ventaCredito['estatus'] = "SIN LIQUIDAR";

                    session()->flash('success', 'El ABONO se ha ELIMINADO con éxito.');
                }
                else
                {
                    $this->addError('abono', 'El abono seleccionado no existe o hubo problemas con la base de datos.');
                }
            });
        } catch (\Exception $e)
        {
                // Manejo de errores si ocurre una excepción
                dd($e);
        }
    }

    public function liquidaCredito()
    {
        $this->dispatch('mostrarToastAceptarCancelar', '¿Deseas liquidar el crédito?', 'lisLiquidarVentaCredito');
    }

    public function liquidarVentaCredito()
    {
       $idVenta = $this->ventaCredito['id'];

       try 
       {
           DB::transaction(function () use ($idVenta) 
           {
               $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)->get();
               $ultimoIdAbono = $this->detallesCredito->max('id_abono');
               $this->ventaCredito['monto'] = $this->detallesCredito->first()->ventaCredito->venta->total;
               $this->sumaAbonos = $this->detallesCredito->sum('abono');
               $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;
       
               $ventaCreditoDetalles = new VentaCreditoDetalle();
               $ventaCreditoDetalles->id = $idVenta;
               $ventaCreditoDetalles->id_abono = $ultimoIdAbono + 1;
               $ventaCreditoDetalles->abono = $this->montoLiquidar;
               $ventaCreditoDetalles->id_modo_pago = $this->ventaCredito['idModoPago'];
               $ventaCreditoDetalles->id_usuario_venta = Auth::id();
               $ventaCreditoDetalles->save();
               
               if ($this->ventaCredito['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
               { 
                   $idRef = $idVenta % 1000;
                   $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
                   $monto = $this->montoLiquidar;
                   $tipoMov = 3;

                   $movimiento = new MovimientoCaja();
                   $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                   $movimiento->id_tipo = $tipoMov;
                   $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                   $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                   $movimiento->id_usuario = Auth::id();
                   $movimiento->save();
               }

                VentaCredito::where('id', $idVenta)->update(['id_estatus' => 2]);
                $this->ventaCredito['estatus'] = $ventaCreditoDetalles->first()->ventaCredito->estatus->descripcion;
                $this->ventaCredito['idEstatus'] = 2;

                $this->muestraDivAbono = false;
                $this->ventaCredito['abono'] = null;

                $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)->get();

                session()->flash('success', 'El crédito ha sido LIQUIDADO exitosamente.');
           });
       } catch (\Exception $e)
       {
               // Manejo de errores si ocurre una excepción
               dd($e);
       }
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
                        $ventaCreditoDetalles->id_modo_pago = $this->ventaCredito['idModoPago'];
                        $ventaCreditoDetalles->save();

                        $this->detallesCredito = VentaCreditoDetalle::where('id', $idVenta)
                        ->where('id_abono', '>', 0)->get();
                        $this->sumaAbonos = $this->detallesCredito->sum('abono');
                        $this->montoLiquidar = $this->ventaCredito['monto'] - $this->sumaAbonos;

                        if ($this->ventaCredito['idModoPago'] == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                        { 
                            $idRef = $idVenta % 1000;
                            $idRef = "V" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
                            $monto = $this->ventaCredito['abono'];
                            $tipoMov = 3;
    
                            $movimiento = new MovimientoCaja();
                            $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                            $movimiento->id_tipo = $tipoMov;
                            $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                            $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                            $movimiento->id_usuario = Auth::id();
                            $movimiento->save();
                        }

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
                        $venta->id_modo_pago = 0;  //Venta a crédito
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

    public function abrirModal()
    {
        $this->ventaCredito['idEstatus'] = 1;
        $this->muestraDivAbono = true;
        $this->detallesCredito = VentaCreditoDetalle::where('id', 125)->where('id_abono', '>', 0)->get();

        $this->dispatch('abreVentaCreditoModal');

        $this->datosCargados = true;
    }
    

    public function executeRender()
    {
        $this->render();
    }

    public function render()
    {
        $productosModal = null;
        $clientesModal = null;
  
        if (strlen($this->descripcionProductoModal) > 0)
        {
            $productosModal = Producto::where('descripcion', 'like', '%' . $this->descripcionProductoModal . '%')
            ->where('disponible', '=', 1)
            ->whereNotIn('codigo', ['COM01', 'COM02', 'COM03', 'COM04', 'COM05', 'COM06', 'COM07', 'COM08', 'COM09'])
            ->orderBy('descripcion')
            ->paginate(10);

            $this->resetPage();
        }

        if (strlen($this->nombreClienteModal) > 0)
        {
            $clientesModal = Cliente::where('nombre', 'like', '%' . $this->nombreClienteModal . '%')
            ->where('telefono', '!=', '0000000000')->where('disponible', 1)
            ->paginate(10);

            $this->resetPage();
        }

        $this->modosPagoModal = ModoPago::where('id', '>', 0)->get();

        return view('livewire.caja', compact('productosModal', 'clientesModal'));
    }

    public function agregaProductoComun()
    {
        $this->validate();

        $this->cantidadProductoCapturado = $this->cantidadProductoComun;
        $producto = Producto::where('codigo', 'COM0' . $this->consecutivoComun)->first();

        if ($this->consecutivoComun < 10)
        { 
            $this->agregaAlCarrito($producto);
        }
        else
        {
            $this->dispatch('mostrarToastError', 'Ya NO ES POSIBLE agregar otro PRODUCTO COMÚN en esta venta.');
        }
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

    public function updatedTipoPrecio($value, $key)
    {
        $index = (int) strtok($key, '.');   //Para obtener el item dentro del carrito
 
        $producto = $this->carrito[$index]['producto'];

        if ($value == 1)  //Si es MENUDEO
        {
            $precio = $producto->precio_venta;
        }
        else  //Si es MAYOREO
        {
            $precio = $producto->precio_mayoreo;
        }


        $cantidad = $this->carrito[$index]['cantidad'];

        $subTotal = $precio * $cantidad;

        // dd($this->carrito);

        // Actualizar el subTotal utilizando transform
        $this->carrito->transform(function ($item, $key) use ($index, $subTotal, $cantidad) {
            if ($key === $index) {
                $item['subTotal'] = number_format($subTotal, 2, '.', ',');
                $item['cantidad'] = $cantidad;
                $item['cantidadVieja'] = $cantidad;
            }
            return $item;
        });

        $this->cuentaCantidadProductosCarrito();
    }

    #[On('actualizaCantidadManual')]
    public function actualizaCantidadManual($index, $cantidad)
    {
        // 👉 Simular el modelo cambiado como si fuera wire:model
        $this->updatedCarrito($cantidad, "carrito.{$index}.cantidad");

        // ⚙️ Recalcular totales
        $this->cuentaCantidadProductosCarrito();

    }

    
    public function updatedCarrito($value, $key)
    {
        if (substr($key, -8) === 'cantidad')
        {
            // Obtener el índice del carrito
            // $index = (int) strtok($key, '.');
            $index = (int) explode('.', $key)[1];
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
                    if ($this->tipoPrecio[$index] == 1)  //Menudeo
                    {
                        $subTotal = $producto->precio_venta * $value;
                    }
                    else
                    {
                        $subTotal = $producto->precio_mayoreo * $value;
                        // dd($subTotal);
                    }

                    // Actualizar el subTotal utilizando transform
                    $this->carrito->transform(function ($item, $key) use ($index, $subTotal, $value) {
                        if ($key === $index) {
                            $item['subTotal'] = number_format($subTotal, 2, '.', ',');
                            $item['cantidad'] = $value;
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
                $index = $this->carrito->count();

                $this->tipoPrecio[$index - 1] = 1;  //POR DEFAULT es MENUDEO

                if ($item['esProductoComun']) 
                { 
                    $this->dispatch('cerrarModalProductoComun');
                }
            }
        }
        else
        {
            $this->dispatch('mostrarToastError', 'Producto INEXISTENTE. Intenta con otro!!!');
        }
        $this->cuentaCantidadProductosCarrito();
        $this->reset('codigoProductoCapturado');

        $this->cambio = 0;
        $this->pagoCon = '';
    }

    public function cuentaCantidadProductosCarrito()
    {
        $cant_productos = 0;
        $total_carrito = 0;
        
        foreach ($this->carrito as $item)
        {
            $cant_productos += $item['cantidadVieja'];
            $subTotalFloat = floatval(str_replace(',', '', $item['subTotal']));
            $total_carrito += $subTotalFloat;
        }
        $this->cantidadProductosCarrito = $cant_productos;
        $this->totalCarrito = $total_carrito;
        $this->totalCarritoDescuento = $total_carrito; 

        // dd($this->carrito);
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

    public function cobrar($modoCobro = 1)
    {
        if ($this->cantidadProductosCarrito)
        {
            try 
            {
                DB::transaction(function () use ($modoCobro)
                {
                    $this->dispatch('cerrarModalCobroCambioCaja');

                    $this->totalCarrito = $this->totalCarritoDescuento;

                    // Crear una nueva instancia del modelo Venta
                    $venta = new Venta();

                    // Asignar valores a las propiedades del modelo
                    $venta->id_cliente = $this->cliente['id'];
                    $venta->total = $this->totalCarrito;
                    $venta->id_modo_pago = $this->idModoPagoA;
                    $venta->id_usuario = Auth::id();

                    // Guardar la instancia del modelo en la base de datos
                    $venta->save();
                    $idVenta = $venta->id;

                    if ($this->idModoPagoA == 1) //Si es EFECTIVO se guarda el MOVIMIENTO
                    { 
                        $idRef = $idVenta % 10000;
                        $idRef = str_pad($idRef, 4, '0', STR_PAD_LEFT);
                        $monto = $this->totalCarrito;
                        $tipoMov = 1;

                        $movimiento = new MovimientoCaja();
                        $movimiento->referencia = $this->regresaReferencia($tipoMov, $idRef);
                        $movimiento->id_tipo = $tipoMov;
                        $movimiento->monto = $this->calculaMonto($tipoMov, $monto);
                        $movimiento->saldo_caja = $this->calculaSaldoCaja($tipoMov, $monto); // Asegura que el saldo_caja sea un número decimal
                        $movimiento->id_usuario = Auth::id();
                        $movimiento->save();
                    }


                    // dd($this->carrito);
                    $index = 0;
                    
                    foreach ($this->carrito as $item)
                    {
                        $cantidad = $item['cantidadVieja'];
                        $codigoProducto = $item['producto']->codigo;

                        if ($item['esProductoComun'])
                        { 
                            $subTotal = $item['precioProductoComun'] * $cantidad;
                        }
                        else
                        {
                            if ($this->tipoPrecio[$index] == 1)  //Menudeo
                            { 
                                $subTotal = $item['producto']->precio_venta * $cantidad;
                            }
                            else
                            {
                                $subTotal = $item['producto']->precio_mayoreo * $cantidad;
                            }
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
                        $index++;
                    }

                    $this->carrito = collect(); // Inicializa $carrito como una colección vacía
                    $this->cantidadProductosCarrito = 0;
                    $cliente = $this->regresacliente('0000000000');

                    if ($modoCobro  == 2)
                    {
                        $printer_name = "Ticket";
                        $connector = new WindowsPrintConnector($printer_name);
                        $printer = new Printer($connector);

                        $printer->pulse();
                        $printer->close();
                    }
                    else 
                    { 
                        if ($this->idModoPagoA == 1)  //Solo si es EFECTIVO se imprime ticket
                        {
                            $this->showMainErrors = true;

                            return redirect()->route('ventas.print', $idVenta);
                        }
                    }

                    $this->dispatch('cierraModalCobrar');
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

// SELECT v.id, v.total AS total_venta, IFNULL(SUM(d.importe), 0) AS total_detalles 
// FROM ventas v LEFT JOIN ventas_detalles d ON d.id_venta = v.id GROUP BY v.id, v.total 
// HAVING ROUND(v.total, 2) != ROUND(SUM(d.importe), 2);
