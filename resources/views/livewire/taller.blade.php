@php
    use Carbon\Carbon;
    $hayNoDisponibles = false;
    $hayInexistentes = false;
    $itemDisponible = true;
@endphp



<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-cobro-taller')
    @include('livewire.modal-anotaciones')
    @include('livewire.creditos.modal-taller')
    @include('livewire.taller.modal-corte-caja')
    @include('livewire.taller.modal-cambia-estatus-equipo-taller')
    @include('livewire.creditos.modal-editar-modo-pago-taller')

    <style>
        .swal2-cancel.btn-danger {
        background-color: #d33 !important;
        color: #fff !important;
        border: none !important;
    }
    </style>

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
    @livewire('agrega-equipo-taller')
     <div class="w-100">
        <div class="row align-items-center mb-4">
            <div class="col-12 col-md-6 d-flex align-items-center">
                <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-screwdriver-wrench"></i> Taller</b></h4>
                <span wire:loading class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </div>
            @if (!$muestraDivAgregaEquipo)
            <div class="col-12 col-md-6">
                <div class="row justify-content-end">
                    <div class="w-10">
                        <a wire:ignore.self id="botonAgregar" class="btn btn-primary w-100" data-toggle="collapse" href="#collapseAgregaEquipoTaller" aria-controls="collapseAgregaEquipoTaller" wire:click="abreAgregaEquipo" title="Agregar equipo" wire:loading.attr="disabled" wire:target="abreAgregaEquipo" onclick="ocultarBoton()">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>   
   
    @if (!$muestraDivAgregaEquipo)
    <div class="w-100">     
        <div class="row mb-2 d-none d-md-flex">
            <div class="col-md-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Tipo Equipo</b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Cliente</b>
            </div>
            <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Fecha de Entrada</b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Estatus</b>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-2 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Tipo Equipo</label>
                <select wire:model.live="busquedaEquipos.idTipo" id="selectTipoEquipo"  class="selectpicker select-picker w-100" title='--TODOS--' multiple>
                    @foreach ($tipos_equipos as $tipo_equipo)
                        <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Cliente</label>
                <input type="text" wire:model.live="busquedaEquipos.nombreCliente" class="col-md-10 input-height form-control" style="font-size:11pt">
            </div>
            <div class="col-12 col-md-4 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Fecha de Entrada</label>
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">Del</label>
                        <input type="date" wire:model.live="busquedaEquipos.fechaEntradaInicio" class="input-height form-control" style="font-size:11pt;">
                    </div>
                    <div class="col-12 col-md-6 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">&nbsp;&nbsp;Al</label>
                        <input type="date" wire:model.live="busquedaEquipos.fechaEntradaFin" class="input-height form-control" style="font-size:11pt;">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Estatus</label>
                <select wire:model.live="busquedaEquipos.entregados" id="selectEntregados" title="--TODOS--" class="selectpicker select-picker w-100" multiple>
                    <optgroup label="Entrega">
                        <option value="entregados">ENTREGADOS</option>
                        <option value="no_entregados">NO ENTREGADOS</option>
                    </optgroup>
                    @foreach ($estatus_equipos as $estatus)
                        @php
                            $icono = $this->obtenerIconoEstatus($estatus->id) ?? '';
                        @endphp
                        @if ($estatus_equipos->count())
                            <option value="{{ $estatus->id }}" data-content="{{ $icono }} &nbsp; {{ $estatus->descripcion }}"></option>
                        @else
                            <option disabled>No hay estatus disponibles</option>
                        @endif
                    @endforeach                    
                </select>
            </div>
        </div>
    </div>
    @endif
    
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
            @if ($muestraDivAgregaEquipo || 
                 $modalCobroFinalAbierta || 
                 $abreModalAnotaciones || 
                 $modalCambiarEstatusEquipoAbierta ||
                 $modalCorteCajaAbierta ||
                 $modalCobroCreditoTallerAbierta)
            <tbody>
            @else
            <tbody wire:poll.10s>
            @endif
                @php
                    $equipos = 0;
                @endphp
                @foreach ($equipos_taller as $taller)
                    @php
                        $taller->fecha_entrada = Carbon::parse($taller->fecha_entrada);
                        $equipos++;
                        $itemDisponible = true;
                    @endphp
                    <tr style="font-size: 10pt;" class="custom-status-color-{{ $taller->estatus->id }}" data-toggle="tooltip" data-title="">
                        @if($taller->equipo->tipo_equipo->disponible)
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="2%">
                            {!! $taller->equipo->tipo_equipo->icono !!}
                        </td>
                        @else
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="4%">
                        <span style="display: inline;">{!! $taller->equipo->tipo_equipo->icono !!}</span><span>*</span>
                        @php
                            $hayNoDisponibles = true;
                            $itemDisponible = false;
                        @endphp
                        </td>
                        @endif
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
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $taller->usuario->name }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; text-align: center">
                        {{-- <span>{{ $taller->id_estatus }}</span> --}}
                        <span wire:loading.remove wire:target="anteriorEstatus, siguienteEstatus">
                        @if ($taller->id_estatus >= 2 && $taller->id_estatus <= 4)
                            <i wire:loading.remove class="fa-solid fa-caret-left" wire:click="anteriorEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})" style="cursor:pointer" title="{!! $this->toolTipAnteriorEstatus($taller->id_estatus) !!}"></i>
                        @else
                            @if ($taller->id_estatus == 6)
                            <i wire:loading.remove class="fa-solid fa-caret-left" wire:click="anteriorEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})" style="cursor:pointer" title="{!! $this->toolTipAnteriorEstatus($taller->id_estatus) !!}"></i>
                            @endif
                        @endif
                        </span>
                        <span wire:loading wire:target="anteriorEstatus, siguienteEstatus" class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span title="{{ $taller->estatus->descripcion }}">&nbsp; {!! $this->obtenerIconoSegunEstatus($taller->id_estatus) !!}  &nbsp; </span>
                        <span wire:loading.remove wire:target="anteriorEstatus, siguienteEstatus">
                        @if ($taller->id_estatus <= 3)
                            <i wire:loading.remove class="fa-solid fa-caret-right" wire:click="siguienteEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})" style="cursor:pointer" title="{!! $this->toolTipSiguienteEstatus($taller->id_estatus) !!}"></i>
                        @else
                            @if ($taller->id_estatus == 5)
                            <i wire:loading.remove class="fa-solid fa-caret-right" wire:click="siguienteEstatus({{ $taller->num_orden }} , {{ $taller->id_estatus }})" style="cursor:pointer" title="{!! $this->toolTipSiguienteEstatus($taller->id_estatus) !!}"></i>
                            @endif
                        @endif
                        </span>
                        <span wire:loading wire:target="anteriorEstatus, siguienteEstatus" class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </td>                                         
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if (!$taller->cobroTaller)  {{-- Si no hay cobro que muestre el botón para editar y cobrar --}}
                            @if(!$muestraDivAgregaEquipo)
                                @if($itemDisponible)
                                    <a id="botonEditaEquipo" class="botonEditaEquipo" data-toggle="collapse" href="#collapseAgregaEquipoTaller" aria-controls="collapseEditaEquipoTaller" wire:click="editaEquipoTaller({{ $taller->num_orden }})" title="Editar equipo en taller" wire:loading.attr="disabled" wire:target="editaEquipoTaller" style="color: dimgrey;" onclick="ocultarBoton()">
                                        <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                                    </a>
                                    <span wire:loading wire:target="editaEquipoTaller" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                @endif
                            @endif
                            {{-- Este botón abre la ventana modal mediante Javascript y no por bootstrap --}}
                            <button wire:click="cobroFinalEquipoTaller({{ $taller->num_orden }})" wire:loading.remove wire:target="cobroFinalEquipoTaller" class="label-button"
                            {{-- data-toggle="modal" data-target="#cobroTallerModal" --}}
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
                                @role('admin')
                                <a href="#" wire:click='abreModalCambiaEstatusEquipo({{ $taller->num_orden }})'> <i class="fa-solid fa-arrow-rotate-left" style="color:dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Regresar a NO ENTREGADOS"></i></a>
                                @endrole
                                <!-- Enlace o Botón para Abrir el Modal -->
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
                            @if(!$muestraDivAgregaEquipo)
                                @if (isset($taller->cobroTallerCredito) && $taller->id_estatus >= 5) 
                                <button wire:click="abreCobroCredito({{ $taller->num_orden }})" wire:loading.remove wire:target="abreCobroCredito" class="label-button">
                                <i class="fa-solid fa-credit-card" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"  title="Crédito y Abonos"></i>
                                </button>
                                @elseif (isset($taller->cobroTallerCredito) && $taller->id_estatus <= 4)
                                <button wire:click="abreCobroCredito({{ $taller->num_orden}}, 'true')" wire:loading.remove wire:target="abreCobroCredito" class="label-button">
                                    <i class="fa-solid fa-credit-card" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'" title="Crédito y Abonos***"></i>
                                </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <div class="row">  --}}
        @if ($hayNoDisponibles || $hayInexistentes)
            <div class="col-md-5">
                <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                   @if ($hayNoDisponibles)* NO DISPONIBLE @endif @if ($hayInexistentes) &nbsp; ***** INEXISTENTE @endif
                </label>
            </div>
        @endif
        {{-- </div> --}}
        <div class="w-100 d-flex justify-content-end align-items-center mb-1">
            <x-button wire:click="abrirCaja" class="ml-md-4 align-self-center mb-2 mb-md-0" style="white-space: nowrap; display: flex; justify-content: center; align-items: center; width: 100%; max-width: 200px;">
                <i class="fa-solid fa-coins" style="width: 20px;"></i> &nbsp; Abrir Caja [F9]
            </x-button>
            <x-button id="botonCorteCaja" wire:click='abreModalCorteCaja' data-toggle="modal" data-target="#corteCajaModal" class="ml-md-4 align-self-center">
                <i class="fa-solid fa-file-invoice-dollar"></i> &nbsp; Corte de Caja [F10]
            </x-button> &nbsp;
        </div>
    </div>    
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $equipos_taller->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        @this.on('abreModalCambiaEstatusEquipoTaller', () => {
            $('#cambiaEstatusEquipoModal').modal('show');
        });
    });

    document.addEventListener('livewire:initialized', function () {
        @this.on('cierraModalCambiaEstatusEquipoTaller', () => {
            $('#cambiaEstatusEquipoModal').modal('hide');
        });
    });

    $(document).ready(function () {
        $('#cobroTallerModal').on('shown.bs.modal', function () {
            $('.selectpicker').selectpicker('refresh');            
        });
    });

    function abreModalBuscarCliente() {
        $('#buscarClienteModal').modal('show');

        // Enfocar el input cuando el modal se muestra completamente
        $('#buscarClienteModal').on('shown.bs.modal', function () {
                $('#nombreClienteModal').focus();
            });
        }    


document.addEventListener('livewire:initialized', function () {
        Livewire.hook('element.updated', () => {
            $('#btnBuscarCliente').click(); // Simular clic para abrir la modal
        });
    });

    function descartarEquipo() {
        $('#collapseAgregaEquipoTaller').collapse('hide');
        let botonAgregar = document.getElementById('botonAgregar');
        if (botonAgregar && botonAgregar.offsetParent !== null) {
            botonAgregar.style.display = 'block';
        }
    }

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('ocultaDivAgregaEquipo', function () {
            $('#collapseAgregaEquipoTaller').collapse('hide'); // Cierra el Collapse
            let botonAgregar = document.getElementById('botonAgregar');
            if (botonAgregar && botonAgregar.offsetParent !== null) {
                botonAgregar.style.display = 'block';
            }
        });
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'F10') {
            Livewire.dispatch('f10-pressed');
            var botonCorteCaja = document.getElementById('botonCorteCaja');
            botonCorteCaja.click();
        }
    });

    document.addEventListener('livewire:initialized', function () {
    Livewire.on('abrirPestanaCorteCajaTaller', () => {
                    window.open('{{ url('/taller/corte') }}', '_blank');
                });
            });

    function ocultarBoton() {
        document.getElementById('botonAgregar').style.display = 'none';
    }

    function ocultarBotonAgregarPago() {
        document.getElementById('botonAgregarPago').style.display = 'none';
    }

    function openWhatsApp(phoneNumber) {
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        var url = isMobile ? "https://api.whatsapp.com/send?phone=" : "https://web.whatsapp.com/send?phone=";

        window.open(url + phoneNumber, "_blank");
    }

    function hideDivAbono(){
        document.getElementById("btn-agregar").style.display = "none";
        document.getElementById("btn-liquidar").style.display = "none";
        document.getElementById("label-abono").style.display = "none";
        document.getElementById("div-abono").style.display = "none";
        document.getElementById("div-selectModoPago5").style.display = "none";
    }

    document.addEventListener('keydown', function(event) {
        if (event.altKey && event.key === '+') {
        event.preventDefault();
        document.getElementById('botonAgregar').click();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    $('#anotacionesModal').on('shown.bs.modal', function () {
        let textAnotaciones = document.getElementById('textAnotaciones');
        if (textAnotaciones) {
            textAnotaciones.focus();
            console.log('Foco puesto en el textarea "textAnotaciones".');
        } else {
            console.log('El textarea con id "textAnotaciones" no existe.');
        }
    });

    Livewire.hook('morph.updated', () => {
        let modal = document.getElementById('anotacionesModal');
        if (modal && modal.classList.contains('show')) {
            let textAnotaciones = document.getElementById('textAnotaciones');
            if (textAnotaciones && !textAnotaciones.hasAttribute('readonly')) {
                textAnotaciones.focus();
            }
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let isSelectTipoEquipoOpen = false;
    let isSelectEntregadosOpen = false;

    function safeInitializeSelectpicker(selector) {
        let $el;

        try {
            $el = $(selector);

            if (!$el || typeof $el.length === 'undefined' || $el.length === 0) {
                // console.warn(`⚠️ No se encontró el elemento "${selector}" o no tiene longitud.`);
                return;
            }

            if ($el.find('option').length === 0) {
                // console.warn(`⚠️ ${selector} no tiene opciones. Se omite inicialización.`);
                return;
            }

            if ($el.data('selectpicker')) {
                $el.selectpicker('destroy');
            }

            $el.selectpicker('render').selectpicker('refresh');

            // console.log(`✅ ${selector} fue inicializado y refrescado correctamente`);
        } catch (error) {
            console.error(`🧨 Error al inicializar ${selector}:`, error);
        }
    }


    // 🧰 Configura todos los selectpickers principales
    function setupSelectpickers() {
        $('.selectpicker').off('shown.bs.select hidden.bs.select changed.bs.select');

        const selectors = ['#selectTipoEquipo', '#selectEntregados'];
        selectors.forEach(safeInitializeSelectpicker);

        // 🎯 Eventos para detectar apertura y cierre
        $('#selectTipoEquipo').on('shown.bs.select', () => (isSelectTipoEquipoOpen = true));
        $('#selectTipoEquipo').on('hidden.bs.select', () => (isSelectTipoEquipoOpen = false));

        $('#selectEntregados').on('shown.bs.select', () => (isSelectEntregadosOpen = true));
        $('#selectEntregados').on('hidden.bs.select', () => (isSelectEntregadosOpen = false));

        // 🔁 Eventos para cerrar select automáticamente después de cambiar
        $('#selectTipoEquipo').on('changed.bs.select', function () {
            if (isSelectTipoEquipoOpen) {
                $('#selectTipoEquipo').selectpicker('toggle');
                isSelectTipoEquipoOpen = false;
            }
        });

       let cerrarEntregadosPendiente = false;

        $('#selectEntregados').on('changed.bs.select', function () {
            cerrarEntregadosPendiente = true;
        });

            setInterval(() => {
                if (cerrarEntregadosPendiente && isSelectEntregadosOpen) {
                    const $select = $('#selectEntregados');
                    if ($select.length && $select.data('selectpicker')) {
                        $select.selectpicker('toggle');
                    }
                    isSelectEntregadosOpen = false;
                    cerrarEntregadosPendiente = false;
                }
            }, 120); // ⏱ amortigua el render sin bloquear eventos
        }

    // 🔄 Sincroniza los valores con Livewire si están inicializados correctamente
    function synchronizeSelectpickerValues() {
        const tipoEquipoEl = $('#selectTipoEquipo');
        const entregadosEl = $('#selectEntregados');

        let livewireTipoEquipo = @this.get('busquedaEquipos.idTipo') || [];
        let livewireEntregados = @this.get('busquedaEquipos.entregados') || [];

        if (tipoEquipoEl.length && tipoEquipoEl.data('selectpicker')) {
            tipoEquipoEl.selectpicker('val', livewireTipoEquipo);
        }

        if (entregadosEl.length && entregadosEl.data('selectpicker')) {
            entregadosEl.selectpicker('val', livewireEntregados);
        }
    }

    // ✅ Inicializar al cargar el documento
    setupSelectpickers();


    // 📦 Hook Livewire para manejar morphs y commits
    Livewire.hook('commit', ({ succeed, fail }) => {
        let wasEntregadosOpen = false;
        const $select = $('#selectEntregados');

        if ($select.length && $select.data('selectpicker')) {
            const $parent = $select.parent('.btn-group');
            wasEntregadosOpen = $parent.hasClass('show');
        }

        succeed(() => {
            queueMicrotask(() => {
                setupSelectpickers();
                synchronizeSelectpickerValues();

                setTimeout(() => {
                    [
                        '#selectTipoEquipo',
                        '#selectEntregados',
                        '#selectModoPago5',
                        '#selectModoPago6',
                        '#selectModoPagoCorte',
                        '#modoPagoSelect',
                        '#estatusEquiposSelect'
                    ].forEach(safeInitializeSelectpicker);
                }, 30);

                // 🔁 Reabrir los selects si estaban abiertos
                if (isSelectTipoEquipoOpen && $('#selectTipoEquipo').data('selectpicker')) {
                    $('#selectTipoEquipo').selectpicker('toggle');
                }

                if (isSelectEntregadosOpen && $('#selectEntregados').data('selectpicker')) {
                    $('#selectEntregados').selectpicker('toggle');
                }

                // 🧬 Actualizar valores en Livewire de forma segura
                const tipoEquipoEl = $('#selectTipoEquipo');
                if (tipoEquipoEl.length && tipoEquipoEl.data('selectpicker')) {
                    tipoEquipoEl.off('change').on('change', function () {
                        @this.set('busquedaEquipos.idTipo', $(this).val());
                    });
                }

                const entregadosEl = $('#selectEntregados');
                if (entregadosEl.length && entregadosEl.data('selectpicker')) {
                    entregadosEl.off('change').on('change', function () {
                        @this.set('busquedaEquipos.entregados', $(this).val());
                    });
                }
            });
        });

        fail(() => {
            console.error('❌ Livewire commit failed');
        });
    });
});

document.addEventListener('livewire:initialized', function () {
        @this.on('cierraModalEditaModoPagoTallerCredito', () => {
            $('#editarModoPagoModalTallerCredito').modal('hide');
        });

        @this.on('abreModalEditaModoPagoTallerCredito', () => {
            $('#editarModoPagoModalTallerCredito').modal('show');
        });
    });

    document.addEventListener('livewire:load', function () {
    Livewire.hook('message.processed', (message, component) => {
        $('#selectEntregados').selectpicker('refresh');
        console.log('✅ selectEntregados refrescado después del render de Livewire');
    });
});
    
</script>

<script>
// 'success' ✔️
// 'error' ❌
// 'warning' ⚠️
// 'info' ℹ️
// 'question' ❓
document.addEventListener('livewire:initialized', function () {
    Livewire.on('mostrarToastSiNo', (attr) => {
        Swal.fire({
            title: attr[0],
            icon: attr[1], // Usar el ícono proporcionado por Livewire
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            customClass: {
                title: 'swal2-title-custom', // Clase CSS personalizada para el título
                content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                icon: 'fa-xs',
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-danger',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Emitir evento a Livewire para ejecutar la función GuardaDatos
                Livewire.dispatch('guardaCambioEstatusEquipo');
            }
        });
    });
});


</script>


