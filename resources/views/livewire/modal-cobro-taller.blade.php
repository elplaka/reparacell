 <div wire:ignore.self class="modal fade" id="cobroTallerModal" name="cobroTallerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog">
        <div class="modal-content" style="border: 1px solid #ccc;">
            <div class="modal-header bg-light border-bottom border-gray-300">
                <h1 class="text-xl font-bold text-uppercase"><b> COBRO DE SALIDA DE EQUIPO </b></h1>       
                
                {{-- <div wire:loading class="text-center">
                    &nbsp;&nbsp; <i class="fa fa-spinner fa-spin"></i> Cargando...
                </div> --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraCobroFinalModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
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
            {{-- <div class="modal-body" style="padding: 15px;" wire:loading.remove> --}}
            <div class="modal-body" style="padding: 15px; display: {{ $datosCobroCargados ? 'block' : 'none' }}">
                <div class="container-fluid">
                    <div class="row mb-2" style="border-bottom: 1px solid #ccc;">
                        <div class="col-md-2 p-0" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3;  border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> NUM. ORDEN  </b> </label>
                            <div class="d-flex">
                                <label for="" class="pl-2 my-auto" style="font-size:11pt;">
                                    {{ $cobroFinal['numOrden']}}
                                </label>
                            </div>
                        </div>                        
                        <div class="col-md-8 p-0" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> Cliente </b> </label>
                            <div class="d-flex">
                                <label for="" class="pl-2 my-auto" style="font-size:11pt;"> {{ $cobroFinal['cliente'] }} </label>
                            </div>
                        </div>
                        <div class="col-md-2 p-0">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3;  border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> Fecha  </b> </label>
                            <div class="d-flex">
                                <label for="" class="pl-2 my-auto" style="font-size:11pt;">
                                    {{ \Carbon\Carbon::parse($cobroFinal['fecha'])->format('d/m/Y') }}
                                </label>
                            </div>
                        </div>    
                    </div>
                    <br>
                    <div class="row mb-2" style="border-bottom: 1px solid #ccc;">                                      
                        <div class="col-md-3 p-0" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3;  border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> TIPO EQUIPO  </b> </label>
                            <div class="d-flex">
                                <label for="" class="pl-2 my-auto" style="font-size:11pt;">
                                    {{ $cobroFinal['tipoEquipo'] }}
                                </label>
                            </div>
                        </div>                        
                        <div class="col-md-5 p-0" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> MARCA </b> </label>
                            <div class="d-flex">
                                <label for="" class="pl-2 my-auto" style="font-size:11pt;"> {{ $cobroFinal['marcaEquipo'] }} </label>
                            </div>
                        </div>
                        <div class="col-md-4 p-0">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3;  border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> MODELO  </b> </label>
                            <div class="d-flex">
                                <label for="" class="pl-2 my-auto" style="font-size:11pt;">
                                    {{ $cobroFinal['modeloEquipo'] }}
                                </label>
                            </div>
                        </div>  
                    </div>
                    <br>
                    <div class="row mb-2" style="border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                        <div class="col-md-12 p-0">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size:10pt; background-color: #e9ebf3; border-bottom: 1px solid #ccc;"> <b> Fallas </b> </label>
                            <div class="d-flex">
                                @if($cobroFinal['fallasEquipo'])
                                    @foreach ($cobroFinal['fallasEquipo'] as $fallaEquipo)
                                        <label for="" class="pl-2 my-auto" style="font-size:11pt;">{{ $fallaEquipo['descripcion'] }}        @if (!$loop->last)
                                            <span class="separator"> | </span>
                                        @endif
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <br>
                    @if ($cobroFinal['anticipo'] || $cobroFinal['montoAbonado'])
                    <div class="row mb-2" style="border-bottom: 1px solid #ccc;">
                        <div class="col-md-3 p-0 d-flex flex-column align-items-start" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> TOTAL ESTIMADO </b> </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> {{ '$ ' . number_format($cobroFinal['cobroEstimado'], 2, '.', ',') }} </label>
                            </div>
                        </div>
                        <div class="col-md-3 p-0 d-flex flex-column align-items-start" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> 
                                @if (isset($cobroFinal['anticipo']))
                                <b> ANTICIPO </b>
                                @else 
                                <b> MONTO ABONADO </b>
                                @endif
                            </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> 
                                @if (isset($cobroFinal['anticipo']))
                                    {{ '$ ' . number_format($cobroFinal['anticipo'], 2, '.', ',') }} 
                                @else
                                    {{ '$ ' . number_format($cobroFinal['montoAbonado'], 2, '.', ',') }} 
                                @endif
                                </label>
                            </div>
                        </div>                        
                        <div class="col-md-3 p-0" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                                <b> TOTAL COBRADO </b>
                            </label>
                            <div class="d-flex align-items-center">
                                <div style="padding: 2px; font-size: 11pt;">
                                    &nbsp;$
                                </div>
                                <input type="number" step="any" wire:model.live.debounce.500ms="cobroFinal.cobroRealizado" class="form-control ml-2" style="color: rgb(83, 83, 83); font-size: 11pt; border: none;">
                                @error('cobroFinal.cobroRealizado')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if ($cobroFinal['restante'] >= 0)
                        <div class="col-md-3 p-0 d-flex flex-column align-items-start" >
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> RESTANTE </b> </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> {{ '$ ' . number_format($cobroFinal['restante'], 2, '.', ',') }} </label>
                            </div>
                        </div>
                        @else
                        <div class="col-md-3 p-0 d-flex flex-column align-items-start" >
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> DEVOLVER </b> </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> {{ '$ ' . number_format(abs($cobroFinal['restante']), 2, '.', ',') }} </label>
                            </div>
                        </div>
                        @endif 
                    </div>
                    @else
                    <div class="row mb-2" style="border-bottom: 1px solid #ccc;">
                        <div class="col-md-6 p-0 d-flex flex-column align-items-start" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> TOTAL ESTIMADO </b> </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> {{ '$ ' . number_format($cobroFinal['cobroEstimado'], 2, '.', ',') }} </label>
                            </div>
                        </div>
                        <div class="col-md-6 p-0">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> TOTAL COBRADO </b> </label>
                            <div class="d-flex align-items-center">
                                <div style="padding: 2px; font-size: 11pt;">
                                    &nbsp;$
                                </div>
                                <input type="number" step="any" wire:model.live="cobroFinal.cobroRealizado" class="form-control ml-2" style="color: rgb(83, 83, 83); font-size: 11pt; border: none;">
                                @error('cobroFinal.cobroRealizado')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-md-12 p-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt;"> Estatus </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt;"> MODO DE PAGO </label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <select wire:model="cobroFinal.idEstatusEquipo" id="estatusEquiposSelect" class="selectpicker select-picker w-100">
                                        @if ($estatusEquipos)
                                            @foreach ($estatusEquipos as $estatus)
                                            <option value="{{ $estatus->id }}" data-content="{{  $this->obtenerIconoEstatus($estatus->id) }} &nbsp; {{ $estatus->descripcion }}"></option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>                                 
                                <div class="col-md-6">
                                    <select wire:model.live="cobroFinal.idModoPago" id="modoPagoSelect" class="selectpicker select-picker w-100">
                                        @foreach ($modosPagoModal as $modoPago)
                                            <option value="{{ $modoPago->id }}" data-content="<i class='{{ $modoPago->icono }}'></i> &nbsp; {{ $modoPago->nombre }}"></option>
                                        @endforeach
                                    </select>
                                </div>                              
                            </div>                            
                            <div class="d-flex justify-content-end align-items-center">
                                @if ($cobroFinal['anticipo'] || $cobroFinal['montoAbonado'])
                                    <div class="p-1">
                                        <button wire:click="cobroLiquidar({{ $cobroFinal['numOrden'] }})" class="btn btn-success text-xs leading-4 font-medium text-white uppercase tracking-wider p-2 px-4" style="letter-spacing: 1px;">
                                            {{ __('LIQUIDAR [ F2 ]') }}
                                        </button>
                                    </div>
                                @endif

                                @if (!$cobroFinal['publicoGeneral'])
                                    <div class="p-1">
                                        <button wire:click="cobroCredito({{ $cobroFinal['numOrden'] }})" class="btn btn-primary text-xs leading-4 font-medium text-white uppercase tracking-wider p-2 px-4" style="letter-spacing: 1px;">
                                            {{ __('CRÉDITO [ F3 ]') }}
                                        </button>
                                    </div>
                                @endif
                                
                                @if(!$cobroFinal['anticipo'] && !$cobroFinal['montoAbonado'])
                                    <div class="p-1">
                                        <x-button wire:click="cobrar({{ $cobroFinal['numOrden'] }}, false)" class="ml-6">
                                            {{ __('Cobrar s/recibo [ F4 ]') }}
                                        </x-button>
                                    </div>
                                    <div class="p-1">
                                        <button wire:click="cobrar({{ $cobroFinal['numOrden'] }}, true)" class="btn btn-danger text-xs leading-4 font-medium text-white uppercase tracking-wider p-2 px-4" style="letter-spacing: 1px;">
                                            {{ __('Cobrar c/recibo [ F6 ]') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('keydown', function(event) {
    if (event.key === 'F2') {
        Livewire.dispatch('f2-pressed', @this.cobroFinal);
    }
    });
    
    window.addEventListener('keydown', function(event) {
    if (event.key === 'F3') {
        Livewire.dispatch('f3-pressed', @this.cobroFinal);
    }
    });

    window.addEventListener('keydown', function(event) {
    if (event.key === 'F4') {
        Livewire.dispatch('f4-pressed', @this.cobroFinal);
    }
    });
</script>




