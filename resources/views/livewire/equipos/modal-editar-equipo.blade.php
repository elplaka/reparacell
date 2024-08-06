@php
    use App\Models\MarcaEquipo;
    use App\Models\ModeloEquipo;
@endphp
<div wire:ignore.self class="modal fade" id="editarEquipoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" style="display:{{ $datosCargados ? 'block' : 'none' }}">
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Editar equipo</b></h1> &nbsp;&nbsp; <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraEquipoModal">
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
               <div class="container mt-3">
                    <div class="row mb-3">
                        <label for="equipoModidTipo" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Tipo Equipo') }} </label> 
                        <div class="col-md-9 d-flex">
                            <select wire:model.live="equipoMod.idTipo" class="selectpicker select-picker w-100" id="equipoModidTipo" style="font-size: 11pt;">
                                @foreach ($tiposEquipos as $tipo_equipo)
                                    <option value="{{ $tipo_equipo->id }}" data-content="{{ $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="equipoModidMarca" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Marca') }} </label> 
                        <div class="col-md-9 d-flex">
                        <select wire:model.live="equipoMod.idMarca" type="text" class="select-height form-control select-hover" id="equipoModidMarca" style="font-size: 11pt;" 
                        wire:key="{{ $equipoMod['idTipo'] }}"
                        >
                        <option value="0">--SELECCIONA--</option>
                        @foreach (MarcaEquipo::where('id_tipo_equipo', $this->equipoMod['idTipo'])->where('disponible', 1)->orderBy('nombre')->get() as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="equipoModidModelo" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Modelo') }} </label> 
                        <div class="col-md-9 d-flex">
                        <select wire:model.live="equipoMod.idModelo" type="text" class="select-height form-control select-hover" id="equipoModidModelo" style="font-size: 11pt;" 
                        wire:key="{{ $equipoMod['idMarca'] }}"
                        >
                        <option value="0" class="custom-option">--SELECCIONA--</option>
                        {{-- @foreach ($modelosMod as $modelo) --}}
                        @foreach (ModeloEquipo::where('id_marca', $this->equipoMod['idMarca'])
                        ->where('disponible', 1)->whereHas('marca', function($query) {
                            $query->where('id_tipo_equipo', $this->equipoMod['idTipo']);
                        })->orderBy('nombre')->get() as $modelo)
                            <option value="{{ $modelo->id }}"> {{ $modelo->nombre }}</option>
                        @endforeach
                        </select>
                        </div>
                    </div>
                    @if ($busquedaClienteHabilitada)
                        @if (strlen(trim($equipoMod['nombreCliente'])) > 0)
                        <div class="row mb-0">
                        @else
                        <div class="row mb-3">
                        @endif
                    @else
                    <div class="row mb-3">
                    @endif
                        <label for="equipoModidCliente" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Cliente') }} </label> 
                        <div class="col-md-9 d-flex">
                        <input type="text" class="form-control input-height w-100" wire:model.live='equipoMod.nombreCliente' style="font-size:11pt;" @if(!$busquedaClienteHabilitada) disabled @endif placeholder="Escribe el cliente a buscar"> &nbsp;
                        @if ($busquedaClienteHabilitada)
                        <button wire:click="desHabilitaBusquedaCliente" class="btn btn-danger" style="font-size: 10pt; display: flex; align-items: center;" title="Descartar"><i class="fa-solid fa-xmark"></i></button>
                        @else
                        <button wire:click="habilitaBusquedaCliente" class="btn btn-secondary" style="font-size: 10pt; display: flex; align-items: center;"><i class="fa-solid fa-magnifying-glass"></i></button>
                        @endif         
                        </div>
                    </div>
                    @if ($busquedaClienteHabilitada)
                        @if (strlen(trim($equipoMod['nombreCliente'])) > 0)
                        <div class="row mb-3">
                            <label for="equipoModidCliente" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;"></label> 
                            <div class="col-md-9 d-flex">
                            <select id="equipoModidClienteSel" size="4" type="text" class="form-control" style="font-size: 11pt;">
                                @foreach ($clientesMod as $cliente)
                                <option value="{{ $cliente->id }}" wire:click="seleccionaCliente('{{ $cliente->id }}', '{{ $cliente->nombre }}')" style="background-color: #ffffff; cursor: pointer;" onmouseover="this.style.backgroundColor='#f0f0f0';" onmouseout="this.style.backgroundColor='#ffffff';">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select> &nbsp;
                            </div>
                        </div>
                        @endif
                    @endif
               </div>
               <div class="modal-footer d-flex justify-content-center">
                   <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="actualizaEquipo" @disabled($busquedaClienteHabilitada)>Actualizar</button>
                   <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="cierraEquipoModal">Cerrar</button>
               </div>
           </div>
       </div>
   </div>
</div>