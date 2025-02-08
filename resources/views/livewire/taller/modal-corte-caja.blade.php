<div wire:ignore.self class="modal fade" id="corteCajaModal" name="corteCajaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Corte de Caja de Taller</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraModalCorteCaja">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           
           {{-- <a href="{{ url('/caja/corte') }}" wire:click="irCorteCaja" class="btn btn-primary mb-3" target="_blank">Generar PDF</a> --}}

            
           <div class="modal-body">
                <!-- Seleccionar Fecha Inicial -->
                <div class="mb-3">
                    <label for="corteCaja.fechaInicial" class="form-label">Fecha Inicial</label>
                    <input type="date" class="form-control" id="fechaInicial" wire:model="corteCaja.fechaInicial">
                </div>

                <!-- Seleccionar Fecha Final -->
                <div class="mb-3">
                    <label for="corteCaja.fechaFinal" class="form-label">Fecha Final</label>
                    <input type="date" class="form-control" id="fechaFinal" wire:model="corteCaja.fechaFinal">
                </div>

                <div class="mb-3">
                    <label for="corteCaja.idModoPagoCorte" class="form-label">Modo Pago</label>
                    {{-- {{ $corteCaja['idModoPago' ]}} --}}
                    <select wire:model.live="corteCaja.idModoPago" id="selectModoPagoCorte" class="selectpicker select-picker w-100">
                        @foreach ($modosPagoModal as $modoPago)
                            <option value="{{ $modoPago->id }}" data-content="<i class='{{ $modoPago->icono }}'></i> &nbsp; {{ $modoPago->nombre }}"></option>
                        @endforeach
                    </select>
                </div>

                @role('admin')
                <div class="mb-3">
                    <label for="corteCaja.idUsuario" class="form-label">Recibió Equipo(s)</label>
                    <select wire:model="corteCaja.idUsuario" type="text" class="select-height form-control w-100">
                        <option value="0">--TODOS--</option>
                        @foreach ($usuariosModal as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endrole

                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label for="chkCobrosTaller" class="form-label">
                            <input type="checkbox" id="chkCobrosTaller" wire:model="corteCaja.incluyeVentas" checked>
                            Incluir Ventas
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label for="chkAbonos" class="form-label">
                            <input type="checkbox" id="chkAbonos" wire:model="corteCaja.incluyeCredito" checked>
                            Incluir Abonos
                        </label>
                    </div>
                </div>

                <!-- Modal Footer con Botón de Cierre -->
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="irCorteCaja" target="_blank">Generar</button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarCorteCajaModal" wire:click="cierraModalCorteCaja">Cerrar</button>
                </div>
            </div>
       </div>
    </div>
</div>