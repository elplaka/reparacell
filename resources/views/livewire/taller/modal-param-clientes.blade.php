<div wire:ignore.self class="modal fade" id="paramClientesModal" name="paramClientesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Agregar CLIENTE(S) como parámetro(s)</b></h1> &nbsp;&nbsp;
               <div wire:loading class="text-center">
                <i class="fa fa-spinner fa-spin"></i>
                </div>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraParamMarcasModal">
                   <span aria-hidden="true">&times;</span>
               </button> 
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

           <div class="modal-body">
               <div class="container mt-3 font-sans text-gray-900 antialiased">
                    <div class="row mb-3">
                        <input type="text" class="input-height col-md-12" placeholder="Nombre del cliente" wire:model.live="nombreCliente">
                    </div>
                    <div class="row mb-3">
                        @if (isset($clientes))
                        <div class="table-responsive" style="max-height: calc(5 * 40px); overflow-y: auto;">
                            <table class="w-full table table-bordered table-hover" wire:loading.remove>
                                <thead style="position: sticky; top: -1; z-index: 1;">
                                    <tr>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientes as $cliente)
                                        @php
                                            $existe = !empty($clientesDiv) && in_array($cliente->id, $clientesSeleccionados);
                                            $style = $existe ? 'cursor: default; background-color: inherit; color: #ccc;' : 'cursor: pointer;';
                                        @endphp
                                        <tr style="font-size: 9pt; {{ $style }}" @if(!$existe) wire:click="capturarFila({{ $cliente->id }})" @endif>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $cliente->nombre }} @if($existe) &nbsp; <i class="fa-solid fa-check"></i> @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    {{-- <button class="btn btn-success uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnAceptarParamModelosModal" wire:click="aceptaParamClientesModal">Aceptar</button> --}}
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarParamModelosModal" wire:click="cierraParamClientesModal">Cerrar</button>
                </div>
           </div>
       </div>
   </div>
</div>

