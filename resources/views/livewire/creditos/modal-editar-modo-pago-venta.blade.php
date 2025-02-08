@php
    use Carbon\Carbon;
@endphp
<div wire:ignore.self class="modal fade" id="editarModoPagoModalVentaCredito" name="editarModoPagoModalVentaCredito" tabindex="-1" role="dialog" aria-labelledby="editarModoPagoModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document" style="border: 5px solid var(--primary); border-radius: 10px;">
    @if (!is_null($ventaModal))
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                   <b> Editar MODO DE PAGO de la VENTA a CRÉDITO </b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCobrar" wire:submit.prevent="actualizarModoPago">
                @php
                    $fechaOriginal = $ventaModal->created_at;
                    $fechaCarbon = Carbon::parse($fechaOriginal);
                    $fechaFormateada = $fechaCarbon->format('d/m/Y H:i:s');

                @endphp
                <div class="modal-body text-begin">
                    <div class="form-group bg-gray-200 text-center mb-1" style="font-size:12pt">
                        Id. Venta: <b><span>{{ $ventaModal->id }}</b></span> &nbsp; &nbsp;
                        Fecha: <b><span>{{ $fechaFormateada }}</b></span>
                    </div>
                    <div class="form-group bg-gray-200 text-center mb-1" style="font-size:12pt">
                        Cliente: <b><span>{{ $ventaModal->ventaCredito->venta->cliente->nombre }}</b></span>
                    </div>
                    <div class="form-group bg-gray-200 text-center mb-1" style="font-size:12pt">
                        Abono: <b>$<span>{{ number_format($ventaModal->abono, 2, '.', ',') }}</b></span>
                    </div>
                    <div class="form-group">
                        <label for="selectModoPago">Modo de Pago </label>
                        <select wire:model="idModoPago" id="selectModoPago3" class="selectpicker select-picker w-100">
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
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click='cierraModalActualizarModoPago'>
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    @endif
    </div>
</div>