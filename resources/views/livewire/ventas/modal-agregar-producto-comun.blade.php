<div wire:ignore.self class="modal fade" id="productoComunModal" name="productoComunModal" tabindex="-1" role="dialog" aria-labelledby="anotacionesModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                   <b> Agregar producto común </b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form wire:submit.prevent="agregaProductoComun">
                <div class="modal-body text-begin" 
                {{-- wire:loading.remove --}}
                >
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" id="cantidad" class="form-control" wire:model="cantidadProductoComun" min="1" step="1">
                        @error('cantidadProductoComun') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="descripcionProducto">Descripción</label>
                        <input type="text" id="descripcionProducto" class="form-control" wire:model="descripcionProductoComun" autofocus>
                        @error('descripcionProductoComun') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="importe">Importe</label>
                        <input type="number" id="monto" class="form-control" wire:model="montoProductoComun" min="0" step="0.50" onblur="this.value = parseFloat(this.value).toFixed(2)">
                        @error('montoProductoComun') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer"> 
                    <button type="submit" 
                    class="btn uppercase tracking-widest font-semibold text-xs" 
                    style="background-color: #007bff; color: white; border: none; padding: 6px 12px;"> 
                        Aceptar 
                    </button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

