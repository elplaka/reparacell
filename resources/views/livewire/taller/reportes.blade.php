@php
    use Carbon\Carbon;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.taller.modal-param-marcas')
    @include('livewire.taller.modal-param-modelos')
    @include('livewire.taller.modal-param-fallas')


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
    <div class="row mb-2">
        <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <b> Fecha de Entrada </b>
        </div>
         <div class="col-md-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
            <b> Tipo(s) de Equipo </b>
        </div>
       <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
            <b>Marca(s)</b>
        </div>
       <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
           <b> Modelo(s) </b>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <div class="row align-items-center">
                &nbsp; &nbsp;  Del &nbsp;
                <input type="date" wire:model.live="busquedaEquipos.fechaEntradaInicio" class="col-md-4 input-height form-control" style="font-size:11pt">
                &nbsp; al &nbsp;
                <input type="date" wire:model.live="busquedaEquipos.fechaEntradaFin" class="col-md-4 input-height form-control" style="font-size:11pt">
            </div>
        </div>
        
        <div class="col-md-2 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <select wire:model.live="busquedaEquipos.idTipos" id="selectIdTipos" class="selectpicker select-picker w-100" title='--TODOS--' multiple>
                @foreach ($tipos_equipos as $tipo_equipo)
                    <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                @endforeach
            </select>
        </div>

        @if($marcasDiv)
        <div class="col-md-3 mb-3" data-toggle="modal" data-target="#paramMarcasModal" wire:click="abreParamMarcasModal">
            <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-96" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
            @foreach($marcasDiv as $marca)
            <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt">{!! $marca->tipoEquipo->icono !!} &nbsp; {{ $marca->nombre }} 
            <a href="#" wire:click.prevent="eliminarMarca({{ $marca->id }})" onclick="event.stopPropagation();">×</a></span>
            @endforeach
            </div>
        </div>       
        @else
        <div class="col-md-3 mb-3">
            <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramMarcasModal">
            --TODAS--
            </div>
        </div>
        @endif

        @if($modelosDiv)
        <div class="col-md-3 mb-4" data-toggle="modal" data-target="#paramModelosModal" wire:click="abreParamModelosModal">
            <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-96" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
            @foreach($modelosDiv as $modelo)
            <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt">{!! $modelo->marca->tipoEquipo->icono !!} &nbsp; {{ $modelo->nombre }}  &nbsp; [ {{ $modelo->marca->nombre }} ]
            <a href="#" wire:click.prevent="eliminarModelo({{ $modelo->id }})" onclick="event.stopPropagation();">×</a></span>
            @endforeach
            </div>
        </div>       
        @else
        <div class="col-md-3 mb-3">
            <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramModelosModal">
            --TODOS--
            </div>
        </div>
        @endif
    </div>
    <hr>
    <div class="row mb-2">
        <div class="col-md-4 mt-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <b> Falla(s) </b>
        </div>
        {{-- <div class="col-md-2 mt-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
            <b> Cliente(s) </b>
        </div>
       <div class="col-md-3 mt-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
            <b> Recibió</b>
        </div>
       <div class="col-md-3 mt-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
           <b> Estatus </b>
        </div> --}}
    </div>
    <div class="row">
        @if($fallasDiv)
        <div class="col-md-4 mb-4" data-toggle="modal" data-target="#paramFallasModal" wire:click="abreParamFallasModal">
            <div class="d-flex flex-wrap text-xs leading-4 font-bold text-gray-700 tracking-wider w-96" style="border: 1px solid rgb(93, 90, 90); font-size: 11pt; cursor: pointer;">
            @foreach($fallasDiv as $falla)
            <span class="badge badge-secondary m-1" onclick="event.stopPropagation();" style="height:1.5em; font-weight:normal; font-size: 10pt">{!! $falla->tipoEquipo->icono !!} &nbsp; {{ $falla->descripcion }} 
            <a href="#" wire:click.prevent="eliminarFalla({{ $falla->id }})" onclick="event.stopPropagation();">×</a></span>
            @endforeach
            </div>
        </div>       
        @else
        <div class="col-md-4 mb-3">
            <div class="text-xs leading-4 font-bold text-gray-700 tracking-wider" style="border: 1px solid rgb(93, 90, 90); padding-top: 5px; padding-left: 10px; font-size: 11pt; cursor: pointer; height:2em;" data-toggle="modal" data-target="#paramFallasModal">
            --TODAS--
            </div>
        </div>
        @endif
    </div>

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
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">RECIBIÓ</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
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
                        $equipos++;
                    @endphp
                    <tr style="font-size: 10pt;" class="custom-status-color-{{ $taller->estatus->id }}" data-toggle="tooltip" data-title="">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="2%">
                            {!! $taller->equipo->tipo_equipo->icono !!}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->num_orden }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->fecha_entrada->format('d/m/Y') }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->marca->nombre }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->modelo->nombre }}</td>

                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @foreach ($taller->fallas as $key => $equipo)
                                {{ $equipo->falla->descripcion }}
                                @if (!$loop->last) <!-- Verifica si no es el último elemento -->
                                    |
                                @endif
                            @endforeach
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->equipo->cliente->nombre }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->usuario->name }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; text-align: center">
                        <span title="{{ $taller->estatus->descripcion }}">&nbsp; {!! $this->obtenerIconoSegunEstatus($taller->id_estatus) !!}  &nbsp; </span>
                        </td>                                         
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            {{-- @if (!$taller->cobroTaller)  
                            <button wire:click="cobroFinalEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="cobroFinalEquipoTaller" class="label-button"
                            ><i class="fa-solid fa-hand-holding-dollar" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'" title="Cobrar"></i></button> <span wire:loading wire:target="cobroFinalEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            @endif
                            <button wire:click="cobroEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="cobroEquipoTaller" class="label-button"><i class="fa-solid fa-print" style="color:dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'" title="Imprimir recibo"></i></button> <span wire:loading wire:target="cobroEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            @if ($taller->id_estatus >= 5)
                            <button wire:click="invierteCobroEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="invierteCobroEquipoTaller" class="label-button">
                            @if (isset($taller->cobroTaller))
                                @if($taller->cobroTaller->cancelado)
                                <i class="fa-solid fa-sack-dollar" style="color:dimgrey;" onmouseover="this.style.color='green'" onmouseout="this.style.color='dimgrey'" title="Activar cobro"></i>
                                @else
                                <i class="fa-solid fa-sack-xmark" style="color:dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Cancelar cobro"></i>
                                @endif
                            @endif
                            </button> <span wire:loading wire:target="invierteCobroEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            @endif
                            <span> &nbsp;</span>
                            <a onclick="openWhatsApp({{ $taller->equipo->cliente->telefono_contacto }})" style="cursor: pointer; text-decoration: none;" target="_blank" href="javascript:void(0);" title="{{ $taller->equipo->cliente->telefono_contacto }}" >
                                <i class="fab fa-whatsapp" style="color: dimgrey;" onmouseover="this.style.color='rgb(50, 212, 64)'" onmouseout="this.style.color='dimgrey'"></i>
                            </a>
                            <span wire:loading wire:target="abrirWhatsApp" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span> &nbsp;</span>
                            <button data-toggle="modal" data-target="#anotacionesModal" wire:click="anotacionesModal({{ $taller->num_orden }})" class="label-button">
                                @if($taller->anotacionEquipoTaller)
                                    <i class="fa-solid fa-comment-dots" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"  title="Ver anotaciones"></i>
                                @else
                                    <i class="fa-solid fa-comment-medical" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"  title="Agregar anotaciones"></i>
                                @endif
                            </button>
                            @if (isset($taller->cobroTallerCredito) && $taller->id_estatus >= 5)
                            <button wire:click="abreCobroCredito({{ $taller->num_orden }})" wire:loading.remove wire:target="abreCobroCredito" class="label-button">
                            <i class="fa-solid fa-credit-card" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"  title="Créditos"></i>
                            </button>
                            @endif --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <div class="w-100 d-flex justify-content-end align-items-center mb-4">
        <x-button id="botonCorteCaja" data-toggle="modal" data-target="#corteCajaModal" class="ml-md-4 align-self-center">
            <i class="fa-solid fa-file-invoice-dollar"></i> &nbsp; Corte de Caja [F10]
        </x-button>
        </div> --}}
    </div>
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $equipos_taller->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>
    function eliminarMarca(id) {
// Aquí se puede agregar la lógica para eliminar la marca utilizando Livewire
console.log('puro Livewire', id);
Livewire.dispatch('eliminarMarca', id);
}
</script>