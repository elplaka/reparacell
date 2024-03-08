<div wire:ignore.self class="modal fade" id="editarClienteModal" name="editarClienteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Editar cliente</b></h1> &nbsp; &nbsp;
               <div wire:loading class="text-center">
                <i class="fa fa-spinner fa-spin"></i> Cargando...
                <br><br>
                </div>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraEditarClienteModal">
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
                        <div class="col-md-4 mb-3">
                            <label for="clienteModalEdit.id" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Id </label>
                            <input wire:model="clienteModalEdit.id" type="text" class="input-height form-control w-100" id="clienteModalEdit.id" style="font-size:11pt;" readonly autofocus>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="clienteModalEdit.telefonoId" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Teléfono Id </label>
                            <input wire:model="clienteModalEdit.telefonoId" type="text" class="input-height form-control w-100" id="clienteModalEdit.telefonoId" style="font-size:11pt;" autofocus>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="clienteModalEdit.telefonoContacto" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Teléfono Contacto </label>
                            <input wire:model="clienteModalEdit.telefonoContacto" type="text" class="input-height form-control w-100" id="clienteModalEdit.telefonoContacto" style="font-size:11pt;" autofocus>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="clienteModalEdit.nombre" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Nombre </label>
                            <input wire:model="clienteModalEdit.nombre" type="text" class="input-height form-control w-100" id="clienteModalEdit.nombre" style="font-size:11pt;" autofocus>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="clienteModalEdit.direccion" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Dirección </label>
                            <input wire:model="clienteModalEdit.direccion" type="text" class="input-height form-control w-100" id="clienteModalEdit.direccion" style="font-size:11pt;" autofocus>
                        </div>
                        
                    </div>
                   <!-- Modal Footer con Botón de Cierre -->
                    <div class="modal-footer d-flex justify-content-center">
                        <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="actualizaCliente" target="_blank">Actualizar</button>
                        <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarEditarClienteModal" wire:click="cierraEditarClienteModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>