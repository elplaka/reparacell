<div wire:ignore.self class="modal fade" id="nuevoDepartamentoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Agregar departamento</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraDepartamentoModal">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div wire:loading class="text-center">
               <i class="fa fa-spinner fa-spin"></i> Cargando...
               <br><br>
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
                       <label for="departamentoMod.nombre" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Nombre') }}</label>
                       <div class="col-md-8">
                           <input wire:model="departamentoMod.nombre" type="text" class="input-height form-control" id="departamentoMod.nombre" style="font-size:11pt;" autofocus>
                       </div>
                   </div>
               </div>
               <div class="modal-footer d-flex justify-content-center">
                   @if ($nuevoDepartamento)
                   <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="guardaDepartamento">Guardar</button>
                   @else
                   <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="actualizaDepartamento">Actualizar</button>
                   @endif
                   <button id="btnCerrarDepartamentoModal" class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="cierraDepartamentoModal">Cerrar</button>
               </div>
           </div>
       </div>
   </div>
</div>