<div wire:ignore.self class="modal fade" id="buscarClienteModal" name="buscarClienteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Buscar clientes</b></h1> &nbsp; &nbsp; <span wire:loading class="text-center">
                <i class="fa fa-spinner fa-spin"></i> Cargando... </span>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraBuscarClienteModal">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           @if($showModalErrors)
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
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

           <div class="modal-body">
            <div class="container mt-3 font-sans text-gray-900 antialiased">
                <div class="row mb-2">
                    <label for="nombreClienteModal" wire:ignore class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Nombre') }}</label>
                    <div class="col-md-5" wire:ignore>
                        <div class="input-group">
                            <input wire:model="nombreClienteModal" wire:keydown.enter="executeRender" wire:key="nombreClienteModal" type="text" class="select-height form-control" id="nombreClienteModal" style="font-size:11pt;" key="nombreClienteInput">
                            <div class="input-group-append">
                                <button wire:click="executeRender" class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" id="button-addon2">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    @if ($clientesModal && $clientesModal->count())
                        <div class="table-responsive">
                            <table id="clientesTable" class="table table-sm table-hover table-bordered" autofocus>
                                <thead>
                                    <tr>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Id</th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Nombre</th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Dirección</th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Teléfono</th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Tel. Contacto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientesModal as $clienteModal)
                                    <tr style="font-size: 9pt; cursor: pointer;" wire:click="capturarFila({{ $clienteModal->id }})">
                                        <td class="px-2 py-1 whitespace-no-wrap">{{ $clienteModal->id }}</td>
                                        <td class="px-2 py-1 whitespace-no-wrap">{{ $clienteModal->nombre }}</td>
                                        <td class="px-2 py-1 whitespace-no-wrap">{{ $clienteModal->direccion }}</td>
                                        <td class="px-2 py-1 whitespace-no-wrap">{{ $clienteModal->telefono }}</td>
                                        <td class="px-2 py-1 whitespace-no-wrap">{{ $clienteModal->telefono_contacto }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-mx">
                            <label class="col-form-label float-left">
                                {{ $clientesModal->links('livewire.paginame') }}
                            </label>
                        </div> 
                    @endif
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button wire:ignore class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarBuscarClienteModal" wire:click="cierraModalBuscarCliente">Cerrar</button>
            </div>
        </div>
       </div>
   </div>
</div>

