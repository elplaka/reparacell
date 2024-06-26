<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-nueva-falla')
    @include('livewire.equipos.modal-editar-falla')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-plug-circle-exclamation"></i>
            Fallas de Equipos</b></h4>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaFalla" title="Agregar falla" wire:loading.attr="disabled" wire:target="abreAgregaFalla" data-toggle="modal" data-target="#nuevaFallaModal">
                <i class="fas fa-plus"></i>
            </a>
    </div>

    <div class="row">  {{-- El wire:ignore en el div exterior evita que desaparezcan los selectpickers de dentro --}}
        <div class="col-md-3 mb-3">
            <label for="filtrosFallas.idTipoEquipo" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Tipo Equipo </label>
            <select wire:model.live="filtrosFallas.idTipoEquipo" class="selectpicker select-picker w-100" id="filtrosFallas.idTipoEquipo" style="font-size:11pt;">
            <option value="0"> -- TODOS -- </option> 
            @foreach ($tipos_equipos as $tipo_equipo)
                <option value="{{ $tipo_equipo->id }}" data-content="{{  $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
            @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosFallas.descripcion" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Descripción </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosFallas.descripcion" style="font-size:11pt;">
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosFallas.disponible" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Estatus</label>
            <select wire:model.live="filtrosFallas.disponible" class="selectpicker select-picker w-100" id="filtrosFallas.disponible" style="font-size:11pt;">
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
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DESCRIPCIÓN</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CVE. DESCRIPCIÓN</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">COSTO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fallas as $falla)
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {!! $falla->tipoEquipo->icono !!} &nbsp; {{ $falla->tipoEquipo->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $falla->descripcion }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $falla->cve_descripcion }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                      $ {{ number_format($falla->costo, 2, '.', ',') }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($falla->disponible)
                            DISPONIBLE
                        @else
                            NO DISPONIBLE
                        @endif
                      </td>
                      <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{-- <a wire:ignore.self wire:click="editaFalla({{ $falla->id }})" title="Editar falla" wire:loading.attr="disabled" wire:target="editaFalla" style="color: dimgrey;cursor:pointer" data-toggle="modal" data-target="#editarFallaModal" >
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a> --}}
                        <a wire:click="editaFalla({{ $falla->id }})" title="Editar falla {{ $falla->id }}" wire:loading.attr="disabled" wire:target="editaFalla" style="color: dimgrey;cursor:pointer" data-toggle="modal" data-target="#editarFallaModal">
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a>
                        
                        <a wire:click="invertirEstatusFalla({{ $falla->id }})" wire:loading.attr="disabled" wire:target="invertirEstatusFalla" style="color: dimgrey;cursor:pointer">
                            @if ($falla->disponible)
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
            {{ $fallas->links('livewire.paginame') }}
        </label>
    </div> 

    <!-- Modal Footer con Botón de Cierre -->
    {{-- <div class="modal-footer d-flex justify-content-center">
        <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="irCorteCaja" target="_blank">Generar</button>
        <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarCorteCajaModal" wire:click="cierraCorteCajaModal">Cerrar</button>
    </div> --}}
</div>

<script>  //Abre la ventana modal y hace que el selectpicker sí tome el valor correcto
//     document.addEventListener('livewire:initialized', function () {
//     Livewire.on('abrirEditarFallaModal', function (tipoEquipoId) {
//         // Abrir la ventana modal aquí usando JavaScript
//         $('#editarFallaModal').modal('show');
//         var valor = tipoEquipoId[0];
//         $('#idTipoEquipoFallaModal').selectpicker('val', valor);
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

