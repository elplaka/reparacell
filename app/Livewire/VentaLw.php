<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\User;
use App\Models\ModoPago;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class VentaLw extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors, $modosPago, $ventaModal, $idModoPago;
    public $collapsed = [];
    public $usuarios;

    public $filtrosVentas = [
        'fechaInicial',
        'fechaFinal',
        'cliente',
        'idUsuario',
        'cancelada'
    ];

    protected $listeners = [
        'lisCancelaVenta' => 'cancelaVenta'
    ];  

    public function abrirEditarModoPagoModal($idVenta)
    {
        $this->ventaModal = Venta::findOrFail($idVenta);

        $this->idModoPago =  $this->ventaModal->id_modo_pago;

        $this->dispatch('abreModalEditaModoPagoVentaCredito');
    }

    public function actualizarModoPago()
    {
        $this->ventaModal->id_modo_pago = $this->idModoPago;
        $this->ventaModal->update();

        $this->dispatch('cierraModalEditaModoPago');
        $this->dispatch('mostrarToast', 'Modo de pago actualizado con éxito!!!');
    }

    public function actualizaInventario($detalles, $ventaCancelada)
    {
        if ($ventaCancelada)
        {
            foreach ($detalles as $detalle)
            {
                $detalle->producto->inventario += $detalle->cantidad;
                $detalle->producto->save();
            }
        }
        else
        {
            foreach ($detalles as $detalle)
            {
                $detalle->producto->inventario -= $detalle->cantidad;
                $detalle->producto->save();
            }
        }
    }

    public function cancelaVenta($idVenta)
    {
        try {
        // 1. Iniciar la Transacción
        DB::transaction(function () use ($idVenta) {
            
            // 1. Obtener la venta
            $venta = Venta::findOrFail($idVenta);
            
            // 2. Invertir el estado 'cancelada'
            $venta->cancelada = !$venta->cancelada;
            $venta->save(); 
            
            // 3. Obtener el estado final y los detalles
            $ventaCancelada = $venta->cancelada;
            $ventaDetalles = VentaDetalle::where('id_venta', $idVenta)->get();
            
            // 4. Actualizar el inventario
            // Si $this->actualizaInventario() falla (ej. stock negativo), 
            // DEBE lanzar una excepción para forzar el ROLLBACK.
            $this->actualizaInventario($ventaDetalles, $ventaCancelada);
            $this->dispatch('mostrarToast', 'Venta cancelada con éxito!!!');
        }); // COMMIT automático si no hay excepciones.

        // 5. Éxito
        // $mensaje = $venta->cancelada ? 'Venta cancelada y inventario actualizado.' : 'Venta reactivada y inventario ajustado.';
        // return response()->json(['message' => $mensaje], 200);

        } catch (ModelNotFoundException $e) {
            // Manejar el caso específico si la Venta no se encuentra
            Log::warning("Intento de actualizar venta no encontrada: ID $idVenta");
            return response()->json(['error' => 'Venta no encontrada.'], 404);

        } catch (Exception $e) {
            // 6. Manejo de Error General (incluye fallos en save() o actualizaInventario())
            
            // Registrar el error en los logs de Laravel (storage/logs/laravel.log)
            Log::error("Error en transacción de cancelación de venta #$idVenta: " . $e->getMessage());

            // Retornar una respuesta de error al frontend
            return response()->json([
                'error' => 'No se pudo completar la operación (Venta/Inventario).',
                'details' => $e->getMessage() // Útil para depuración en desarrollo
            ], 500);
        }
    }

    public function invertirEstatusVenta($idVenta)
    {
        $this->dispatch('mostrarToastAceptarCancelarIdVenta', '¿Deseas CANCELAR la venta seleccionada?', 'lisCancelaVenta', $idVenta);        
    } 

    public function render()
    {
        $ventasQuery = Venta::query();

        $ventasQuery->whereDate('created_at', '>=', $this->filtrosVentas['fechaInicial'])
        ->whereDate('created_at', '<=', $this->filtrosVentas['fechaFinal']);

        if ($this->filtrosVentas['cliente'] != '')
        {
            $nombreCliente = $this->filtrosVentas['cliente'];
            $ventasQuery->whereHas('cliente', function ($query) use ($nombreCliente) {
                $query->where('nombre', 'LIKE', "%$nombreCliente%");
            });
        }

        if ($this->filtrosVentas['idUsuario'] != 0)
        {
            $ventasQuery->where('id_usuario', $this->filtrosVentas['idUsuario']);
        }

        if ($this->filtrosVentas['idModoPago'] != 0)
        {
            $ventasQuery->where('id_modo_pago', $this->filtrosVentas['idModoPago']);
        }

        if ($this->filtrosVentas['cancelada'] > 0)
        {
            $this->filtrosVentas['cancelada'] == 1 ? $cancelada = 0 : $cancelada = 1;
            $ventasQuery->where('cancelada', $cancelada);
        }

        $ventas = $ventasQuery->paginate(10);

        // Inicializar collapsed si no está configurado para una venta
        foreach ($ventas as $venta) {
            if (!isset($this->collapsed[$venta->id])) {
                $this->collapsed[$venta->id] = true; // Mostrar los detalles por defecto
            }
        }

        $this->showMainErrors = true;

        return view('livewire.ventas.index', compact('ventas'));
    }

    public function updatedPage($page)
    {
        // Runs after the page is updated for this component...
        $this->collapsed = [];
    }

    public function mount()
    {
        $this->collapsed = [];

        $this->filtrosVentas = [
            // 'fechaInicial' => now()->subDays(7)->toDateString(),
            'fechaInicial' => now()->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'cliente' => '',
            'idModoPago' => 0,
            'idUsuario' => 0,
            'cancelada' => 0
        ];

        $this->usuarios = User::all();
        $this->modosPago = ModoPago::where('id', '>', 0)->get();

        $this->showMainErrors = true;

    }

    public function verDetalles($ventaId)
    {
        if ($this->collapsed[$ventaId] == true) {
            $this->collapsed[$ventaId] = false;
        } else {
            $this->collapsed[$ventaId] = true; // Muestra los detalles.
        }
    }

}
