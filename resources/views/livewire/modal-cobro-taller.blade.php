{{-- <div wire:ignore.self class="modal fade" id="cobroTallerModal" name="cobroTallerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Cobro</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           <div wire:loading class="text-center">
               <i class="fa fa-spinner fa-spin"></i> Cargando...
               <br><br>
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
           <div class="modal-body">
               <div class="container mt-3 font-sans text-gray-900 antialiased">
                    <div class="row mb-2">
                        <div class="col md-2">
                            <label class="col-form-label text-md-left" style="font-size:11pt"> <b> Fecha Entrada </b> </label>
                            <div>
                                <label for="">
                                    {{ \Carbon\Carbon::parse($cobroFinal['fecha'])->format('d/m/Y') }}
                                </label>
                            </div>
                        </div>
                        <div class="col md-2">
                            <label class="col-form-label text-md-left" style="font-size:11pt"> <b> Tipo Equipo </b> </label>
                            <div>
                                <label for=""> {{ $cobroFinal['tipoEquipo'] }} </label>
                            </div>
                        </div>
                        <div class="col md-2">
                            <label class="col-form-label text-md-left" style="font-size:11pt"> <b> Marca </b> </label>
                            <div>
                                <label for=""> {{ $cobroFinal['marcaEquipo'] }} </label>
                            </div>
                        </div>
                        <div class="col md-2">
                            <label class="col-form-label text-md-left" style="font-size:11pt"> <b> Modelo </b> </label>
                            <div>
                                <label for=""> {{ $cobroFinal['modeloEquipo'] }} </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col md-2">
                            <label class="col-form-label text-md-left" style="font-size:11pt"> <b> Fallas </b> </label>
                            <div>
                                @if($cobroFinal['fallasEquipo'])
                                @foreach ($cobroFinal['fallasEquipo'] as $fallaEquipo)
                                    <label for=""> {{ $fallaEquipo['descripcion'] }} </label>  <br>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col md-2">
                            <label class="col-form-label text-md-left" style="font-size:11pt"> <b> Total Estimado </b> </label>
                            <div>
                                <label for=""> {{ '$ ' . $cobroFinal['cobroEstimado'] }} </label>  <br>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
       </div>
   </div>
</div>
 --}}

 <div wire:ignore.self class="modal fade" id="cobroTallerModal" name="cobroTallerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="dialog">
        <div class="modal-content" style="border: 1px solid #ccc;">
            <div class="modal-header bg-light border-bottom border-gray-300">
                <h1 class="text-xl font-bold text-uppercase"><b> COBRO DE SALIDA DE EQUIPO </b></h1>       
                
                {{-- <div wire:loading class="text-center">
                    &nbsp;&nbsp; <i class="fa fa-spinner fa-spin"></i> Cargando...
                </div> --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" wire:click="cierraCobroFinalModal">
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
                    @if ($cobroFinal['anticipo'])
                    <div class="row mb-2" style="border-bottom: 1px solid #ccc;">
                        <div class="col-md-3 p-0 d-flex flex-column align-items-start" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> TOTAL ESTIMADO </b> </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> {{ '$ ' . number_format($cobroFinal['cobroEstimado'], 2, '.', ',') }} </label>
                            </div>
                        </div>
                        <div class="col-md-3 p-0 d-flex flex-column align-items-start" style="border-right: 1px solid #ccc;">
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt; background-color: #e9ebf3; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;"> <b> ANTICIPO </b> </label>
                            <div class="d-flex flex-grow-1 align-items-center my-auto">
                                <label for="" class="pl-2 my-auto w-100" style="font-size: 11pt;"> {{ '$ ' . number_format($cobroFinal['anticipo'], 2, '.', ',') }} </label>
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
                            <label class="col-form-label text-md-left w-100 d-block pl-2 text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider" style="font-size: 10pt;"> Estatus </label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select wire:model="cobroFinal.idEstatusEquipo" id="estatusEquiposSelect" type="text" class="select-height form-control" style="font-size:11pt">
                                        @if ($estatusEquipos)
                                            @foreach ($estatusEquipos as $estatus)
                                                <option value="{{ $estatus->id }}">{{ $estatus->descripcion }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                @if ($cobroFinal['anticipo'])
                                <div class="col-md-3 text-right">
                                    <button wire:click="cobroLiquidar({{ $cobroFinal['numOrden'] }})" class="btn btn-success text-xs leading-4 font-medium text-white uppercase tracking-wider ml-8 p-2 px-4" style="letter-spacing: 1px;">
                                        {{ __('LIQUIDAR [ F2 ]') }}
                                    </button>
                                </div>
                                @endif
                                <div class="col-md-3 text-right">
                                    @if (!$cobroFinal['publicoGeneral'])
                                    <button wire:click="cobroCredito({{ $cobroFinal['numOrden'] }})" class="btn btn-primary text-xs leading-4 font-medium text-white uppercase tracking-wider ml-8 p-2 px-4" style="letter-spacing: 1px;">
                                        {{ __('CRÉDITO [ F3 ]') }}
                                    </button>
                                    @endif
                                </div>
                                @if(!$cobroFinal['anticipo'])
                                <div class="col-md-3 text-right">
                                    <x-button wire:click="cobrar({{ $cobroFinal['numOrden'] }})" class="ml-6">
                                        {{ __('Cobrar [ F4 ]') }}
                                    </x-button>
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




