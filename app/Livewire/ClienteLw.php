<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class ClienteLw extends Component
{
    use WithPagination;

    public $numberOfPaginatorsRendered = [];
    public $showMainErrors, $showModalErrors;
    
    public $rules, $messages;
    
    public $filtrosClientes = [
        'telefonoId',
        'nombre',
        'direccion',
        'telefonoContacto',
        'disponible'
    ];

    public $clienteModal = [
        'id',
        'telefonoId',
        'nombre',
        'direccion',
        'telefonoContacto',
        'disponible'
    ];

    public $clienteModalEdit = [
        'id',
        'telefonoId',
        'nombre',
        'direccion',
        'telefonoContacto',
        'disponible'
    ];

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

        if ($this->filtrosClientes['direccion'] != '')
        {
           $clientesQuery->where('direccion', 'like', '%'. $this->filtrosClientes['direccion'] . '%');
        }

        if ($this->filtrosClientes['telefonoContacto'] != '')
        {
           $clientesQuery->where('telefono_contacto', 'like', '%'. $this->filtrosClientes['telefonoContacto'] . '%');
        }

        if ($this->filtrosClientes['disponible'] != -1) {
            $clientesQuery->where('disponible', $this->filtrosClientes['disponible']);
        }

        $clientesQuery->where('telefono', '!=', "0000000000");
        

        $clientes = $clientesQuery->paginate(10);

        $this->goToPage(1);


        return view('livewire.clientes.index', compact('clientes'));
    }

    public function mount()
    {
        $this->filtrosClientes = [
            'telefonoId' => '',
            'nombre' => '',
            'direccion' => '',
            'telefonoContacto' => '',
            'disponible' => -1
        ];

        $this->clienteModal = [
            'id' => 0,
            'telefonoId' => '',
            'nombre' => '',
            'direccion' => '',
            'telefonoContacto' => '',
            'disponible' => -1
        ];

        $this->clienteModalEdit = [
            'id' => 0,
            'telefonoId' => '',
            'nombre' => '',
            'direccion' => '',
            'telefonoContacto' => '',
            'disponible' => -1
        ];
    }

    public function editaCliente($idCliente)
    {
        $cliente = Cliente::find($idCliente);

        $this->clienteModalEdit = [
            'id' => $cliente->id,
            'telefonoId' => $cliente->telefono,
            'nombre' => $cliente->nombre,
            'direccion' => $cliente->direccion,
            'telefonoContacto' => $cliente->telefono_contacto,
            'disponible' => $cliente->disponible
        ];

        $this->rules =
        [
            'clienteModalEdit.telefonoId' => 'required|numeric|digits:10|unique:clientes,telefono,' . $this->clienteModalEdit['id'] . ',id',
            'clienteModalEdit.telefonoContacto' => 'required|numeric|digits:10',
            'clienteModalEdit.nombre' => 'required|string|max:50',
            'clienteModalEdit.direccion' => 'required|string|max:50',
        ];

        $this->messages =
        [
            'clienteModalEdit.telefonoId.required' => 'El campo Teléfono Id es obligatorio.',
            'clienteModalEdit.telefonoId.numeric' => 'El campo Teléfono Id debe ser un número.',
            'clienteModalEdit.telefonoId.digits' => 'El campo Teléfono Id debe tener exactamente 10 dígitos.',
            'clienteModalEdit.telefonoId.unique' => 'El número de Teléfono Id ya está registrado.',
            'clienteModalEdit.telefonoContacto.required' => 'El campo Teléfono Contacto es obligatorio.',
            'clienteModalEdit.telefonoContacto.numeric' => 'El campo Teléfono Contacto debe ser un número.',
            'clienteModalEdit.telefonoContacto.digits' => 'El campo Teléfono Contacto debe tener exactamente 10 dígitos.',
            'clienteModalEdit.nombre.required' => 'El campo Nombre es obligatorio.',
            'clienteModalEdit.direccion.max' => 'El Nombre no puede exceder los 50 caracteres.',
            'clienteModalEdit.direccion.max' => 'La Dirección no puede exceder los 50 caracteres.',
        ];

    }

    public function cierraEditarClienteModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }


    public function actualizaCliente()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        if ($this->clienteModalEdit['direccion'] === null || strlen(trim($this->clienteModalEdit['direccion'])) === 0)
        {
            $this->clienteModalEdit['direccion'] = "-";
        }

        $this->validate();

        $cliente = Cliente::findOrFail($this->clienteModalEdit['id']);
        $cliente->telefono = $this->clienteModalEdit['telefonoId'];
        $cliente->nombre = trim(mb_strtoupper($this->clienteModalEdit['nombre']));
        $cliente->direccion = trim(mb_strtoupper($this->clienteModalEdit['direccion']));
        $cliente->telefono_contacto = $this->clienteModalEdit['telefonoContacto'];
        $cliente->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        session()->flash('success', 'El CLIENTE se ha actualizado correctamente.');

        $this->resetModal();

        $this->dispatch('cerrarModalEditarCliente');     
    }

    public function resetModal()
    {
        $this->clienteModal = [
            'id' => 0,
            'telefonoId' => '',
            'nombre' => '',
            'direccion' => '',
            'telefonoContacto' => '',
            'disponible' => -1
        ];
    }

    public function abreAgregaCliente()
    {
        $this->rules =
        [
            'clienteModal.id' => 'required|numeric',
            "clienteModal.telefonoId" => "required|digits:10|unique:clientes,telefono,{$this->clienteModal['id']},id",
            
            'clienteModal.telefonoContacto' => 'required|numeric|digits:10',
            'clienteModal.nombre' => 'required|string|max:50',
            'clienteModal.direccion' => 'required|string|max:50',
        ];

        $this->messages =
        [
            'clienteModal.id.required' => 'El campo Id es obligatorio.',
            'clienteModal.id.numeric' => 'El campo Id debe ser un número.',
            'clienteModal.telefonoId.required' => 'El campo Teléfono Id es obligatorio.',
            'clienteModal.telefonoId.numeric' => 'El campo Teléfono Id debe ser un número.',
            'clienteModal.telefonoId.digits' => 'El campo Teléfono Id debe tener exactamente 10 dígitos.',
            'clienteModal.telefonoId.unique' => 'El número de Teléfono Id ya está registrado.',
            'clienteModal.telefonoContacto.required' => 'El campo Teléfono Contacto es obligatorio.',
            'clienteModal.telefonoContacto.numeric' => 'El campo Teléfono Contacto debe ser un número.',
            'clienteModal.telefonoContacto.digits' => 'El campo Teléfono Contacto debe tener exactamente 10 dígitos.',
            'clienteModal.nombre.required' => 'El campo Nombre es obligatorio.',
            'clienteModal.direccion.max' => 'El Nombre no puede exceder los 50 caracteres.',
        ];
    }

    public function guardaCliente()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        if ($this->clienteModal['direccion'] === null || strlen(trim($this->clienteModal['direccion'])) === 0)
        {
            $this->clienteModal['direccion'] = "-";
        }

        $this->validate();

        $cliente = new Cliente;
        $cliente->telefono = $this->clienteModal['telefonoId'];
        $cliente->nombre = trim(mb_strtoupper($this->clienteModal['nombre']));
        $cliente->direccion = trim(mb_strtoupper($this->clienteModal['direccion']));
        $cliente->telefono_contacto = $this->clienteModal['telefonoContacto'];
        $cliente->disponible = 1;
        $cliente->save();

        $this->showModalErrors = false;
        $this->showMainErrors = true;

        session()->flash('success', 'El CLIENTE se ha agregado correctamente.');

        $this->resetModal();

        $this->dispatch('cerrarModalNuevoCliente');
    }

    public function cierraNuevoClienteModal()
    {
        $this->resetModal();
    }

    public function invertirEstatusCliente($idCliente)
    {
        $cliente = Cliente::findOrFail($idCliente);

        $cliente->disponible = !$cliente->disponible;
        $cliente->save();
    } 
    
}
