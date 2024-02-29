<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.clientes.modal-nuevo')
    @include('livewire.clientes.modal-editar') 
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-elevator"></i> Índice de Clientes </b></h4>
        <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaCliente" title="Agregar cliente" wire:loading.attr="disabled" wire:target="abreAgregaCliente" data-toggle="modal" data-target="#nuevoClienteModal">
            <i class="fas fa-plus"></i>
        </a>
    </div>
    @if ($showMainErrors)
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" wire:ignore>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('success') }}
        </div>
        @endif
    @endif
    <div class="row" wire:ignore>  {{-- El wire:ignore en el div exterior evita que desaparezcan los selectpickers de dentro --}}
        <div class="col-md-2 mb-3">
            <label for="filtrosClientes.telefonoId" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Teléfono Id </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosClientes.telefonoId" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosClientes.nombre" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Nombre </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosClientes.nombre" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosClientes.direccion" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Dirección </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosClientes.direccion" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosClientes.telefonoContacto" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Teléfono Contacto </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosClientes.telefonoContacto" style="font-size:11pt;">
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosClientes.disponible" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Estatus</label>
            <select wire:model.live="filtrosClientes.disponible" class="selectpicker select-picker w-100" style="font-size:11pt;">
                <option value="-1"> -- TODOS -- </option> 
                <option value="0" data-content="<i class='fa-solid fa-rectangle-xmark'></i> &nbsp; NO DISPONIBLE"></option>
                <option value="1" data-content="<i class='fa-solid fa-square-check'></i> &nbsp; DISPONIBLE"></option>
            </select>
        </div>
    </div>
        
    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TELÉFONO ID</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">NOMBRE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DIRECCIÓN</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TELÉFONO CONTACTO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $cliente->id }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $cliente->telefono }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $cliente->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $cliente->direccion }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $cliente->telefono_contacto }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($cliente->disponible)
                            DISPONIBLE
                        @else
                            NO DISPONIBLE
                        @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        <a wire:click.prevent="editaCliente('{{ $cliente->id }}')" title="Editar cliente" wire:loading.attr="disabled" wire:target="editaCliente" style="color: dimgrey; cursor:pointer;" data-toggle="modal" data-target="#editarClienteModal"
                        >
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a>                    
                        <a wire:click="invertirEstatusCliente('{{ $cliente->id }}')" wire:loading.attr="disabled" wire:target="invertirEstatusCliente" style="color: dimgrey;cursor:pointer">
                            @if ($cliente->disponible)
                            <i class='fa-solid fa-rectangle-xmark' style="color: dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Poner NO DISPONIBLE"></i>
                            @else
                            <i class='fa-solid fa-square-check' style="color: dimgrey;" onmouseover="this.style.color='green'" onmouseout="this.style.color='dimgrey'" title="Poner DISPONIBLE"></i>
                            @endif
                        </a>
                        </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="col-mx">
            <label class="col-form-label float-left">
                {{ $clientes->links('livewire.paginame') }}
            </label>
        </div> 
    </div>

    <script>
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('cerrarModalNuevoCliente', () => {
            document.getElementById('btnCerrarNuevoClienteModal').click();
                })
        });
    
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('cerrarModalEditarCliente', () => {
            document.getElementById('btnCerrarEditarClienteModal').click();
                })
        });
    
    </script>
