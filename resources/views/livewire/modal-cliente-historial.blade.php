@php
    use Carbon\Carbon;
    // $i = 0;
@endphp

<div wire:ignore.self class="modal fade" id="clienteHistorialModal" name="clienteHistorialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" >
    <div class="modal-dialog modal-xl" role="dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Historial del cliente :: [{{ $cliente['nombre'] }}] </b></h1>
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
           <div class="modal-body" wire:loading.remove style="{{ $muestraHistorialClienteModal ? 'display: block' : 'display: none'}}">
               <div class="container mt-3 font-sans text-gray-900 antialiased">
                    <div class="row mb-3">
                        @if (!is_null($historialClienteTaller) && $historialClienteTaller->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered">
                                <thead>
                                    <tr>
                                    <th class="px-2 py-2 bg-gray-200 text-center text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">                                       
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   FEC. ENTRADA                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">   MARCA                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">    MODELO                                     
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider"> FEC. SALIDA                                  
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    ESTATUS
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    OBSERVACIONES
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    FALLA(S)
                                    </th>
                                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                        IMÁGENES
                                    </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!is_null($historialClienteTaller))
                                        @foreach($historialClienteTaller as $equipoCliente)
                                        @php
                                            $fecha_entrada = Carbon::parse($equipoCliente->equiposTaller[0]->fecha_entrada);

                                            $fecha_salida = is_null($equipoCliente->fecha_salida) ? "AÚN EN TALLER" : Carbon::parse($equipoCliente->fecha_salida)->format('d/m/Y');

                                            $fallas_equipo = App\Models\FallaEquipoTaller::where('num_orden', $equipoCliente->num_orden)->get();

                                            $imagenes_equipo = App\Models\ImagenEquipo::where('num_orden', $equipoCliente->num_orden)->get();

                                            $palabras_observaciones = str_word_count($equipoCliente->observaciones, 1);

                                            if (count($palabras_observaciones) > 3)
                                            {
                                                $observaciones = implode(' ', array_slice(str_word_count($equipoCliente->equiposTaller[0]->observaciones, 1), 0, 3)) . "...";
                                            }
                                            else
                                            {
                                                $observaciones = $equipoCliente->equiposTaller[0]->observaciones;
                                            }

                                            // O utilizando Storage::url()
                                            $url = asset('storage/imagenes-equipos');
                                        @endphp
                                        <tr style="font-size: 9pt;">
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {!! $equipoCliente->tipo_equipo->icono !!}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $fecha_entrada->format('d/m/Y') }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $equipoCliente->marca->nombre }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap"> 
                                                {{ $equipoCliente->modelo->nombre }}
                                            </td> 
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $fecha_salida }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $equipoCliente->equiposTaller[0]->estatus->descripcion }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                {{ $observaciones }}
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                @foreach ($fallas_equipo as $falla_equipo)
                                                    {{ $falla_equipo->falla->descripcion }}
                                                @endforeach
                                            </td>
                                            <td class="px-2 py-1 whitespace-no-wrap">
                                                @foreach ($imagenes_equipo as $key => $imagen_equipo)
                                                <a href="{{ url($url) . '/' . $imagen_equipo->nombre_archivo }}" target="_blank" title="Imagen {{ $key + 1 }}">
                                                    <i class="fa fa-image"></i>
                                                </a>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
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
