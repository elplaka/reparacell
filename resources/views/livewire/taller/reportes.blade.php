@php
    use Carbon\Carbon;
    $hayNoDisponibles = false;
    $hayInexistentes = false;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased ">
    @include('livewire.taller.modal-param-marcas')
    @include('livewire.taller.modal-param-modelos')
    @include('livewire.taller.modal-param-fallas')
    @include('livewire.taller.modal-param-clientes')
    @include('livewire.taller.modal-editar-modo-pago')

    @if ($showMainErrors)
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
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
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-file-invoice"></i> Reportes de Reparaciones</b></h4>
            <span wire:loading class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </div>
    </div>

<div
    wire:loading
    class="fixed top-0 left-0 w-screen h-screen z-[9999] flex items-center justify-center"
    style="top: 0; left: 0; position: fixed;
    width: 100vw;
        height: 100vh;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 9999;">
    <div class="flex flex-col items-center">
        <svg class="animate-spin w-12 h-12 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span class="mt-3 text-lg font-medium text-gray-600">Procesando...</span>
    </div>
</div>

    <div class="w-100">     
        <!-- Encabezados visibles solo en pantallas medianas y grandes -->
        <div class="row mb-2 d-none d-md-flex">
            <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Fecha de Entrada</b>
            </div>
            <div class="col-md-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Tipo(s) de Equipo</b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Marca(s)</b>
            </div>
             @if (!$verGrafico)
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Modelo(s)</b>
            </div>
            @endif
        </div>
    
        <!-- Fila de campos -->
        <div class="row">
            <!-- Fecha de Entrada -->
            <div class="col-12 col-md-4 mb-2">
                <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Fecha de Entrada</label>
                <div class="row">
                    <div class="col-12 col-md-6 d-flex align-items-center mb-2">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">Del</label>
                        <input type="date" wire:model.live="busquedaEquipos.fechaEntradaInicio" class="input-height form-control" style="font-size:11pt;">
                    </div>
                    <div class="col-12 col-md-6 d-flex align-items-center mb-2">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">&nbsp;&nbsp;Al</label>
                        <input type="date" wire:model.live="busquedaEquipos.fechaEntradaFin" class="input-height form-control" style="font-size:11pt;">
                    </div>
                </div>
            </div>
    
            <!-- Tipo(s) de Equipo -->
            <div class="col-12 col-md-2 mb-2">
                <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Tipo(s) de Equipo</label>
                <select wire:model.live="busquedaEquipos.idTipos" id="selectIdTipos" class="selectpicker select-picker w-100" title='--TODOS--' multiple>
                    @foreach ($tipos_equipos as $tipo_equipo)
                        <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                    @endforeach
                </select>
            </div>
    
            <!-- Marca(s) -->
            @if($marcasDiv)
            <div class="col-12 col-md-3 mb-3" data-toggle="modal" data-target="#paramMarcasModal" wire:click="abreParamMarcasModal">
                <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Marca(s)</label>
                <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-100 hover-bg" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
                    @foreach($marcasDiv as $marca)
                    <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt">{!! $marca->tipoEquipo->icono !!} &nbsp; {{ $marca->nombre }} 
                    <a href="#" wire:click.prevent="eliminarMarca({{ $marca->id }})" onclick="event.stopPropagation();" style="text-decoration: none;">×</a></span>
                    @endforeach
                </div>
            </div>
            @else
            <div class="col-12 col-md-3 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Marca(s)</label>
                <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider hover-bg" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramMarcasModal">
                    --TODAS--
                </div>
            </div>
            @endif
    
            <!-- Modelo(s) -->
            @if (!$verGrafico)
                @if($modelosDiv)
                <div class="col-12 col-md-3 mb-4" data-toggle="modal" data-target="#paramModelosModal" wire:click="abreParamModelosModal">
                    <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Modelo(s)</label>
                    <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-100 hover-bg" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
                        @foreach($modelosDiv as $modelo)
                        <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt">{!! $modelo->marca->tipoEquipo->icono !!} &nbsp; {{ $modelo->nombre }}  &nbsp; [ {{ $modelo->marca->nombre }} ]
                        <a href="#" wire:click.prevent="eliminarModelo({{ $modelo->id }})" onclick="event.stopPropagation();" style="text-decoration: none;">×</a></span>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="col-12 col-md-3 mb-3">
                    <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Modelo(s)</label>
                    <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider hover-bg" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramModelosModal">
                        --TODOS--
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>   
    <hr>
    <div class="w-100 mt-2">     
        <div class="row mb-2 d-none d-md-flex">
            <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider mb-01" style="font-size: 11pt;">
                <b> Falla(s) </b>
            </div>
            <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider mb-0" style="font-size: 11pt;">
                <b> Cliente(s) </b>
            </div>
            @if($chkFechaSalida)
            <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <input type="checkbox" wire:model.live="chkFechaSalida"> <b> Fecha de Salida </b>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700 mb-0" style="font-size: 11pt;">Falla(s)</label>
                @if($fallasDiv)
                <div data-toggle="modal" data-target="#paramFallasModal" wire:click="abreParamFallasModal">
                    <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-100 hover-bg" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
                        @foreach($fallasDiv as $falla)
                        <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt">{!! $falla->tipoEquipo->icono !!} &nbsp; {{ $falla->descripcion }} 
                        <a href="#" wire:click.prevent="eliminarFalla({{ $falla->id }})" onclick="event.stopPropagation();" style="text-decoration: none;">×</a></span>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider hover-bg" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramFallasModal">
                    --TODAS--
                </div>
                @endif
            </div>
    
            <div class="col-12 col-md-4 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700 mb-0" style="font-size: 11pt;">Cliente(s)</label>
                @if($clientesDiv)
                <div data-toggle="modal" data-target="#paramClientesModal" wire:click="abreParamClientesModal">
                    <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-100 hover-bg" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
                        @foreach($clientesDiv as $cliente)
                        <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt"> {{ $cliente->nombre }} 
                        <a href="#" wire:click.prevent="eliminarCliente({{ $cliente->id }})" onclick="event.stopPropagation();" style="text-decoration: none;">×</a></span>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider hover-bg" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramClientesModal" wire:click="abreParamClientesModal">
                    --TODOS--
                </div>
                @endif
            </div>
    
            @if($chkFechaSalida)
            <div class="col-12 col-md-4 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700 mb-2" style="font-size: 11pt;">Fecha de Salida</label>
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">Del</label>
                        <input type="date" wire:model.live="busquedaEquipos.fechaSalidaInicio" class="input-height form-control" style="font-size:11pt;">
                    </div>
                    <div class="col-12 col-md-6 mb-2 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">&nbsp;&nbsp;Al</label>
                        <input type="date" wire:model.live="busquedaEquipos.fechaSalidaFin" class="input-height form-control" style="font-size:11pt;">
                    </div>
                </div>
            </div>
            @else
            <div class="col-12 col-md-4 mt-2 text-xs leading-4 font-bold text-gray-700 tracking-wider mb-2" style="font-size: 11pt;">
                <input type="checkbox" wire:model.live="chkFechaSalida"> <b> Fecha de Salida </b>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="col-2 col-md-2 mt-2 text-xs leading-4 font-bold text-gray-700 tracking-wider mb-2" style="font-size: 11pt;">
                <input type="checkbox" wire:model.live="chkAgrupar"> <b> Agrupar por modelo </b>
            </div>
        </div>
    </div>
    <div class="{{ $verGrafico && $chkAgrupar ? '' : 'd-none' }}">
        <div class="card">
            <div class="card-body">
                <div style="position: relative; width: 100%;">
                    <canvas id="graficaBarras" style="width: 100% !important; height: 70px !important;"></canvas>
                </div>
            </div>
        </div>

        <div class="w-100 d-flex justify-content-end align-items-center mb-1 mt-2">
            <x-button id="botonCorteCaja" wire:click='muestraTabla' data-toggle="modal" data-target="#corteCajaModal" class="ml-md-4 align-self-center">
                <i class="fa-solid fa-table-list"></i> &nbsp; Tabla
            </x-button> &nbsp; 
        </div>
    </div>
    @if (!$verGrafico)
    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider"></th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">#</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FEC. ENTRADA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MARCA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MODELO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FALLA(S)</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CLIENTE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">COBRO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">RECIBIÓ</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    @if ($chkFechaSalida == true)
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FEC. SALIDA</i></th>
                    @endif
                </tr>
            </thead>
            {{-- <tbody wire:poll> --}}
            <tbody>
                @php
                    $equipos = 0;
                @endphp
                @foreach ($equipos_taller as $taller)
                    @php
                        $taller->fecha_entrada = Carbon::parse($taller->fecha_entrada);
                        $taller->fecha_salida = Carbon::parse($taller->fecha_salida);
                        $equipos++;
                    @endphp
                    {{-- PREGUNTA SI EL MODELO EXISTE DENTRO DE LA MARCA --}}
                    @if(($taller->equipo->modelo->id_marca === $taller->equipo->marca->id)) 
                    <tr style="font-size: 10pt;" class="custom-status-color-{{ $taller->estatus->id }}" data-toggle="tooltip" data-title="">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="2%">
                            {!! $taller->equipo->tipo_equipo->icono !!}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->num_orden }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->fecha_entrada->format('d/m/Y') }}</td>
                        @if($taller->equipo->marca->id_tipo_equipo === $taller->equipo->id_tipo)
                            @if($taller->equipo->marca->disponible)
                                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->marca->nombre }}</td>
                            @else
                                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->marca->nombre . '*' }}</td>
                                @php
                                    $hayNoDisponibles = true;
                                @endphp
                            @endif
                        @else
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">*****</td>
                            @php
                                $hayInexistentes = true;
                            @endphp
                        @endif
                        @if($taller->equipo->modelo->id_marca === $taller->equipo->marca->id)
                            @if($taller->equipo->modelo->disponible)
                                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->modelo->nombre }}</td>
                            @else
                                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->modelo->nombre . '*' }}</td>
                                @php
                                    $hayNoDisponibles = true;
                                @endphp
                            @endif
                        @else
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">*****</td>
                        @php
                            $hayInexistentes = true;
                        @endphp
                        @endif
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @foreach ($taller->fallas as $key => $equipo)
                            @if($equipo->falla->id_tipo_equipo === $taller->equipo->id_tipo)
                                @if($equipo->falla->disponible)
                                    {{ $equipo->falla->descripcion }}
                                @else
                                    {{ $equipo->falla->descripcion . "*" }}
                                    @php
                                        $hayNoDisponibles = true;
                                    @endphp
                                @endif
                                @if (!$loop->last) <!-- Verifica si no es el último elemento -->
                                    |
                                @endif
                            @else
                                @php
                                    $hayInexistentes = true;
                                @endphp
                                *****
                            @endif
                            @endforeach
                        </td>
                        @if($taller->equipo->cliente->disponible)
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->cliente->nombre }}</td>
                        @else
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->cliente->nombre . '*' }}</td>
                        @php
                            $hayNoDisponibles = true;
                        @endphp
                        @endif
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            {{-- Check if the cobroTaller relationship exists --}}
                            @if ($taller->cobroTaller)
                                {{-- If cobroTaller exists, display the realiado amount --}}
                                $ &nbsp; {{ $taller->cobroTaller->cobro_realizado }}

                                {{-- Then check for the credit status on the existing cobroTaller --}}
                                @if ($taller->cobroTaller->credito)
                                    [C]
                                @else
                                    &nbsp;
                                    {{-- Also check if the modoPago relationship exists on the existing cobroTaller --}}
                                    @if ($taller->cobroTaller->modoPago)
                                        <i class='{{ $taller->cobroTaller->modoPago->icono }}' wire:click="abrirEditarModoPagoModal({{ $taller->num_orden }})" style="cursor: pointer;"></i>
                                    @endif
                                @endif
                            @else
                                {{-- If cobroTaller is null, display something else, e.g., a dash or "Pending" --}}
                                - {{-- Or leave this section empty: --}}{{-- &nbsp; --}}
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->usuario->name }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; text-align: center">
                        <span title="{{ $taller->estatus->descripcion }}">&nbsp; {!! $this->obtenerIconoSegunEstatus($taller->id_estatus) !!}  &nbsp; </span>
                        </td>
                        @if ($chkFechaSalida == true)
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            {{ $taller->fecha_salida->format('d/m/Y') }}
                        </td>
                        @endif
                    </tr>
                    {{-- SI NO EXISTE EL MODELO DENTRO DE LA MARCA LO VA A MOSTRAR SIEMPRE Y CUANDO
                    NO SE ESTÉ BUSCANDO DICHO MODELO --}}
                    @else
                        @if (isset($this->busquedaEquipos['idModelos']) && $this->busquedaEquipos['idModelos'] != [])
                        @else
                        <tr style="font-size: 10pt;" class="custom-status-color-{{ $taller->estatus->id }}" data-toggle="tooltip" data-title="">
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="2%">
                                {!! $taller->equipo->tipo_equipo->icono !!}  
                            </td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->num_orden }}</td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->fecha_entrada->format('d/m/Y') }}</td>
                            @if($taller->equipo->marca->id_tipo_equipo === $taller->equipo->id_tipo)
                                @if($taller->equipo->marca->disponible)
                                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->marca->nombre }}</td>
                                @else
                                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->marca->nombre . '*' }}</td>
                                    @php
                                        $hayNoDisponibles = true;
                                    @endphp
                                @endif
                            @else
                                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">*****</td>
                                @php
                                    $hayInexistentes = true;
                                @endphp
                            @endif
                            @if($taller->equipo->modelo->id_marca === $taller->equipo->marca->id)
                                @if($taller->equipo->modelo->disponible)
                                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->modelo->nombre }}</td>
                                @else
                                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->modelo->nombre . '*' }}</td>
                                    @php
                                        $hayNoDisponibles = true;
                                    @endphp
                                @endif
                            @else
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">*****</td>
                            @php
                                $hayInexistentes = true;
                            @endphp
                            @endif
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                                @foreach ($taller->fallas as $key => $equipo)
                                    {{ $equipo->falla->descripcion }}
                                    @if (!$loop->last) <!-- Verifica si no es el último elemento -->
                                        |
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->cliente->nombre }}</td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">$ &nbsp; {{ $taller->cobroTaller->cobro_realizado }} </td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->usuario->name }}</td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; text-align: center">
                            <span title="{{ $taller->estatus->descripcion }}">&nbsp; {!! $this->obtenerIconoSegunEstatus($taller->id_estatus) !!}  &nbsp; </span>
                            </td>
                            @if ($chkFechaSalida == true)
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                                {{ $taller->fecha_salida->format('d/m/Y') }}
                            </td>
                            @endif
                        </tr>
                        @endif
                    @endif
                @endforeach
            </tbody>
        </table>
        @endif
        @if ($hayNoDisponibles || $hayInexistentes)
        <div class="col-md-5">
            <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
               @if ($hayNoDisponibles)* NO DISPONIBLE @endif @if ($hayInexistentes) &nbsp; ***** INEXISTENTE @endif
            </label>
        </div>
    @endif
    </div>
    @if ($chkAgrupar)
    <div class="w-100 d-flex justify-content-end align-items-center mb-1">
        <x-button id="botonCorteCaja" wire:click='muestraGrafico' data-toggle="modal" data-target="#corteCajaModal" class="ml-md-4 align-self-center">
            <i class="fa-solid fa-chart-simple"></i> &nbsp; Gráfico
        </x-button> &nbsp; 
    </div>
    @endif
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $equipos_taller->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
            $('#selectModoPago8').selectpicker();

        $('#editarModoPagoModalCobroTaller').on('shown.bs.modal', function () {
            $('#selectModoPago8').selectpicker();

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                $('.selectpicker').selectpicker();

                succeed(({ snapshot, effect }) => {
                    $('#selectModoPago8').selectpicker('destroy');
                    $('#selectIdTipos').selectpicker('destroy');

                    queueMicrotask(() => {
                            $('#selectModoPago8').selectpicker('refresh');
                            $('#selectIdTipos').selectpicker('refresh'); 
                    });
                });

                fail(() => {
                    console.error('Livewire commit failed');
                });
            });
        });
    });

    

    document.addEventListener('livewire:initialized', function () {
        @this.on('cierraModalEditaModoPagoCobroTaller', () => {
            $('#editarModoPagoModalCobroTaller').modal('hide');
        });
    });

    document.addEventListener('livewire:initialized', function () {
        @this.on('abreModalEditaModoPagoCobroTaller', () => {
            $('#editarModoPagoModalCobroTaller').modal('show');
        });
    });

        
   document.addEventListener('DOMContentLoaded', function() {
   // Inicializar selectpicker

         $('#selectIdTipos').selectpicker();

        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            $('.selectpicker').selectpicker();
            succeed(({ snapshot, effect }) => {
                $('selectIdTipos').selectpicker('destroy');
                queueMicrotask(() => {
                    setTimeout(() => {
                        $('.selectpicker').selectpicker('refresh');
                    }, 10); //
                });
            });

            fail(() => {
                console.error('Livewire commit failed');
            });
        });
    });

    let chartInstance = null;

document.addEventListener('livewire:initialized', function () {
    Livewire.on('renderChart', ({ labels, valores }) => {
        const canvas = document.getElementById('graficaBarras');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        const noDataPlugin = {
            id: 'noData',
            afterDraw(chart) {
                if (chart.data.labels.length === 0 || chart.data.datasets[0].data.length === 0) {
                    const { ctx, chartArea } = chart;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#999';
                    ctx.font = 'bold 16px sans-serif';
                    ctx.fillText('No hay datos para mostrar', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
                    ctx.restore();
                }
            }
        };

        const resizeObserver = new ResizeObserver(() => {
            if (chartInstance) {
                chartInstance.resize();
            }
        });
        resizeObserver.observe(canvas);

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Reparaciones',
                    data: valores,
                    backgroundColor: '#dc3545'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        }
                    }
                }
            },
            plugins: [noDataPlugin] // 👈 activamos el plugin personalizado
        });
    });
});


</script>

