<div wire:ignore.self class="modal fade" id="nuevaFallaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Agregar falla</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraFallaModal">
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
                    <label for="fallaMod.idTipoEquipo" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Tipo Equipo') }}</label>
                    <div class="col-md-8 d-flex">
                        <select wire:model.live="fallaMod.idTipoEquipo" class="selectpicker select-picker w-100" id="fallaMod.idTipoEquipo" style="font-size: 11pt;">
                            @foreach ($tipos_equipos as $tipo_equipo)
                                <option value="{{ $tipo_equipo->id }}" data-content="{{ $tipo_equipo->icono }} &nbsp; {{ $tipo_equipo->nombre }}"></option>
                            @endforeach
                        </select>
                    </div>
                </div>
                   <div class="row mb-3">
                       <label for="fallaMod.descripcion" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Descripción') }}</label>
                       <div class="col-md-8">
                           <input wire:model="fallaMod.descripcion" type="text" class="input-height form-control" id="fallaMod.descripcion" style="font-size:11pt;" autofocus>
                       </div>
                   </div>
                   <div class="row mb-3">
                        <label for="fallaMod.cveDescripcion" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Cve. Descripción') }}</label>
                        <div class="col-md-8">
                            <input wire:model="fallaMod.cveDescripcion" type="text" class="input-height form-control" id="fallaMod.cveDescripcion" style="font-size:11pt;" autofocus>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="fallaMod.costo" class="col-md-4 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Costo') }}</label>
                        <div class="col-md-8">
                            <input wire:model="fallaMod.costo" type="number" class="input-height form-control" id="fallaMod.costo" style="font-size:11pt;" autofocus>
                        </div>
                    </div>
               </div>
               <div class="modal-footer d-flex justify-content-center">
                   @if (!$guardoFallaOK)
                   <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="guardaFalla">Guardar</button>
                   @endif
                   <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="cierraFallaModal">Cerrar</button>
               </div>
           </div>
       </div>
   </div>
</div>