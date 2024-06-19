@php
    use Carbon\Carbon;
    use App\Models\VentaDetalle;
@endphp

<div wire:ignore.self class="modal fade" id="ventasHistorialModal" name="ventasHistorialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" >
    <div class="modal-dialog modal-xl" role="dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold" wire:loading.remove><b> Historial del cliente en Ventas :: [{{ $cliente['nombre'] }}] </b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraModalClienteHistorial">
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
           <div class="modal-body" wire:loading.remove style="{{ $muestraHistorialVentasModal ? 'display: block;' : 'display: none;'}}">
               <div class="container mt-3 font-sans text-gray-900 antialiased">
                    <div class="row mb-3">
                        @if ($historialClienteVentas  && !$historialClienteVentas->isEmpty())
                        <div class="table-responsive"  style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover table-bordered">
                                <thead>
                                    <tr>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   FEC. VENTA                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   TOTAL                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">    VENDIÓ                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $colors = ['#3490dc', '#10d4cd', '#9561e2', '#f66d9b', '#38c172', '#6cb2eb', '#e3342f', '#ffed4a', '#4dc0b5', '#b3342f'];
                    
                                        $i = 0;
                                    ?>
                                    @if (!is_null($historialClienteVentas))
                                        @foreach($historialClienteVentas as $ventaCliente)
                                        @php
                                            $fecha_venta = Carbon::parse($ventaCliente->created_at);
                                            $i = $i % 10;
                                            $bgColor = $colors[$i];
                                        @endphp
                                        <tr style="font-size: 9pt; border-left: 5px solid {{ $bgColor }}">
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $fecha_venta->format('d/m/Y') }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                $ {{ $ventaCliente->total }}
                                            </td>
                                             <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $ventaCliente->usuario->name }}
                                            </td> 
                                            <td colspan="2" class="px-3 py-1 whitespace-no-wrap" style="vertical-align: middle">
                                                <div class="row ml-1">
                                                    <button class="btn col-md-1" data-toggle="collapse" data-target="#collapseDetalle{{ $ventaCliente->id }}" wire:click="verDetalles('{{ $i }}')" style="margin: 0; padding: 0; line-height: 1; outline: none;"  onclick="this.blur();" style="color:dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'" wire:refresh>
                                                        @if (isset($collapsed[$i]))
                                                            <i class="fa-solid fa-angles-left" title="Ocultar detalles"></i>
                                                        @else
                                                            <i class="fa-solid fa-angles-right" title="Mostrar detalles"></i>
                                                        @endif
                                                    </button> &nbsp; &nbsp;
                                                    <div class="collapse col-md-10" style="margin:0; padding:0;display:{{ isset($collapsed[$i]) ? 'block' : 'none' }}" id="collapseDetalle{{ $ventaCliente->id }}">
                                                        <table class="w-full mb-0">
                                                            <thead>
                                                                <tr class="no-hover">
                                                                    <th class="col-md-1 px-2 py-0 text-left text-xs leading-4 font-bold text-white uppercase tracking-wider" style="font-size:8pt; background-color: {{ $bgColor }}">
                                                                        CANT.
                                                                    </th>
                                                                    <th class="col-md-8 px-2 py-0 text-left text-xs leading-4 font-bold text-white uppercase tracking-wider" style="font-size:8pt; background-color: {{ $bgColor }}">
                                                                        PRODUCTO
                                                                    </th>
                                                                    <th class=" col-md-3 px-2 py-0 text-left text-xs leading-4 font-bold text-white uppercase tracking-wider" style="font-size:8pt; background-color: {{ $bgColor }}">
                                                                        IMPORTE
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            @php
                                                                $ventas_detalles = VentaDetalle::where('id_venta', $ventaCliente->id)->get();
                                                            @endphp
                                                            <tbody>
                                                                @foreach ($ventas_detalles as $detalle)
                                                                    <tr class="no-hover">
                                                                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; font-size: 8pt" >
                                                                            {{ $detalle->cantidad }}
                                                                        </td>
                                                                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; font-size: 8pt">
                                                                            {{ $detalle->producto->descripcion }}
                                                                        </td>
                                                                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; font-size: 8pt">
                                                                        $ {{ $detalle->importe }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>          
                                        </tr>
                                        @php
                                        $i++;
                                    @endphp
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if ($historialClienteVentas)
                        <div class="col-mx">
                            <label class="col-form-label float-left">
                                {{ $historialClienteVentas->links('livewire.paginame') }}
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
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" id="btnCerrarEquiposClienteModal" wire:click="cierraModalClienteHistorial">Cerrar</button>
                </div>
           </div>
       </div>
   </div>
</div>
