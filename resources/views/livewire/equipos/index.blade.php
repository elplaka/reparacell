@php
    $hayNoDisponibles = false;
    $hayInexistentes = false;
    $itemDisponible = true;
@endphp
<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.equipos.modal-nuevo-equipo')
    @include('livewire.equipos.modal-editar-equipo')
    @include('livewire.equipos.modal-historial-equipo')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-rectangle-list"></i>
            Catálogo de Equipos</b></h4> 
        <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreNuevoEquipo" title="Agregar equipo" wire:loading.attr="disabled" wire:target="abreNuevoEquipo" data-toggle="modal" data-target="#nuevoEquipoModal">
                <i class="fas fa-plus"></i>
            </a>
    </div>

    <div class="row"> 
        <div class="col-md-1 mb-3">
            <label for="filtrosEquipos.id" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Id </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosEquipos.id" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosEquiposidTipo" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Tipo </label>
            <select wire:model.live="filtrosEquipos.idTipo" class="selectpicker select-picker w-100" id="filtrosEquiposidTipo" style="font-size:11pt;" title="-- TODOS --" multiple wire.change="cambia">
            @foreach ($tiposEquipos as $tipo_equipo)
                <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
            @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosEquiposidMarca" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Marca </label>
            <select wire:model.live="filtrosEquipos.idMarca" class="selectpicker select-picker w-100" id="filtrosEquiposidMarca" style="font-size:11pt;" title="-- TODAS --" multiple>
            @foreach ($marcas as $marca)
                <option value="{{ $marca->id }}" data-content="{{ $marca->tipoEquipo->icono }} &nbsp; {{ $marca->nombre }}"></option>
            @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosEquiposidModelo" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Modelo </label>
            <select wire:model.live="filtrosEquipos.idModelo" class="selectpicker select-picker w-100" id="filtrosEquiposidModelo" style="font-size:11pt;" title="-- TODOS --" multiple>
            @foreach ($modelos as $modelo)
                <option value="{{ $modelo->id }}" data-content="{{ $modelo->marca->tipoEquipo->icono }} &nbsp; {{ $modelo->marca->nombre }} :: {{ $modelo->nombre }}"></option>
            @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosEquiposnombreCliente" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Cliente </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosEquipos.nombreCliente" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosEquiposdisponible" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Estatus </label>
            <select wire:model.live="filtrosEquipos.disponible" class="selectpicker select-picker w-100" id="filtrosEquiposdisponible" style="font-size:11pt;" title="-- TODOS --">
            <option value="1">DISPONIBLE </option>
            <option value="0">NO DISPONIBLE </option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TIPO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MARCA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MODELO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CLIENTE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipos as $equipo)
                @php
                    $itemDisponible = true;
                    if ($equipo->tipo_equipo->disponible)
                    {
                        $tipoEquipo = $equipo->tipo_equipo->icono;
                    }
                    else 
                    {
                        $tipoEquipo = $equipo->tipo_equipo->icono . "*";
                        $hayNoDisponibles = true;
                        $itemDisponible = false;
                    }

                    if($equipo->marca->id_tipo_equipo === $equipo->id_tipo)
                    {                        
                        if($equipo->marca->disponible)
                        {
                            $nombreMarca = $equipo->marca->nombre;
                        }
                        else 
                        {
                            $nombreMarca = $equipo->marca->nombre . "*";
                            $hayNoDisponibles = true;
                        }
                    }
                    else 
                    {
                        $nombreMarca = "*****";
                        $hayInexistentes = true;
                    }

                    if($equipo->modelo->id_marca === $equipo->marca->id)
                    {
                        if ($equipo->modelo->disponible)
                        {
                            $nombreModelo = $equipo->modelo->nombre;
                        }
                        else 
                        {
                            $nombreModelo = $equipo->modelo->nombre . "*";
                            $hayNoDisponibles = true;
                        }
                    }
                    else 
                    {
                        $nombreModelo = "*****";
                        $hayInexistentes = true;
                    }
                @endphp
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $equipo->id }} 
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {!! $tipoEquipo !!}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $nombreMarca }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $nombreModelo }}
                    </td>
                    @if($equipo->cliente->disponible)
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $equipo->cliente->nombre }}</td>
                    @else
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $equipo->cliente->nombre . '*' }}</td>
                    @php
                        $hayNoDisponibles = true;
                    @endphp
                    @endif
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $equipo->disponible == 1 ? 'DISPONIBLE' : 'NO DISPONIBLE'  }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($itemDisponible)
                        <a wire:click="editaEquipo('{{ $equipo->id }}')" title="Editar equipo" wire:loading.attr="disabled" wire:target="editaEquipo" style="color: dimgrey; cursor:pointer;" data-toggle="modal" data-target="#editarEquipoModal"
                            >
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a>
                        @endif                    
                        <a wire:click.prevent="abreHistorialTaller({{ $equipo->id }})" title="Historial en taller" wire:loading.attr="disabled" wire:target="abreHistorialTaller" style="color: dimgrey; cursor:pointer;" data-toggle="modal" data-target="#equipoHistorialModal"
                            >
                            <i class="fa-solid fa-screwdriver-wrench" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a>
                        <a wire:click="invertirEstatusEquipo('{{ $equipo->id }}')" wire:loading.attr="disabled" wire:target="invertirEstatusEquipo" style="color: dimgrey;cursor:pointer">
                            @if ($equipo->disponible)
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
    </div>
    @if ($hayNoDisponibles || $hayInexistentes)
    <div class="row"> 
        <div class="col-md-10">
            <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                @if ($hayNoDisponibles)* NO DISPONIBLE @endif @if ($hayInexistentes) &nbsp; ***** INEXISTENTE @endif
            </label>
        </div>
    </div>
    @endif
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $equipos->links('livewire.paginame') }}
        </label>
    </div> 
</div>

{{-- 

<script>

// Este script vale oro
// Se pudo usar selectpicker sin que desapareciera después de cada render de Livewire
// Con este script se resuelve el problema que tiene selectpicker con Livewire
// Usando Javascript la solución es esta, aclarando que es Livewire 3.0
// document.addEventListener('DOMContentLoaded', function () {

//     $('.selectpicker').selectpicker();

//     Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => { 
//     // Equivalent of 'message.sent'
//     succeed(({ snapshot, effect }) => {
//         // Equivalent of 'message.received'
//         $('select').selectpicker('destroy');
//         queueMicrotask(() => {
//             // Equivalent of 'message.processed'
//             $('.selectpicker').selectpicker();
//         })
//     })
//     fail(() => {
//         // Equivalent of 'message.failed'
//     })
// })
// </script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Inicializar los selectpickers al cargar la página
    // $('.selectpicker').selectpicker();

    // Hook para manejar el commit de Livewire
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        // Capturar el valor del selectpicker antes de la actualización
        const disponible = document.getElementById('filtrosEquiposdisponible').value;

        // Re-inicializar los selectpickers después de la actualización
        succeed(({ snapshot, effect }) => {
            // Destruir y volver a inicializar los selectpickers
            $('select').selectpicker('destroy');
            queueMicrotask(() => {
                $('.selectpicker').selectpicker('refresh');
                $('#filtrosEquiposdisponible').selectpicker('val', disponible);
            });
        });

        fail(() => {
            console.error('Livewire commit failed');
        });
    });
});
</script> 








