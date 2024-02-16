<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.productos.modal-nuevo')
    {{-- @include('livewire.modal-buscar-cliente')
    @include('livewire.modal-corte-caja') --}}
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-kitchen-set"></i> Productos</b></h4>
        <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaProducto" title="Agregar producto" wire:loading.attr="disabled" wire:target="abreAgregaProducto" data-toggle="modal" data-target="#nuevoProductoModal">
            <i class="fas fa-plus"></i>
        </a>
    </div>
    <div class="row" wire:ignore>  {{-- El wire:ignore en el div exterior evita que desaparezcan los selectpickers de dentro --}}
        <div class="col-md-3 mb-3">
            <label for="filtrosProductos.descripcion" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Descripción </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosProductos.descripcion" style="font-size:11pt;">
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosProductos.idDepartamento" class="form-label text-gray-700" style="font-weight:500; font-size:11pt"> Departamento </label>
            <select wire:model.live="filtrosProductos.idDepartamento" class="selectpicker select-picker w-100" style="font-size:11pt;" title="-- TODOS --" multiple>
            @foreach ($departamentos as $departamento)
                <option value="{{ $departamento->id }}" data-content="{{ $departamento->nombre }}"></option>
            @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosProductos.disponible" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Estatus</label>
            <select wire:model.live="filtrosProductos.disponible" class="selectpicker select-picker w-100" style="font-size:11pt;">
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
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CÓDIGO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DESCRIPCIÓN</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">PRECIO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">INVENTARIO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DEPARTAMENTO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $producto->codigo }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $producto->descripcion }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                       $ {{ $producto->precio_venta }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $producto->inventario }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $producto->departamento->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($producto->disponible)
                            DISPONIBLE
                        @else
                            NO DISPONIBLE
                        @endif
                      </td>
                      {{-- <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        <a wire:click.prevent="editaMarca({{ $marca->id }})" title="Editar marca" wire:loading.attr="disabled" wire:target="editaMarca" style="color: dimgrey; cursor:pointer;">
                            <i class="fa-solid fa-file-pen" style="color: dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"></i>
                        </a>                    
                        <a wire:click="invertirEstatusMarca({{ $marca->id }})" wire:loading.attr="disabled" wire:target="invertirEstatusMarca" style="color: dimgrey;cursor:pointer">
                            @if ($marca->disponible)
                            <i class='fa-solid fa-rectangle-xmark' style="color: dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Poner NO DISPONIBLE"></i>
                            @else
                            <i class='fa-solid fa-square-check' style="color: dimgrey;" onmouseover="this.style.color='green'" onmouseout="this.style.color='dimgrey'" title="Poner DISPONIBLE"></i>
                            @endif
                        </a>
                      </td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $productos->links('livewire.paginame') }}
        </label>
    </div> 
</div>