@php
    use Carbon\Carbon;
@endphp
<div wire:ignore.self class="modal fade" id="editarModoPagoModal" name="editarModoPagoModal" tabindex="-1" role="dialog" aria-labelledby="editarModoPagoModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
    @if (!is_null($ventaModal))
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                   <b> Editar MODO DE PAGO de la VENTA </b>
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
                        Cliente: <b><span>{{ $ventaModal->cliente->nombre }}</b></span>
                    </div>
                    <div class="form-group bg-gray-200 text-center mb-1" style="font-size:12pt">
                        Total: <b>$<span>{{ number_format($ventaModal->total, 2, '.', ',') }}</b></span>
                    </div>
                    <div class="form-group">
                        <label for="selectModoPago">Modo de Pago </label>
                        <select wire:model.live="idModoPago" id="selectModoPago3" class="selectpicker select-picker w-100">
                            @foreach ($modosPago as $modoPago)
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
    @endif
    </div>
</div>