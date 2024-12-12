<div wire:ignore.self class="modal fade" id="cambiaEstatusEquipoModal" name="cambiaEstatusEquipoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Cambiar estatus del equipo en el taller</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraCambiaEstatusEquipoModal">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           
           @if ($modalCambiarEstatusEquipoAbierta)
           <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                    <tbody>
                        <tr>
                            <td class="text-right leading-4 font-bold text-gray-700"><strong>Tipo de Equipo:</strong></td>
                            <td class="leading-4 font-bold text-gray-700">{{ $equipoTallerModal->equipo->tipo_equipo->nombre }}</td>
                        </tr>
                        <tr>
                        <td class="text-right leading-4 font-bold text-gray-700"><strong>Marca:</strong></td>
                        <td class="leading-4 font-bold text-gray-700">{{ $equipoTallerModal->equipo->marca->nombre }}</td>
                        </tr>
                        <tr>
                        <td class="text-right leading-4 font-bold text-gray-700"><strong>Modelo:</strong></td>
                        <td class="leading-4 font-bold text-gray-700">{{ $equipoTallerModal->equipo->modelo->nombre }}</td>
                        </tr>
                        <tr>
                        <td class="text-right leading-4 font-bold text-gray-700"><strong>Cliente:</strong></td>
                        <td class="leading-4 font-bold text-gray-700">{{ $equipoTallerModal->equipo->cliente->nombre }}</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                <div class="d-flex flex-column align-items-center col-md-8 mb-3 mx-auto">
                    <div class="d-flex align-items-center w-100 mb-1">
                        @if ($estatusModalCambiaEstatus == 0)
                            <i class="fas fa-list mr-2"></i>
                        @else
                            {!! $this->obtenerIconoSegunEstatus($estatusModalCambiaEstatus) !!}
                        @endif
                        &nbsp;
                        <select wire:model.live="estatusModalCambiaEstatus" class="select-height form-control w-100" style="border: 1px solid rgb(93, 90, 90) !important; border-radius: inherit !important; font-size: 11pt !important; font-weight: bold; cursor: pointer; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(0, 0, 0, 0.05)'" onmouseout="this.style.backgroundColor=''" required>
                            <option value="0" selected>--SELECCIONA UN ESTATUS--</option>
                            @foreach ($estatus_equipos as $estatus)
                                @if ($estatus->id <= 4)
                                    <option value="{{ $estatus->id }}">{{ $estatus->descripcion }}</option>
                                @endif
                            @endforeach 
                        </select>
                    </div>
                    @if ($errors->has('estatusModalCambiaEstatus'))
                        <div class="text-danger mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $errors->first('estatusModalCambiaEstatus') }}
                        </div>
                    @endif
                </div>
            
                <!-- Modal Footer con Botón de Cierre -->
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="cambiarEstatusEquipo" target="_blank">Aceptar</button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarCambiaEstatusEquipoModal" wire:click="cierraCambiaEstatusEquipoModal">Cerrar</button>
                </div>
            </div>
            @endif
       </div>
    </div>
</div>