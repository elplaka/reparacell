<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Venta;
use App\Models\CobroTallerCredito;
use App\Models\VentaCredito;
use Livewire\WithPagination;

class ClienteHistorial extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $muestraHistorialClienteModal, $muestraHistorialVentasModal;
    public $muestraHistorialCreditosTallerModal, $muestraHistorialCreditosVentasModal;
    public $collapsed = [];
    public $equiposClienteModal;
    public $modalSoloLectura, $datosCargados;

    public $cliente = [
        'id',
        'nombre',              
        'telefono',  
        'direccion',
        'telefonoContacto',
    ];

    public $filtrosClientes = [
        'telefonoId',
        'nombre',
    ];

    public function mount()
    {
        $this->filtrosClientes = [
            'telefonoId' => '',
            'nombre' => '',
        ];

        $this->cliente = [
            'id' => null,
            'nombre' => null,              
            'telefono' => null,  
            'direccion' => null,
            'telefonoContacto' => null,
        ];    
        
        $this->muestraHistorialClienteModal = false;
        $this->equiposClienteModal = null;
    
        $this->modalSoloLectura = true;
    }

    public function cierraModalEquiposCliente()
    {

    }

    public function abrirEquiposClienteModal($idCliente)
    {
        $this->datosCargados = false;

        $this->equiposClienteModal = $this->regresaEquiposCliente($idCliente);

        $this->cliente['nombre'] = $this->equiposClienteModal->first()->cliente->nombre;

        $this->datosCargados = true;

    }   

    public function regresaEquiposCliente($idCliente)
    {
        $equipos = Equipo::where('id_cliente', $idCliente)->where('disponible', 1)->get();

        return $equipos;
    }


    public function render()
    {
        $clientesQuery = Cliente::query();

        if ($this->filtrosClientes['telefonoId'] != '')
        {
           $clientesQuery->where('telefono', 'like', '%'. $this->filtrosClientes['telefonoId'] . '%');
        }

        if ($this->filtrosClientes['nombre'] != '')
        {
           $clientesQuery->where('nombre', 'like', '%'. $this->filtrosClientes['nombre'] . '%');
        }

        if ($this->muestraHistorialClienteModal) 
        {
            $historialClienteTaller = Equipo::join('equipos_taller', 'equipos_taller.id_equipo', '=', 'equipos.id')
            ->where('equipos.id_cliente', $this->cliente['id'])
            ->orderBy('equipos_taller.fecha_salida')
            ->orderBy('equipos_taller.num_orden')
            ->select('equipos.*', 'equipos_taller.num_orden','equipos_taller.id_estatus', 'equipos_taller.fecha_salida', 'equipos_taller.observaciones') 
            ->paginate(5, ['*'], 'historial-equipos');

            $this->resetPage('historial-equipos');

            $cliente = $this->regresaCliente($this->cliente['id']);
    
            if (isset($cliente))   //Cliente ya existente
            {
                $this->cliente['nombre'] = $cliente->nombre;
            }
        }
        else
        {
            $historialClienteTaller = null;
        }

        if ($this->muestraHistorialVentasModal) 
        {
            $historialClienteVentas = Venta::where('id_cliente', $this->cliente['id'])->paginate(5, ['*'], 'historial-ventas');

            // Restablecer la página a la número 1
            $this->resetPage('historial-ventas');

            $cliente = $this->regresaCliente($this->cliente['id']);
    
            if (isset($cliente))   //Cliente ya existente
            {
                $this->cliente['nombre'] = $cliente->nombre;
            }
        }
        else
        {
            $historialClienteVentas = null;
        }

        if ($this->muestraHistorialCreditosTallerModal)
        {
            $historialCreditosTaller = CobroTallerCredito::where('id_cliente', $this->cliente['id'])->paginate(5, ['*'], 'historial-creditos-taller');

            // Restablecer la página a la número 1
            $this->resetPage('historial-creditos-taller');

            $cliente = $this->regresaCliente($this->cliente['id']);
    
            if (isset($cliente))   //Cliente ya existente
            {
                $this->cliente['nombre'] = $cliente->nombre;
            }
        }
        else
        {
            $historialCreditosTaller = null;
        }

        if ($this->muestraHistorialCreditosVentasModal)
        {
            $idCliente = $this->cliente['id'];

            $historialCreditosVentas = Venta::whereHas('ventaCredito', function ($query) use ($idCliente) {
                $query->where('id_cliente', $idCliente);
            })->paginate(5, ['*'], 'historial-creditos-ventas');

            $this->resetPage('historial-creditos-ventas');

            $cliente = $this->regresaCliente($this->cliente['id']);
    
            if (isset($cliente))   //Cliente ya existente
            {
                $this->cliente['nombre'] = $cliente->nombre;
            }
        }
        else
        {
            $historialCreditosVentas = null;
        }

        $clientes = $clientesQuery->where('disponible', 1)->paginate(10);

        return view('livewire.clientes.historial', compact('clientes', 'historialClienteTaller', 'historialClienteVentas', 'historialCreditosTaller', 'historialCreditosVentas'));
    }

    protected function regresaCliente($id)
    {
        $cliente = Cliente::find($id);

        return $cliente;
    }

    public function abrirCreditoTaller($numOrden)
    {
        $this->dispatch('cerrarModalCreditosTallerHistorial');

        $this->dispatch('lisCreditosTallerCliente', numOrden:$numOrden)->to(TallerCreditoLw::class);  
        
    }

    public function abrirCreditoVentas($id)
    {
        $this->dispatch('cerrarModalCreditosVentasHistorial');

        $this->dispatch('lisCreditosVentasCliente', idVenta:$id)->to(VentaCreditoLw::class);  
        
    }

    public function cierraModalCreditosTallerHistorial()
    {
         $this->muestraHistorialCreditosTallerModal = false;
    }

    public function cierraModalCreditosVentasHistorial()
    {
        $this->muestraHistorialCreditosVentasModal = false;
    }

    public function abreHistorialCreditosVentas($idCliente)
    {
        $this->cliente['id'] = $idCliente;

        $this->muestraHistorialCreditosVentasModal = true;
    }

    public function abreHistorialCreditosTaller($idCliente)
    {
        $this->cliente['id'] = $idCliente;

        $this->muestraHistorialCreditosTallerModal = true;
    }

    public function abreHistorialTaller($idCliente)
    {
        $this->cliente['id'] = $idCliente;

        $this->muestraHistorialClienteModal = true;
    }

    public function cierraModalClienteHistorial()
    {
        $this->muestraHistorialClienteModal = false;
    }

    public function abreHistorialVentas($idCliente)
    {
        $this->cliente['id'] = $idCliente;

        $this->muestraHistorialVentasModal = true;

        $this->collapsed = [];
    }

    public function cierraModalVentasHistorial()
    {
        $this->muestraHistorialVentasModal = false;
    }

    public function verDetalles($ventaId)
    {
        if (isset($this->collapsed[$ventaId]))
        {
            unset($this->collapsed[$ventaId]);
        } 
        else 
        {
            $this->collapsed[$ventaId] = true;
        }
    }

    public function updatedPage($page)
    {
        $this->collapsed = [];
    }


}
