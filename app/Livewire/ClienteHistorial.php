<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Venta;
use Livewire\WithPagination;


class ClienteHistorial extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    public $muestraHistorialClienteModal, $muestraHistorialVentasModal;
    public $collapsed = [];

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
            ->paginate(5);

            $cliente = $this->regresaCliente($this->cliente['id']);
    
            if (isset($cliente))   //Cliente ya existente
            {
                $this->cliente['nombre'] = $cliente->nombre;
                // $this->cliente['direccion'] = $cliente->direccion;
                // $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;
            }
        }
        else
        {
            $historialClienteTaller = null;
        }

        if ($this->muestraHistorialVentasModal) 
        {
            $historialClienteVentas = Venta::where('id_cliente', $this->cliente['id'])->paginate(5);

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

        // $this->goToPage(1);

        // dd($historialClienteTaller);

        $clientes = $clientesQuery->where('disponible', 1)->paginate(10);

        return view('livewire.clientes.historial', compact('clientes', 'historialClienteTaller', 'historialClienteVentas'));
    }

    protected function regresaCliente($id)
    {
        $cliente = Cliente::find($id);

        return $cliente;
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
