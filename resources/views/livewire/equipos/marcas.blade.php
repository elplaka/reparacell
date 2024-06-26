<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-nueva-marca')
    @include('livewire.equipos.modal-editar-marca')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-splotch"></i>
            Marcas de Equipos</b></h4>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaMarca" title="Agregar marca" wire:loading.attr="disabled" wire:target="abreAgregaMarca" data-toggle="modal" data-target="#nuevaMarcaModal">
                <i class="fas fa-plus"></i>
            </a>
    </div>

    <div class="row">  
        <div class="col-md-3 mb-3">
            <label for="filtrosMarcas.idTipoEquipo" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Tipo Equipo </label>
            <select wire:model.live="filtrosMarcas.idTipoEquipo" class="selectpicker select-picker w-100" id="filtrosMarcas.idTipoEquipo" style="font-size:11pt;">
            <option value="0"> -- TODOS -- </option> 
            @foreach ($tipos_equipos as $tipo_equipo)
                <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
            @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label for="filtrosMarcas.nombre" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Nombre </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosMarcas.nombre" style="font-size:11pt;">
        </div>

        <div class="col-md-3 mb-3">
            <label for="filtrosMarcas.disponible" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Estatus</label>
            <select wire:model.live="filtrosMarcas.disponible" class="selectpicker select-picker w-100" id="filtrosMarcas.disponible" style="font-size:11pt;">
                <option value="-1"> -- TODOS -- </option> 
                <option value="0" data-content="<i class='fa-solid fa-rectangle-xmark'></i> &nbsp; NO DISPONIBLE"></option>
                <option value="1" data-content="<i class='fa-solid fa-square-check'></i> &nbsp; DISPONIBLE"></option>
            </select>
        </div>

        <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
    </div>

    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TIPO EQUIPO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">NOMBRE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($marcas as $marca)
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {!! $marca->tipoEquipo->icono !!} &nbsp; {{ $marca->tipoEquipo->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $marca->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($marca->disponible)
                            DISPONIBLE
                        @else
                            NO DISPONIBLE
                        @endif
                      </td>
                      <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        <a wire:click.prevent="editaMarca({{ $marca->id }})" title="Editar marca" wire:loading.attr="disabled" wire:target="editaMarca" style="color: dimgrey; cursor:pointer;" data-toggle="modal" data-target="#editarMarcaModal" >
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a>                    
                        <a wire:click="invertirEstatusMarca({{ $marca->id }})" wire:loading.attr="disabled" wire:target="invertirEstatusMarca" style="color: dimgrey;cursor:pointer">
                            @if ($marca->disponible)
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

    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $marcas->links('livewire.paginame') }}
        </label>
    </div> 
</div>



<script>  //Abre la ventana modal y hace que el selectpicker sí tome el valor correcto
//     document.addEventListener('livewire:initialized', function () {
//     Livewire.on('abrirEditarMarcaModal', function (tipoEquipoId) {
//         // Abrir la ventana modal aquí usando JavaScript
//         $('#editarMarcaModal').modal('show');
//         var valor = tipoEquipoId[0];
//         $('#idTipoEquipoMarcaModal').selectpicker('val', valor);
//     });
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



