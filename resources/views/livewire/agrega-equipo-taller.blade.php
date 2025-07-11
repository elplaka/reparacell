
<div>
    @include('livewire.modal-nuevo-tipo-equipo')
    @include('livewire.modal-nueva-marca')
    @include('livewire.modal-nuevo-modelo')
    @include('livewire.modal-nueva-falla')
    @include('livewire.modal-buscar-cliente')
    @include('livewire.modal-equipos-cliente')
    @include('livewire.modal-equipo-cliente-historial')
    @include('livewire.modal-cliente-historial')
    @include('livewire.modal-advertencia-equipo-taller')  

     <!-- Ventana modal de confirmación -->
    <div wire:ignore.self class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="uppercase tracking-widest font-semibold text-xs" id="confirmDeleteModalLabel"> Confirmar eliminación</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro(a) de que deseas eliminar esta imagen?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="eliminaImagen({{ $imagenIndexToDelete }})">Eliminar</button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @if(!$muestraDivAgregaEquipo)    
    <div class="d-flex justify-content-center">
        <span wire:loading class="ml-2">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Cargando...</span>
        </span>
    </div>
    @endif
    <div id="divPrincipal" name="divPrincipal" style="display: @if(!$muestraDivAgregaEquipo) none @endif">
    <div wire:ignore.self class="collapse" id="collapseAgregaEquipoTaller">
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
    <div class="modal-header">
        <span>
            @if ($equipoTaller['estatus'] == 1)
            <h4 class="text-xl font-bold"><i class="fa-solid fa-pen-to-square"></i><b> Editar equipo del taller</b></h4>
            @else
            <h4 class="text-xl font-bold"><i class="fa-solid fa-arrow-right-to-bracket"></i><b> Agregar equipo al taller</b></h4>
            @endif
        </span>
        <span wire:loading class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>        
    </div>
    <div class="modal-body">
        <div class="rounded p-3 border"> 
            <h3 class="mt-n4 ml-n2">
                <span class="text-gray-700 uppercase tracking-wider" style="background-color:rgb(249, 250, 253); font-size:11pt"> <strong>&nbsp; CLIENTE &nbsp;</strong></span>
                @if ($cliente['estatus'] == 1) &nbsp;&nbsp;&nbsp;&nbsp; <span class="uppercase tracking-wider" style="background-color:green; color:white; vertical-align:top; font-size: 8pt">&nbsp;Nuevo&nbsp;</span> @endif <span style="background-color:rgb(249, 250, 253);" wire:loading class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </h3>
            <div class="row mt-3">
                <label for="cliente.telefono" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0 mb-1" style="font-size: 11pt;">
                    {{ __('Teléfono') }}
                </label>
    
                <div class="col-12 col-md-3">
                    <div class="row">
                        <div class="col-8 col-md-7 pr-0">
                            <input wire:model.live="cliente.telefono" id="telefonoCliente" name="telefonoCliente" type="text" class="input-height form-control"
                                   {{-- @if (strlen($cliente['telefono']) >= 9) wire:input="validarNumeros" @endif --}}
                                   style="font-size: 11pt;"
                                   @if ($cliente['estatus'] == 3) readonly @endif 
                                   autofocus> 
                        </div>
                        @if ($cliente['estatus'] == 0)
                        <div class="col-4 col-md-5 pl-1">
                            <button id="buscarClienteBtn" class="btn btn-secondary btn-sm btn-md d-inline-flex align-items-center" 
                                    data-toggle="modal" 
                                    data-target="#buscarClienteModal" 
                                    style="font-size: 10pt"
                                    onclick="abreModalBuscarCliente()"
                                    wire:click='abreModalBuscarCliente'
                                    >
                                <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <!-- Additional buttons for existing client -->
                @if (!$cliente['publicoGeneral'])
                    @if (strlen($cliente['telefono']) == 10 || $cliente['estatus'] >= 2)
                        <div class="col-12 col-md-8 d-flex justify-content-end mt-2 mt-md-0">
                            @if ($cliente['estatus'] >= 2)   {{-- Cliente ya existente --}}
                            <button class="btn btn-secondary ml-2" style="font-size: 10pt" data-toggle="modal" data-target="#equiposClienteModal" title="Ver equipos del cliente" wire:click="abrirEquiposClienteModal">
                                <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-mobile-screen"></i>
                            </button>
                            <button class="btn btn-secondary ml-2" style="font-size: 10pt" data-toggle="modal" data-target="#clienteHistorialModal" wire:click="abreClienteHistorial" title="Ver historial del cliente">
                                <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-clock-rotate-left"></i>
                            </button>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
            <div class="row mt-3">
                @if (strlen($cliente['telefono']) == 10  ||  $cliente['estatus'] == 3)
                <label for="cliente.nombre" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0 mb-1" style="font-size: 11pt;">{{ __('Nombre') }}</label>
                <div class="col-md-3">
                    <input wire:model="cliente.nombre" type="text" class="input-height form-control mb-3" id="cliente.nombre" style="font-size:11pt;" @if($cliente['estatus'] >= 2) readonly @endif autofocus>
                </div>
                <label for="cliente.direccion" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0 mb-1" style="font-size: 11pt;">{{ __('Dirección') }}</label>
                <div class="col-md-3">
                    <input wire:model="cliente.direccion" type="text" class="input-height form-control mb-3" id="cliente.direccion" style="font-size:11pt;" @if($cliente['estatus'] >= 2) readonly @endif autofocus>
                </div>
                <label for="cliente.telefonoContacto" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0 mb-1" style="font-size: 11pt;">{{ __('Contacto') }}</label>
                <div class="col-md-2 mb-2">
                    <input wire:model="cliente.telefonoContacto" type="text" class="input-height form-control mb-3" id="telefonoContacto" style="font-size:11pt;" wire:keydown="validarNumerosContacto" @if($cliente['estatus'] >= 2) readonly @endif autofocus>
                </div>
                @endif
            </div>
        </div>
            <br>
            @if (strlen($cliente['telefono']) == 10)
                @if(!$tieneEquiposCliente || $cliente['estatus'] == 1 || $equipoSeleccionadoModal || $equipo['estatus'] == 1 || $cliente['publicoGeneral'])
                <div class="rounded p-3 border"> 
                    <h3 class="mt-n4 ml-n2">
                        <span class="bg-white text-gray-700 uppercase tracking-wider" style="background-color:rgb(249, 250, 253); font-size: 11pt"> <strong>&nbsp; EQUIPO &nbsp;</strong></span>
                        @if ($equipo['estatus'] == 1  && !$cliente['publicoGeneral']) &nbsp;&nbsp;&nbsp;&nbsp; <span class="uppercase tracking-wider" style="background-color:green; color:white; vertical-align:top; font-size: 8pt">&nbsp;Nuevo&nbsp;</span> @endif
                    </h3>  
                    <div class="container mt-3">
                        <div class="row">
                            <label for="equipo.idTipo" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Tipo') }}</label>
                            <div class="col-md-2 d-flex align-items-center">
                                @if ($equipo['estatus'] == 0 || $equipo['estatus'] == 1 || $equipo['estatus'] == 3)
                                    <select wire:model.live="equipo.idTipo" type="text" class="select-height form-control" id="equipo.idTipo" style="font-size:11pt;"  autofocus>
                                        @foreach ($tipos_equipos as $tipo_equipo)
                                            <option value="{{ $tipo_equipo->id }}">{{ $tipo_equipo->nombre }}</option>
                                        @endforeach
                                    </select> &nbsp;
                                    <button id="nuevoTipoEquipoButton" data-toggle="modal" data-target="#nuevoTipoEquipoModal" title="Nuevo tipo de equipo" wire:click="nuevoTipoEquipoModal">
                                        +
                                    </button>
                                @else
                                    <input wire:model.live="equipo.nombreTipo" type="text" class="input-height form-control" id="equipo.nombreTipo" style="font-size:11pt;" readonly autofocus>
                                @endif
                            </div>
                            <label for="equipo.idMarca" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Marca') }} </label>
                            <div class="col-md-2 d-flex align-items-center">
                                @if ($equipo['estatus'] == 0 || $equipo['estatus'] == 1 || $equipo['estatus'] == 3)
                                    <select wire:model.live="equipo.idMarca" type="text" class="select-height form-control" id="equipo.idMarca" style="font-size:11pt;" autofocus>
                                        <option value="">-SELECCIONA-</option>
                                        @foreach ($marcas_equipos as $marca_equipo)
                                            <option value="{{ $marca_equipo->id }}">{{ $marca_equipo->nombre }}</option>
                                        @endforeach
                                    </select> &nbsp;
                                    <button id="nuevaMarcaButton" data-toggle="modal" data-target="#nuevaMarcaModal" title="Nueva marca" wire:click="nuevaMarcaModal">
                                        +
                                    </button>
                                @else
                                    <input wire:model.live="equipo.nombreMarca" type="text" class="input-height form-control" id="equipo.nombreMarca" style="font-size:11pt;" readonly autofocus>
                                @endif
                            </div>                                
                            <label for="equipo.idModelo" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Modelo') }} </label>
                            <div class="col-md-2 d-flex align-items-center">
                                @if ($equipo['estatus'] == 0 || $equipo['estatus'] == 1 || $equipo['estatus'] == 3)
                                    <select wire:model="equipo.idModelo" type="text" class="select-height form-control" id="equipo.idModelo" style="font-size:11pt;" autofocus>
                                        <option value="">-SELECCIONA-</option>
                                        @foreach ($modelos_equipos as $modelo_equipo)
                                            <option value="{{ $modelo_equipo->id }}">{{ $modelo_equipo->nombre }}</option>
                                        @endforeach
                                    </select> &nbsp;
                                    <button id="nuevoModeloButton" data-toggle="modal" data-target="#nuevoModeloModal" title="Nuevo modelo" wire:click="nuevoModeloModal">
                                        +
                                    </button>
                                @else
                                    <input wire:model.live="equipo.nombreModelo" type="text" class="input-height form-control" id="equipo.nombreModelo" style="font-size:11pt;" readonly autofocus>
                                @endif
                            </div>
                            @if (strlen($cliente['telefono']) == 10 || $equipo['estatus'] >= 2)
                            <div class="col col-md-3 d-flex justify-content-end">
                                @if ($equipo['estatus'] >= 2)
                                <button class="btn btn-secondary ml-2" style="font-size: 10pt" data-toggle="modal" data-target="#equipoClienteHistorialModal" wire:click="abreEquipoClienteHistorial" title="Ver historial del equipo">
                                    <i class="fa-solid fa-mobile-screen"></i>&thinsp;<i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="container mt-3">
                        <div class="row mb-3">
                            <label for="fallas" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Fallas') }}</label>
                            <div class="col-md-11">
                                @php
                                $contador = 0;
                                @endphp
                                @foreach ($fallas_equipos as $falla)
                                    @if ($contador % 4 == 0)
                                    <div class="row">
                                    @endif
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    id="falla{{ $falla->id }}"
                                                    value="{{ $falla->id }}"
                                                    wire:model="fallas.{{ $falla->id }}"
                                                    wire:click="hazParo()"
                                                >
                                                <label class="form-check-label" for="falla{{ $falla->id }}" style="color: dimgray; font-size: 11pt">
                                                    {{ $falla->descripcion }}
                                                </label>
                                            </div>
                                        </div>
                                        @if ($contador % 4 == 3 || $loop->last)
                                            @if ($loop->last)
                                                <button id="nuevaFallaButton" data-toggle="modal" data-target="#nuevaFallaModal" title="Nueva falla" wire:click="nuevaFallaModal">
                                                    +
                                                </button>
                                                @endif
                                    </div>
                                            @endif
                                            @php
                                            $contador++;
                                            @endphp
                                @endforeach
                                @if ($fallas_equipos->count() == 0)
                                <button id="nuevaFallaButton" data-toggle="modal" data-target="#nuevaFallaModal" title="Nueva falla" wire:click="nuevaFallaModal">
                                    +
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <label for="imagenes" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Imágenes') }}</label>
                            @for ($i = 0; $i < 6; $i++)
                                <div class="col-md-1 d-flex align-items-center justify-content-begin">
                                    <div wire:loading wire:target="imagenes.{{ $i }}">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div> &nbsp;
                                    @if ($errors->has("imagenes.$i"))
                                        <label for="imagen_{{ $i }}" style="cursor: pointer;">
                                            <i class="fa-solid fa-file-image"></i><b>+</b>
                                        </label>
                                    @else
                                        @if (isset($imagenes[$i]))
                                            <span style="background-color: red; border-radius: 50%; padding: 3px; font-size: 12px; cursor: pointer;"
                                            wire:click="$set('imagenIndexToDelete', {{ $i }})"
                                            data-toggle="modal"
                                            data-target="#confirmDeleteModal">
                                            <i class="fa fa-trash-can" style="color: white;"></i>
                                            </span>
                                            &nbsp;                                    
                                            @if (is_object($imagenes[$i]) && method_exists($imagenes[$i], 'temporaryUrl'))
                                            <a href="{{ $imagenes[$i]->temporaryUrl() }}" target="_blank">
                                                <img src="{{ $imagenes[$i]->temporaryUrl() }}" width="50">
                                            </a>
                                            @else
                                            <img src="{{ asset('storage/imagenes-equipos/' . $imagenes[$i]) }}" alt="Imagen" width="50">
                                            @endif
                                            @php
                                                $numImagenes++;
                                            @endphp
                                        @else
                                            @if ($i <= $numImagenes - 1)
                                            <label for="imagen_{{ $i }}" style="cursor: pointer;"
                                            onmouseover="this.style.color='grey'; this.style.cursor='pointer';"
                                            onmouseout="this.style.color='initial';">
                                            <i class="fa-solid fa-file-image"></i><b>+</b>
                                            </label>                                 
                                            @endif
                                        @endif
                                    @endif                                
                                </div>
                                <input type="file" id="imagen_{{ $i }}" style="display: none;" accept="image/jpeg, image/jpg" wire:model="imagenes.{{ $i }}" />
                            @endfor
                            <span wire:loading>Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="rounded pb-3 border"> 
                    <div class="container mt-3">
                        <div class="row">
                            <!-- Columna para Observaciones -->
                            <div class="col-md-1 p-0">
                                <label for="equipoTaller.observaciones" class="font-medium text-gray-700 text-end w-100" style="font-size: 11pt; height: 2em; display: flex; align-items: center; justify-content: flex-end;">
                                    {{ __('Observ.') }} &nbsp;
                                </label>
                            </div>
                            <div class="col-md-3 p-0">
                                <input wire:model="equipoTaller.observaciones" type="text" class="input-height form-control" style="font-size:11pt;" autofocus>
                            </div>
                        
                            <div class="col-md-2">
                                <label for="equipoTaller.totalEstimado" class="block font-medium text-gray-700 pr-0" style="font-size: 11pt; height: 2em; display: flex; align-items: center; justify-content: flex-end; text-align: right;"> 
                                    {{ __('Total Estimado $ ') }} {{ number_format($equipoTaller['totalEstimado'], 2, '.', ',') }} 
                                </label>
                            </div>
                        
                            @if (!$cliente['publicoGeneral'])
                                @if ($equipoTaller['estatus'] == 0 || $equipoTaller['agregaAbono'])
                                    <div class="col-md-1 p-0"> <!-- p-0 elimina el padding interno -->
                                        <label for="equipoTaller.anticipo" class="font-medium text-gray-700 text-end w-100" style="font-size: 11pt; height: 2em; display: flex; align-items: center; justify-content: flex-end;">
                                            {{ __('Anticipo $') }} &nbsp;
                                        </label>
                                    </div>
                                    <div class="col-md-2 p-0"> <!-- p-0 elimina el padding interno -->
                                        <input wire:model.live="equipoTaller.anticipo" id="inputAnticipo" type="number" step="0.5" class="input-height form-control rounded-0" style="font-size:11pt;" autofocus>
                                    </div>
                                    @if ($equipoTaller['anticipo'] > 0)
                                    <div class="col-md-3">
                                        <select wire:model="equipoTaller.idModoPagoAnticipo" id="selectModoPagoAnticipo" class="selectpicker select-picker w-100">
                                            @foreach ($modosPago as $modoPago)
                                                <option value="{{ $modoPago->id }}" data-content="<i class='{{ $modoPago->icono }}'></i> &nbsp; {{ $modoPago->nombre }}"></option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                @else
                                    <div class="col-md-1 p-0"> <!-- p-0 elimina el padding interno -->
                                        <label for="equipoTaller.anticipo" class="font-medium text-gray-700 text-end w-100" style="font-size: 11pt; height: 2em; display: flex; align-items: center; justify-content: flex-end;">
                                            {{ __('Anticipo $ ') }} &nbsp;
                                        </label>
                                    </div>
                                    @if ($equipoTaller['anticipo'] == 0)
                                        <div class="col-md-1">
                                            <button wire:click="agregaAbono" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" style="padding: 0; height: 24px; width: 24px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar abono">
                                                <i class="fa fa-plus" style="margin: 0; font-size: 14px;"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="col-md-1 p-0">
                                            <label for="equipoTaller.anticipo" class="font-medium text-gray-700 w-100" style="font-size: 11pt; height: 2em; display: flex; align-items: center; justify-content: flex-start;">
                                                {{ $equipoTaller['anticipo'] }} &nbsp;
                                            </label>
                                        </div>
                                    @endif
                                    @if ($equipoTaller['agregaAbono'])
                                    <div class="col-md-3">
                                        <select wire:model.live="equipoTaller.idModoPagoAnticipo" id="selectModoPagoAnticipo" class="selectpicker select-picker w-100">
                                            @foreach ($modosPago as $modoPago)
                                                <option value="{{ $modoPago->id }}" data-content="<i class='{{ $modoPago->icono }}'></i> &nbsp; {{ $modoPago->nombre }}"></option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @else
                                        @if ($equipoTaller['anticipo'] > 0)
                                        <div class="col-md-3">
                                                @foreach ($modosPago as $modoPago)
                                                    @if ($modoPago->id == $equipoTaller['idModoPagoAnticipo'])
                                                        <i class="{{ $modoPago->icono }}"></i> &nbsp; {{ $modoPago->nombre }}
                                                    @endif
                                                @endforeach
                                        </div>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @endif
            <br>
            <div class="modal-footer d-flex justify-content-end">
                @if (strlen($cliente['telefono']) == 10)
                    @if ($equipoTaller['estatus'] == 1)  {{-- Editando el equipo en taller --}}
                    <button class="btn btn-success uppercase tracking-widest font-semibold text-xs" wire:click="actualizaEquipo" wire:loading.attr='disabled'>Actualizar</button>
                    @else
                    <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="aceptaEquipo" wire:loading.attr='disabled'>Aceptar</button>
                    @endif
                @endif
                <button class="btn btn-danger uppercase tracking-widest font-semibold text-xs" wire:click="descartaEquipo" onclick="descartarEquipo()">Descartar</button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.hook('morph.updated', () => {
            let divPrincipal = document.getElementById('divPrincipal');
            let telefonoContacto = document.getElementById('telefonoContacto');
            let inputAnticipo = document.getElementById('inputAnticipo');

            if (divPrincipal && isElementVisible(divPrincipal)) {
                let telefonoCliente = document.getElementById('telefonoCliente');
                if (telefonoCliente) {
                    if (!telefonoCliente.hasAttribute('readonly')) {
                        if (document.activeElement === telefonoContacto)
                        { 
                            telefonoContacto.focus();
                        }
                        else if (document.activeElement != inputAnticipo)
                        {
                            telefonoCliente.focus();
                        }
                    } else {
                        console.log('El input "telefonoCliente" está en modo readonly. No se pone el foco.');
                    }
                } else {
                    console.log('El input con id "telefonoCliente" no existe.');
                }
            }
        });

        // Función para verificar si un elemento es visible
        function isElementVisible(el) {
            const style = window.getComputedStyle(el);
            return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    // Evento para detectar el Enter en el input
    document.getElementById('telefonoCliente').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevenir el comportamiento por defecto
            const button = document.getElementById('buscarClienteBtn');
            if (button && button.offsetParent !== null) { // Verificar si el botón está visible
                button.click(); // Simular el clic del botón
            } else {
                console.error('Botón no encontrado o no visible');
            }
        }
    });
});

//FUNCIONA PERO USA SETTIMEOUT
document.addEventListener('DOMContentLoaded', function () {
    Livewire.on('focusInput', () => {
        const input = document.getElementById('nombreClienteModal');
        const modal = document.getElementById('buscarClienteModal');
        
        if (input && modal) {
            // Introducimos un pequeño retraso
            setTimeout(() => {
                input.focus(); // Luego, enfocamos de nuevo
            }, 1); // Ajusta el tiempo de espera según sea necesario
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    let shouldFocus = false;

    Livewire.on('focusInput', () => {
        shouldFocus = true;
        const input = document.getElementById('nombreClienteModal');
        if (input && document.activeElement !== input) {
            input.focus();
            console.log('Foco restaurado solo si es necesario.');
        }
    });

    Livewire.hook('morph.updated', ({ el }) => {
        if (shouldFocus) {
            const input = document.getElementById('nombreClienteModal');
            if (input && document.activeElement !== input) {
                input.focus();
            }
            shouldFocus = false; // Resetear el flag después de enfocar
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
        $('#selectModoPagoAnticipo').selectpicker();
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            $('.selectpicker').selectpicker();

            succeed(({ snapshot, effect }) => {
                $('#selectModoPagoAnticipo').selectpicker('destroy');

                queueMicrotask(() => {
                    setTimeout(() => { 
                        $('#selectModoPagoAnticipo').selectpicker('refresh'); 
                    }, 100);
                });
            });

            fail(() => {
                console.error('Livewire commit failed');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        $('#marcaMod\\.idTipoEquipo').selectpicker();
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            $('.selectpicker').selectpicker();

            succeed(({ snapshot, effect }) => {
                $('#marcaMod\\.idTipoEquipo').selectpicker('destroy');

                queueMicrotask(() => {
                    setTimeout(() => { 
                        $('#marcaMod\\.idTipoEquipo').selectpicker('refresh'); 
                    }, 100);
                });
            });

            fail(() => {
                console.error('Livewire commit failed');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        $('#fallaMod\\.idTipoEquipo').selectpicker();
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            $('.selectpicker').selectpicker();

            succeed(({ snapshot, effect }) => {
                $('#fallaMod\\.idTipoEquipo').selectpicker('destroy');

                queueMicrotask(() => {
                    setTimeout(() => { 
                        $('#fallaMod\\.idTipoEquipo').selectpicker('refresh'); 
                    }, 100);
                });
            });

            fail(() => {
                console.error('Livewire commit failed');
            });
        });
    });


</script>

