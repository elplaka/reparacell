<div wire:ignore.self class="modal fade" id="nuevoTipoEqModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Agregar tipo de equipo</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraTipoEqModal">
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
                       <label for="tipoEquipoMod.nombre" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Nombre') }}</label>
                       <div class="col-md-8">
                           <input wire:model="tipoEquipoMod.nombre" type="text" class="input-height form-control" id="tipoEquipoMod.nombre" style="font-size:11pt;" placeholder="Ej. TABLET, COMPUTADORA" autofocus>
                       </div>
                   </div>
                   <div class="row mb-2">
                        <label for="tipoEquipoMod.cveNombre" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Cve. Nombre') }}</label>
                        <div class="col-md-8">
                            <input wire:model="tipoEquipoMod.cveNombre" type="text" class="input-height form-control" id="tipoEquipoMod.cveNombre" style="font-size:11pt;" placeholder="Abreviatura del nombre. Ej. TAB, COM" autofocus>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <label for="tipoEquipoMod.icono" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Ícono') }}</label>
                        <div class="col-md-8">
                            <input wire:model="tipoEquipoMod.icono" type="text" class="input-height form-control" id="tipoEquipoMod.icono" style="font-size:11pt;" placeholder='Ej. <i class="fa-solid fa-laptop"></i>' autofocus>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-4 block text-sm-right text-gray-700 pr-0">
                        </label>
                        <div class="col-md-8">
                        <a href="https://fontawesome.com/search?o=r&m=free&s=solid" target="_blank" style="font-size:9pt">Enlace a base de datos de Íconos FontAwesome</a>
                        </div>
                    </div>
               </div>
               <div class="modal-footer d-flex justify-content-center">
                   @if ($nuevoTipo)
                   @if (!$guardoTipoEquipoOK)
                   <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="guardaTipoEquipo">Guardar</button>
                   @endif
                   @else
                   <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="actualizaTipoEquipo">Actualizar</button>
                   @endif
                   <button id="btnCerrarTipoEquipoModal" class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="cierraTipoEqModal">Cerrar</button>
               </div>
           </div>
       </div>
   </div>
</div>