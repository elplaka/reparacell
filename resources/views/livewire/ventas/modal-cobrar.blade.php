<div wire:ignore.self class="modal fade" id="cobrarModal" name="cobrarModal" tabindex="-1" role="dialog" aria-labelledby="cobrarModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                   <b> Cobrar VENTA </b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCobrar" wire:submit.prevent="cobrar">
                <div class="modal-body text-begin">
                    <div class="form-group bg-gray-200 text-center" style="font-size:14pt">
                        Total: <b>$<span id="total-amount">{{ number_format($totalCarrito, 2, '.', ',') }}</b></span>
                    </div>
                    <div class="form-group">
                        <label for="selectModoPago">Modo de Pago </label>
                        <select wire:model.live="idModoPago" id="selectModoPago" class="selectpicker select-picker w-100">
                            @foreach ($modosPagoModal as $modoPago)
                                <option value="{{ $modoPago->id }}" data-content="<i class='{{ $modoPago->icono }}'></i> &nbsp; {{ $modoPago->nombre }}"></option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnAceptar" type="submit" class="btn uppercase tracking-widest font-semibold text-xs" style="background-color: #007bff; color: white; border: none; padding: 6px 12px;">
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