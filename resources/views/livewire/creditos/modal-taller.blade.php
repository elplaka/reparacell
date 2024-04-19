@php
    use Carbon\Carbon;
@endphp
<div wire:ignore.self class="modal fade" id="cobroCreditoTallerModal" name="cobroCreditoTallerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Crédito de Taller </b> &nbsp;&nbsp; 
               @if ($cobroACredito['idEstatus'] == 1) 
               <span class="badge badge-danger">{{ $cobroACredito['estatus'] }}</span></h1>
               @else
               <span class="badge badge-success">{{ $cobroACredito['estatus'] }}</span></h1>
               @endif
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraCobroCreditoTallerModal">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div> 
           <div class="modal-body">
            @if ($showModalErrors)
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" wire:ignore>
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
           <div class="ml-0">
                <div class="container mt-0">
                    <div class="row mb-0">
                        <label class="col-md-8 block text-sm-left text-gray-700" style="font-size: 11pt;">{{ __('CLIENTE: ') }} <b> {{ $cobroACredito['nombreCliente'] }} </b></label>
                        <label class="col-md-4 block text-sm-left text-gray-700" style="font-size: 11pt;">{{ __('NUM. ORDEN: ') }} <b> {{ $cobroACredito['numOrden'] }} </b></label>
                    </div>
                    <div class="row mb-0">
                        <label class="col-md-11 block text-sm-left text-gray-700" style="font-size: 11pt;">{{ __('EQUIPO: ') }} <b> {{ $cobroACredito['tipoEquipo'] }} &nbsp;  <i class="fa-solid fa-grip-lines-vertical"></i> &nbsp; {{ $cobroACredito['marcaEquipo'] }} &nbsp; <i class="fa-solid fa-grip-lines-vertical"></i> &nbsp; {{ $cobroACredito['modeloEquipo'] }} </b></label>
                    </div>
                </div>
                <br>
                <div class="row mb-2">
                    <div class="col-md-4 block text-sm-left text-gray-700" style="font-size:12pt">
                        <b> HISTORIAL DE PAGOS </b>
                    </div>
                    @if ($muestraDivAbono)
                    <button class="btn btn-success md-2 uppercase tracking-widest font-semibold text-xs" wire:click="liquidaCredito" id="btn-liquidar" wire:ignore>LIQUIDAR</button>
                    <label for="cobroACredito.abono" class="col-md-2 block text-sm-right text-gray-700 pr-0" style="font-size:11pt;" id="label-abono" wire:ignore>{{ __('Abono $') }}</label>
                    <div class="col-md-2 ml-0" id="div-abono" wire:ignore>
                        <input wire:model="cobroACredito.abono" step="any" type="number" class="input-height form-control" style="font-size:11pt;">
                    </div>
                    <button class="btn btn-primary md-2 uppercase tracking-widest font-semibold text-xs" wire:click="agregaAbono" id="btn-agregar" wire:ignore.self wire:loading.attr="disabled"  onclick="hideDivAbono()">AGREGAR</button>
                    @else
                        @if ($cobroACredito['idEstatus'] == 1)
                        <div class="col-md-8 d-flex justify-content-end"> 
                            <a wire:ignore.self id="botonAgregarPago" class="btn btn-primary" title="Agregar abono" wire:loading.attr="disabled" wire:click="muestraDivAgregaAbono" onclick="ocultarBotonAgregarPago()">
                                <i class="fas fa-plus"></i> 
                            </a>
                        </div>
                        @endif
                    @endif
                </div>
           </div>

            {{-- <div class="table-responsive"> --}}
            <div class="table-responsive" style="max-height: calc(5 * 40px); overflow-y: auto;">
                <table class="w-full table table-bordered table-hover">
                    <thead style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th class="px-2 py-2 bg-gray-200 text-center text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CONCEPTO</th>
                            <th class="px-2 py-2 bg-gray-200 text-center text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MONTO</th>
                            <th class="px-2 py-2 bg-gray-200 text-center text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FECHA</th>
                            <th class="px-2 py-2 bg-gray-200 text-center text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                    {{-- <tbody @if($modalCobroCreditoTallerAbierta) wire:poll @endif> --}}
                        @if($detallesCredito !== null)
                        @foreach ($detallesCredito as $detalles)
                        @php
                            $fechaOriginal = $detalles->created_at;
                            // Convertir a objeto Carbon
                            $fechaCarbon = Carbon::parse($fechaOriginal);
                            // Formatear la fecha
                            $fechaFormateada = $fechaCarbon->format('d/m/Y H:i:s');
                        @endphp
                        <tr style="font-size: 10pt;">
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                                @if ($detalles->id_abono == 0)
                                ANTICIPO
                                @else
                                    @if ($detalles->abono < 0)
                                    DEVOLUCIÓN
                                    @else
                                        @if ($detalles == $detallesCredito->last() && $cobroACredito['idEstatus'] == 2)
                                        LIQUIDACIÓN
                                        @else
                                        ABONO
                                        @endif
                                    @endif
                                @endif 
                            </td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="text-align: right; vertical-align: middle">
                                $ {{ number_format(abs($detalles->abono), 2, '.', ',')  }}
                            </td>
                             <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                                {{ $fechaFormateada }}
                            </td>
                            <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                                @if ($detalles->id_abono > 0)
                                <button wire:click="preguntaBorraAbono('{{ $detalles->num_orden }}', '{{ $detalles->id_abono }}')" wire:loading.remove wire:target="borraAbono" class="label-button">
                                    <i class="fa-solid fa-trash-can" style="color:dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Borrar abono"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($detallesCredito)
            <br>
            <hr>
            <div class="row mt-2 mb-3">
                <div class="col-md-6 block text-sm-left text-gray-700" style="font-size:12pt">
                    MONTO CUBIERTO: <b> ${{ number_format($sumaAbonos, 2, '.', ',') }} </b>
                </div>
                <div class="col-md-4 block text-sm-left text-gray-700" style="font-size:12pt">
                    MONTO A LIQUIDAR: <b> ${{ number_format($montoLiquidar, 2, '.', ',') }} </b>
                </div>
            </div>
            @endif
                <!-- Modal Footer con Botón de Cierre -->
                {{-- <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="irCorteCaja" target="_blank">Generar</button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarCorteCajaModal" wire:click="cierraCorteCajaModal">Cerrar</button>
                </div> --}}
            </div>
       </div>
    </div>
</div>