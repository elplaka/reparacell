<div wire:ignore.self class="modal fade" id="editarProductoModal" name="editarProductoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Editar producto</b></h1> &nbsp; &nbsp;
               <div wire:loading class="text-center">
                <i class="fa fa-spinner fa-spin"></i> Cargando...
                <br><br>
                </div>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraEditarProductoModal">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
     
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
            <div class="modal-body" wire:loading.remove>
                <div class="container mt-3">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="productoMod.codigo" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Código </label>
                            <input wire:model="productoMod.codigo" type="text" class="input-height form-control w-100" id="productoMod.codigo" style="font-size:11pt;" readonly autofocus>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="productoMod.descripcion" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Descripción </label>
                            <input wire:model="productoMod.descripcion" type="text" class="input-height form-control w-100" id="productoMod.descripcion" style="font-size:11pt;" autofocus>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="productoMod.precioCosto" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Precio Costo </label>
                            <input wire:model="productoMod.precioCosto" type="number" step="0.5" class="input-height form-control w-100" id="productoMod.precioCosto" style="font-size:11pt;" autofocus>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="productoMod.precioVenta" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Precio Venta </label>
                            <input wire:model="productoMod.precioVenta" type="number" step="0.5" class="input-height form-control w-100" id="productoMod.precioVenta" style="font-size:11pt;" autofocus>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="productoMod.precioMayoreo" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Precio Mayoreo </label>
                            <input wire:model="productoMod.precioMayoreo" type="number" step="0.5" class="input-height form-control w-100" id="productoMod.precioMayoreo" style="font-size:11pt;" autofocus>
                        </div>
                        <div class="col-md-6 mb-3" wire:ignore>
                            <label for="productoMod.idDepartamento" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Departamento </label>
                            <select wire:model="productoMod.idDepartamento" type="text" class="select-height form-control w-100" id="productoMod.idDepartamento" style="font-size:11pt;" id="idDepartamentoProductoModal" autofocus>
                                @foreach ($departamentos as $departamento)
                                    <option value='{{ $departamento->id }}'> {{ $departamento->nombre }}</option>
                                    {{-- <option value='{{ $departamento->id }}' {{ $productoMod['idDepartamento'] == $departamento->id ? 'selected' : '' }}>
                                        {{ $departamento->nombre }}
                                    </option> --}}
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="productoMod.inventario" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Inventario </label>
                            <input wire:model="productoMod.inventario" type="number" step="1" class="input-height form-control w-100" id="productoMod.inventario" style="font-size:11pt;" autofocus>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="productoMod.inventarioMinimo" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Inventario Mínimo </label>
                            <input wire:model="productoMod.inventarioMinimo" type="number" step="1" class="input-height form-control w-100" id="productoMod.inventarioMinimo" style="font-size:11pt;" autofocus>
                        </div>
                    </div>
                    <!-- Modal Footer con Botón de Cierre -->
                    <div class="modal-footer d-flex justify-content-center">
                        <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="actualizaProducto" target="_blank">Actualizar</button>
                        <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarEditarProductoModal" wire:click="cierraEditarProductoModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>