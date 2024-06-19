@php
    use Carbon\Carbon;
@endphp
<div wire:ignore.self class="modal fade" id="creditosTallerHistorialModal" name="creditosTallerHistorialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" >
    <div class="modal-dialog modal-xl" role="dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold" wire:loading.remove><b> Historial del cliente en Créditos de Taller :: [{{ $cliente['nombre'] }}] </b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraModalCreditosTallerHistorial">
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           <div wire:loading class="text-center">
               <i class="fa fa-spinner fa-spin"></i> Cargando...
               <br><br>
           </div>
           @if($showModalErrors)
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
           @endif
           <div class="modal-body" wire:loading.remove style="{{ $muestraHistorialCreditosTallerModal ? 'display: block;' : 'display: none;'}}">
               <div class="container mt-3 font-sans text-gray-900 antialiased">
                    <div class="row mb-3">
                        @if ($historialCreditosTaller  && !$historialCreditosTaller->isEmpty())
                        <div class="table-responsive"  style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover table-bordered">
                                <thead>
                                    <tr>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   FEC. SALIDA EQUIPO                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   EQUIPO                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   ESTATUS                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">    TOTAL                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 0;
                                    ?>
                                    @if (!is_null($historialCreditosTaller))
                                        @foreach($historialCreditosTaller as $creditoTaller)
                                        <tr style="font-size: 9pt;">
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $creditoTaller->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {!! $creditoTaller->cobroTaller->equipoTaller->equipo->tipo_equipo->icono !!} :: {{ $creditoTaller->cobroTaller->equipoTaller->equipo->marca->nombre . ' :: ' . $creditoTaller->cobroTaller->equipoTaller->equipo->modelo->nombre }}

                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $creditoTaller->estatus->descripcion }}
                                            </td>
                                            <td>
                                                $ {{ $creditoTaller->cobroTaller->cobro_realizado }}
                                            </td>
                                            <td>
                                                <a wire:click="abrirCreditoTaller({{ $creditoTaller->num_orden }})" title="Ir a Créditos de Taller" style="color: dimgrey; cursor:pointer;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'">
                                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif 
                                </tbody>
                            </table>
                        </div>
                        @if ($historialCreditosTaller)
                        <div class="col-mx">
                            <label class="col-form-label float-left">
                                {{ $historialCreditosTaller->links('livewire.paginame') }}
                            </label>
                        </div>
                        @endif
                        @else
                            <div class="px-2 py-2 bg-gray-200 text-center text-lg leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                EL CLIENTE NO TIENE HISTORIAL
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarCreditosTallerHistorialModal" wire:click="cierraModalCreditosTallerHistorial">Cerrar</button>
                </div>
           </div>
       </div>
   </div>
</div>
