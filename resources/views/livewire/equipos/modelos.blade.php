@php
    use App\Models\MarcaEquipo;

    $hayNoDisponibles = false;
@endphp
<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-nuevo-modelo')
    @include('livewire.equipos.modal-editar-modelo')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-bookmark"></i>
            Modelos de Equipos</b></h4>
            <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaModelo" title="Agregar modelo" wire:loading.attr="disabled" wire:target="abreAgregaModelo" data-toggle="modal" data-target="#nuevoModeloModal">
                <i class="fas fa-plus"></i>
            </a>
    </div>

    <div class="row">  
        <div class="col-md-3 mb-3">
            <label for="filtrosModelos.idTipoEquipo" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Tipo Equipo </label>
            <select wire:model.live="filtrosModelos.idTipoEquipo" class="selectpicker select-picker w-100" id="filtrosModelos.idTipoEquipo" style="font-size:11pt;">
                <option value="0"> -- TODOS -- </option> 
                @foreach ($tipos_equipos as $tipo_equipo)
                    <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosModelos.idMarca" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Marca </label>
            <select wire:model.live="filtrosModelos.idMarca" type="text" class="select-height form-control w-100 select-hover" id="filtrosModelos.idMarca" style="font-size: 11pt;" wire:key={{ $filtrosModelos['idTipoEquipo'] }}>
                <option value="0"> -- TODAS -- </option>
                @foreach (MarcaEquipo::where('id_tipo_equipo', $this->filtrosModelos['idTipoEquipo'])->where('disponible', 1)->orderBy('nombre')->get() as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosModelos.nombre" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Nombre </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosModelos.nombre" style="font-size:11pt;">
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosModelos.disponible" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Estatus </label>
            <select wire:model.live="filtrosModelos.disponible" class="selectpicker select-picker w-100" id="filtrosModelos.disponible" style="font-size:11pt;">
                <option value="-1"> -- TODOS -- </option> 
                <option value="0" data-content="<i class='fa-solid fa-rectangle-xmark'></i> &nbsp; NO DISPONIBLE"></option>
                <option value="1" data-content="<i class='fa-solid fa-square-check'></i> &nbsp; DISPONIBLE"></option>
            </select>
        </div>      
    </div>

    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TIPO EQUIPO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MARCA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">NOMBRE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($modelos as $modelo)
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {!! $modelo->marca->tipoEquipo->icono !!} &nbsp; {{ $modelo->marca->tipoEquipo->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{-- @if($taller->equipo->marca->id_tipo_equipo === $taller->equipo->id_tipo) --}}
                        {{-- @if($modelo->marca->id_tipo_equipo === $modelo->marca->tipoEquipo->id_tipo) --}}
                            @if ($modelo->marca->disponible)
                                {{ $modelo->marca->nombre }}
                            @else   
                                {{ $modelo->marca->nombre . '*' }}
                                @php
                                    $hayNoDisponibles = true;
                                @endphp
                            @endif
                        {{-- @else
                            *****
                            @php
                                $hayInexistentes = true;
                            @endphp
                        @endif --}}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $modelo->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($modelo->disponible)
                            DISPONIBLE
                        @else
                            NO DISPONIBLE
                        @endif
                    </td>
                      <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        <a wire:click="editaModelo({{ $modelo->id }})" wire:target="editaModelo" title="Editar modelo" wire:loading.attr="disabled" style="color: dimgrey; cursor:pointer;" data-toggle="modal" data-target="#editarModeloModal"    
                            >
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a> 
                                          
                        <a wire:click="invertirEstatusModelo({{ $modelo->id }})" wire:loading.attr="disabled" wire:target="invertirEstatusModelo" style="color: dimgrey;cursor:pointer">
                            @if ($modelo->disponible)
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
        @if ($hayNoDisponibles)
        {{-- <div class="row">  --}}
            <div class="col-md-10">
                <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                    @if ($hayNoDisponibles)* NO DISPONIBLE @endif
                </label>
            </div>
        {{-- </div> --}}
        @endif
    </div>

    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $modelos->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>  //Abre la ventana modal y hace que el selectpicker sí tome el valor correcto
//     document.addEventListener('livewire:initialized', function () {
//     Livewire.on('abrirEditarModeloModal', function (tipoEquipoId, idMarca) {
//         console.log('tipoEquipoId:', tipoEquipoId); // Imprime el valor
//         console.log('idMarca:', idMarca); // Imprime el valor
//         // Abrir la ventana modal aquí usando JavaScript
//         $('#editarModeloModal').modal('show');
//         var valor = tipoEquipoId[0];
//         var marcaId = idMarca;
//         $('#idTipoEquipoModeloModal').selectpicker('val', valor);
//         $('#idMarcaModeloModal').selectpicker('val', marcaId);
//     });
// });

// document.addEventListener('livewire:initialized', function () {
    // Livewire.on('abrirEditarModeloModal', function (tipoEquipoId) {
        // Abrir la ventana modal aquí usando JavaScript
        // $('#editarModeloModal').modal('show');
        // var valor = tipoEquipoId[0];
        // $('#idTipoEquipoModeloModal').selectpicker('val', valor);
        // $('#idTipoEquipoModeloModal').selectpicker('refresh');
        // $('#idMarcaModeloModal').selectpicker('refresh');
    // });
// });

document.addEventListener('DOMContentLoaded', function () {
    // Hook para manejar el commit de Livewire
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        // Re-inicializar los selectpickers después de la actualización
        $('.selectpicker').selectpicker();

        succeed(({ snapshot, effect }) => {
            // Destruir y volver a inicializar los selectpickers
            $('select').selectpicker('destroy');
            queueMicrotask(() => {
                $('.selectpicker').selectpicker('refresh');
            });
        });

        fail(() => {
            console.error('Livewire commit failed');
        });
    });
});
</script>