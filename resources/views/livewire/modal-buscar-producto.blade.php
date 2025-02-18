<div wire:ignore.self class="modal fade" id="buscarProductoModal" name="buscarProductoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Buscar producto</b></h1> &nbsp;
               <div wire:loading class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> Cargando...
                </div>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraBuscarProductoModal">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           {{-- <div wire:loading class="text-center">
               <i class="fa fa-spinner fa-spin"></i> Cargando...
               <br><br>
           </div> --}}
           @if($showModalErrors)
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
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
           <div class="modal-body">
               <div class="container mt-3 font-sans text-gray-900 antialiased">
                <div class="row mb-3 justify-content-center">
                    <div class="col-md-6"> <!-- Ajusta este valor para ocupar la mitad del ancho -->
                        <div class="input-group">
                            <input placeholder="Descripción del producto" wire:keydown.enter="executeRender" wire:model="descripcionProductoModal" type="text" class="select-height form-control" id="descripcionProductoModal" style="font-size:11pt;" autofocus>
                            <div class="input-group-append">
                                <button wire:click="executeRender" class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" id="button-addon2">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>                   
                    </div>
                </div>
                    <div class="row mb-3">
                        @if (isset($productosModal))
                            <div class="table-responsive" wire:loading.remove>
                                <table class="table table-sm table-hover table-bordered">
                                    <thead>
                                        <tr>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                            Código
                                        </th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                            Descripción
                                        </th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                            Precio
                                        </th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                            Departamento
                                        </th>
                                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                            Inv.
                                        </th>
                                    </thead>
                                    <tbody>
                                        @foreach($productosModal as $productoModal)
                                        {{-- <tr style="font-size: 9pt; cursor: pointer; @if ($productoModal->inventario == 0) color:red; @endif" wire:click="gotoPageAndCapture('{{ $productoModal->codigo }}', {{ $productosModal->currentPage() }})"> --}}
                                        <tr style="font-size: 9pt; @if ($productoModal->inventario == 0) color:red; cursor: default; pointer-events: none; @else cursor: pointer; @endif" 
                                        @if ($productoModal->inventario != 0) wire:click="gotoPageAndCapture('{{ str_replace("'", "\'", $productoModal->codigo) }}', {{ $productosModal->currentPage() }})" @endif>
                                            <td class="px-2 py-1 whitespace-no-wrap" style="color: @if ($productoModal->inventario == 0) red; @else blue @endif; font-weight: bold;">
                                                {{ $productoModal->codigo }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap" style="color: @if ($productoModal->inventario == 0) red; @else green @endif; font-weight: bold;">
                                                {{ $productoModal->descripcion }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap" style="color: @if ($productoModal->inventario == 0) red; @else orange @endif; font-weight: bold;">
                                                {{ $productoModal->precio_venta }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap" style="color: @if ($productoModal->inventario == 0) red; @else purple @endif; font-weight: bold;">
                                                {{ $productoModal->departamento->nombre }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap" style="color: @if ($productoModal->inventario == 0) red; @else teal @endif; font-weight: bold;">
                                                {{ $productoModal->inventario == -1 ? 'N/A' : $productoModal->inventario }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    @if (!is_null($productosModal))
                    <div class="col-mx">
                        <label class="col-form-label float-left">
                            {{ $productosModal->links('livewire.paginame') }}
                        </label>
                    </div> 
                    @endif
                </div>
           </div>
           <div class="modal-footer d-flex justify-content-center">
            <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarBuscarProductoModal" wire:click="cierraBuscarProductoModal">Cerrar</button>
            </div>
       </div>
   </div>
</div>

