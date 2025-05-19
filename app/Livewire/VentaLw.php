<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Venta;
use App\Models\User;
use App\Models\ModoPago;
use Livewire\WithPagination;

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

    public function invertirEstatusVenta($idVenta)
    {
        $venta = Venta::findOrFail($idVenta);

        $venta->cancelada = !$venta->cancelada;
        $venta->save();     
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
