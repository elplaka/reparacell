<div wire:ignore.self class="modal fade" id="equiposClienteModal" name="equiposClienteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
          <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Equipos del cliente :: {{ $cliente['nombre'] }}</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraModalEquiposCliente">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           <div wire:loading class="text-center">
               <i class="fa fa-spinner fa-spin"></i> Cargando...
               <br><br>
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
                    <div class="row mb-3">
                        <div class="col-md-11 table-responsive" wire:loading.remove>
                            <table class="table table-sm table-hover table-bordered">
                                <thead>
                                    <tr>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider col-0 d-none">                                        
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider col-1">                                        
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider col-5">
                                        MARCA
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider col-6">
                                        MODELO
                                    </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!is_null($equiposClienteModal))
                                        @foreach($equiposClienteModal as $equipo)
                                        <tr style="font-size: 9pt; cursor: pointer;" wire:click="capturarFilaEquiposCliente({{ $equipo->id }})">
                                            <td class="d-none"> {{ $equipo->id }}</td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {!! $equipo->tipo_equipo->icono !!}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $equipo->marca->nombre }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $equipo->modelo->nombre }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-1 mb-3" wire:loading.remove>
                            <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="nuevoEquipoCliente">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarEquiposClienteModal" wire:click="cierraModalEquiposCliente">Cerrar</button>
                </div>
           </div> 
       </div>
   </div>
</div>
