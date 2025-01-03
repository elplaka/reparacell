@php
    $hayItemsNoDisponibles = false;
    $hayItemsInexistentes = false;
@endphp
<div wire:ignore.self class="modal fade" id="equiposClienteModal" name="equiposClienteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" style="display:{{ $datosCargados ? 'block' : 'none' }}">
       <div class="modal-content">
            <div class="modal-header flex-column">
                <!-- Primer renglón: Botón de cerrar alineado a la derecha y pegado a la esquina -->
                <div class="w-100 d-flex justify-content-end align-items-start">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraModalEquiposCliente" style="position: absolute; right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            
                <!-- Segundo y tercer renglón: Nombre del cliente -->
                <div class="w-100 text-center mt-0">
                    <h1 class="text-xl font-bold mb-0"><b>Equipos del cliente :: {{ $cliente['nombre'] }}</b></h1>
                </div>
            
                <!-- Cuarto renglón: Ícono de carga -->
                <div class="w-100 mt-0 mb-0 d-flex justify-content-center align-items-center">
                    <div style="visibility: hidden;">
                        <i class="fa fa-spinner fa-spin"></i> Cargando...
                    </div>
                    <div wire:loading style="position: absolute;">
                        <i class="fa fa-spinner fa-spin"></i> Cargando...
                    </div>
                </div>
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
           <div class="modal-body mt-0">
               <div class="container font-sans text-gray-900 antialiased">
                    <div class="row mb-3">
                        <div class="col-md-11 table-responsive">
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
                                    @if ($modalSoloLectura)
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider col-6">
                                        ESTATUS
                                    </th>
                                    {{-- <th class="px-2 py-2 bg-gray-200 text-center text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th> --}}
                                    @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!is_null($equiposClienteModal))
                                        @foreach($equiposClienteModal as $equipo)
                                        @php
                                            if ($equipo->tipo_equipo->disponible)
                                            {
                                                $tipoEquipo = $equipo->tipo_equipo->icono;
                                            }
                                            else 
                                            {
                                                $tipoEquipo = $equipo->tipo_equipo->icono . "*";
                                                $hayItemsNoDisponibles = true;
                                            }

                                            if ($equipo->marca->disponible)
                                            {
                                                $nombreMarca = $equipo->marca->nombre;
                                            }
                                            else
                                            {
                                                $nombreMarca = $equipo->marca->nombre . "*";
                                                $hayItemsNoDisponibles = true;
                                            }    
                                            if ($equipo->modelo->disponible)
                                            {
                                                $nombreModelo = $equipo->modelo->nombre;
                                            }
                                            else
                                            {
                                                $nombreModelo = $equipo->modelo->nombre . "*";
                                                $hayItemsNoDisponibles = true;
                                            } 
                                        @endphp
                                        @if ($modalSoloLectura)
                                        <tr style="font-size: 9pt;">
                                        @else
                                            @if ($equipo->tipo_equipo->disponible)
                                            <tr style="font-size: 9pt; cursor: pointer;" wire:click="capturarFilaEquiposCliente({{ $equipo->id }})">
                                            @else
                                            <tr style="font-size: 9pt;">
                                            @endif
                                        @endif
                                            <td class="d-none"> {{ $equipo->id }}</td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {!! $tipoEquipo !!}
                                            </td>
                                            @if($equipo->marca->id_tipo_equipo === $equipo->id_tipo)
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $nombreMarca }}
                                            </td>
                                            @else
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                *****
                                            </td>
                                            @php
                                                $hayItemsInexistentes = true;
                                            @endphp
                                            @endif

                                            @if($equipo->modelo->id_marca === $equipo->marca->id)
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $nombreModelo }}
                                            </td>
                                            @else
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                *****
                                            </td>
                                            @php
                                                $hayItemsInexistentes = true;
                                            @endphp
                                            @endif
                                            @if ($modalSoloLectura)
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $equipo->disponible ? 'DISPONIBLE' : 'NO DISPONIBLE' }}
                                            </td>
                                            {{-- <td>
                                                <a wire:click="abrirCreditoTaller({{ $equipo->id }})" title="Ir a Equipos del Cliente" style="color: dimgrey; cursor:pointer;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'">
                                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                </a>
                                            </td> --}}
                                            @endif
                                        </tr>
                                        @endforeach
                                    @endif
                                    @if($hayItemsNoDisponibles || $hayItemsInexistentes)
                                    <div class="col-md-10">
                                        <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">                                        @if ($hayItemsNoDisponibles)* NO DISPONIBLE @endif @if ($hayItemsInexistentes) &nbsp; ***** INEXISTENTE @endif
                                        </label>
                                    </div>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if (!$modalSoloLectura)
                        <div class="col-md-1 mb-3" wire:loading.remove>
                            <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="nuevoEquipoCliente" title="Nuevo Equipo">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        @endif
                        {{-- @if($hayItemsNoDisponibles || $hayItemsInexistentes)
                        <div class="col-md-10">
                            <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                            @if ($hayItemsNoDisponibles)* NO DISPONIBLE @endif @if ($hayItemsInexistentes) &nbsp; ***** INEXISTENTE @endif
                            </label>
                        </div>
                        @endif --}}
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarEquiposClienteModal" wire:click="cierraModalEquiposCliente">Cerrar</button>
                </div>
           </div> 
       </div>
   </div>
</div>
