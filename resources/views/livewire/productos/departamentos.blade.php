<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.productos.modal-departamento')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-layer-group"></i>
            Departamentos</b></h4>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaDepartamento" title="Agregar departamento" wire:loading.attr="disabled" wire:target="abreAgregaDepartamento" data-toggle="modal" data-target="#nuevoDepartamentoModal">
                <i class="fas fa-plus"></i>
            </a>
    </div>
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
    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">NOMBRE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departamentos as $departamento)
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $departamento->nombre }}
                    </td> 
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($departamento->disponible)
                            DISPONIBLE
                        @else
                            NO DISPONIBLE
                        @endif
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                    <a wire:click.prevent="editaDepartamento({{ $departamento->id }})" title="Editar departamento" wire:loading.attr="disabled" wire:target="editaDepartamento" style="color: dimgrey; cursor:pointer;" data-toggle="modal" data-target="#nuevoDepartamentoModal">
                        <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                    </a>                  
                    <a wire:click="invertirEstatusDepartamento({{ $departamento->id }})" wire:loading.attr="disabled" wire:target="invertirEstatusDepartamento" style="color: dimgrey;cursor:pointer">
                        @if ($departamento->disponible)
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
            {{ $departamentos->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalDepartamento', () => {
        document.getElementById('btnCerrarDepartamentoModal').click();
            })
    });
</script>







