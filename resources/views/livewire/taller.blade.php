@php
    use Carbon\Carbon;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-cobro-taller')
    @include('livewire.modal-anotaciones')
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
    @livewire('agrega-equipo-taller')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-screwdriver-wrench"></i> Taller</b></h4>
            <span wire:loading class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </div>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" data-toggle="collapse" href="#collapseAgregaEquipoTaller" aria-controls="collapseAgregaEquipoTaller" wire:click="abreAgregaEquipo" title="Agregar equipo" wire:loading.attr="disabled" wire:target="abreAgregaEquipo" onclick="ocultarBoton()">
            <i class="fas fa-plus"></i>
        </a>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3" wire:ignore>
            <label class="col-md-4 text-xs leading-4 font-bo_ld text-gray-700 uppercase tracking-wider" style="font-size: 11pt;"> <strong> TIPO EQUIPO </strong> </label>
            <div class="col-md-8">
                <select wire:model.live="busquedaEquipos.idTipo" class="selectpicker select-picker w-100" title='--TODOS--' multiple>
                    @foreach ($tipos_equipos as $tipo_equipo)
                        <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex justify-content-center">
                <label class="text-center text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;">
                    <strong>FECHA DE ENTRADA&nbsp;&nbsp;&nbsp;&nbsp;</strong> 
                </label>
            </div>
            <div class="form-inline">
                <label class="block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">  Del &nbsp;</label>
                <input type="date" wire:model.live="busquedaEquipos.fechaEntradaInicio" class="col-md-4 input-height form-control" style="font-size:11pt">
                <span class="mx-2"> <label class="block text-sm-right text-gray-700 pr-0" style="font-size:11pt;"> al </label> </span>
                <input type="date" wire:model.live="busquedaEquipos.fechaEntradaFin" class="col-md-4 input-height form-control" style="font-size:11pt">
            </div>
        </div>
        <div class="col-md-4 mb-3" wire:ignore>
            <label class="col-md-4 text-xs leading-4 font-bo_ld text-gray-700 uppercase tracking-wider" style="font-size: 11pt;"> <strong> ESTATUS </strong> </label>
            <div class="col-md-8">
                <select wire:model.live="busquedaEquipos.entregados" id="selectEntregados" title="--TODOS--" class="selectpicker select-picker w-100" multiple>
                    <optgroup label="Entrega">
                        <option value="entregados">ENTREGADOS</option>
                        <option value="no_entregados">NO ENTREGADOS</option>
                    </optgroup>
                    @foreach ($estatus_equipos as $estatus)
                        <option value="{{ $estatus->id }}" data-content="{{  $this->obtenerIconoEstatus($estatus->id) }} &nbsp; {{ $estatus->descripcion }}"></option>
                    @endforeach                    
                </select>
            </div>
        </div>
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
                        @if ($taller->id_estatus >= 2 && $taller->id_estatus <= 4)
                            <i wire:loading.remove class="fa-solid fa-caret-left" wire:click="anteriorEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})" style="cursor:pointer" title="{!! $this->toolTipAnteriorEstatus($taller->id_estatus) !!}"></i>
                            <span wire:loading wire:target="anteriorEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})"><i class="fa fa-spinner fa-spin"></i>
                            </span>
                        @else
                            <span> &nbsp;</span>
                        @endif
                        <span title="{{ $taller->estatus->descripcion }}">&nbsp; {!! $this->obtenerIconoSegunEstatus($taller->id_estatus) !!}  &nbsp; </span>
                        @if ($taller->id_estatus <= 3)
                            <i wire:loading.remove class="fa-solid fa-caret-right" wire:click="siguienteEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})" style="cursor:pointer" title="{!! $this->toolTipSiguienteEstatus($taller->id_estatus) !!}"></i>
                            <span wire:loading wire:target="siguienteEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})"><i class="fa fa-spinner fa-spin"></i>
                            </span>
                        @else
                            <span> &nbsp;</span>
                        @endif
                        </td>                                         
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if (!$taller->cobroTaller)  {{-- Si no hay cobro que muestre el botón para editar y cobrar --}}
                            @if(!$muestraDivAgregaEquipo)
                            <a id="botonEditaEquipo" class="botonEditaEquipo" data-toggle="collapse" href="#collapseAgregaEquipoTaller" aria-controls="collapseEditaEquipoTaller" wire:click="editaEquipoTaller({{ $taller->num_orden }})" title="Editar equipo en taller" wire:loading.attr="disabled" wire:target="editaEquipoTaller" style="color: dimgrey;" onclick="ocultarBoton()">
                                <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                            </a>
                            <span wire:loading wire:target="editaEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            @endif
                            {{-- Este botón abre la ventana modal mediante Javascript y no por bootstrap --}}
                            <button wire:click="cobroFinalEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="cobroFinalEquipoTaller" class="label-button"
                            {{-- data-toggle="modal" data-target="#cobroTallerModal" --}}
                            ><i class="fa-solid fa-hand-holding-dollar" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i></button> <span wire:loading wire:target="cobroFinalEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            @endif
                            <button wire:click="cobroEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="cobroEquipoTaller" class="label-button"><i class="fa-solid fa-print" style="color:dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i></button> <span wire:loading wire:target="cobroEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            @if ($taller->id_estatus >= 5)
                            <button wire:click="invierteCobroEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="invierteCobroEquipoTaller" class="label-button">
                            @if (isset($taller->cobroTaller->cancelado))
                                <i class="fa-solid fa-sack-dollar" style="color:dimgrey;" onmouseover="this.style.color='green'" onmouseout="this.style.color='dimgrey'" title="Activar cobro"></i>
                            @else
                                <i class="fa-solid fa-sack-xmark" style="color:dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Cancelar cobro"></i>
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $equipos_taller->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>
    $(document).ready(function () {
        $('#cobroTallerModal').on('shown.bs.modal', function () {
            // Refrescar el selectpicker
            $('.selectpicker').selectpicker('refresh');            
        });
    });

    function abreModalBuscarCliente() {
    $('#buscarClienteModal').modal('show');
}

document.addEventListener('livewire:initialized', function () {
        Livewire.hook('element.updated', () => {
            $('#btnBuscarCliente').click(); // Simular clic para abrir la modal
        });
    });

    function descartarEquipo() {
        $('#collapseAgregaEquipoTaller').collapse('hide');
        document.getElementById('botonAgregar').style.display = 'block';
        console.log('Descartar Equipo');
    }

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('ocultaDivAgregaEquipo', function () {
            $('#collapseAgregaEquipoTaller').collapse('hide'); // Cierra el Collapse
            document.getElementById('botonAgregar').style.display = 'block';
            console.log('OcultaDivAgregaEquipo');
        });
    });

    $(document).ready(function () {
        $('#selectEntregados').selectpicker('refresh');
    });

    function ocultarBoton() {
        document.getElementById('botonAgregar').style.display = 'none';
    }

    function openWhatsApp(phoneNumber) {
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        var url = isMobile ? "https://api.whatsapp.com/send?phone=" : "https://web.whatsapp.com/send?phone=";

        window.open(url + phoneNumber, "_blank");
    }
</script>




