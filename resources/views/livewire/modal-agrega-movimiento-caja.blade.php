<div wire:ignore.self class="modal fade" id="agregaMovimientoModal" name="agregaMovimientoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Nuevo Movimiento de Caja</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click='cerrarModal'>
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           
           <div class="modal-body">
                <div class="mb-3">
                    <label for="nuevoMovimiento.idTipo" class="form-label">Tipo</label>
                    <select wire:model.live="nuevoMovimiento.idTipo" id="selectMovimiento" type="text" class="select-height form-control w-100" required>
                        <option value="">-- SELECCIONA UN TIPO --</option>
                        @foreach ($tiposMovimientoModal as $tipoMovimiento)
                            <option value="{{ $tipoMovimiento->id }}">{{ $tipoMovimiento->nombre }}</option>
                        @endforeach
                    </select>
                    @error('nuevoMovimiento.idTipo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label for="nuevoMovimiento.monto" class="form-label">Monto</label>
                    <input type="number" step="any" wire:model="nuevoMovimiento.monto" class="form-control" style="color: rgb(83, 83, 83); font-size: 11pt;" required>
                    @error('nuevoMovimiento.monto') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="agregaMovimiento" target="_blank">Aceptar</button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click='cerrarModal'>Cerrar</button>
                </div>
            </div>
       </div>
    </div>
</div>