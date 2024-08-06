@php
    use App\Models\VentaDetalle;
    use Carbon\Carbon;
    $hayNoDisponibles = false;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    {{-- @include('livewire.productos.modal-nuevo')
    @include('livewire.productos.modal-editar') --}}
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-table"></i> Registros de Ventas</b></h4>
        <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
        {{-- <a wire:ignore.self id="botonAgregar" class="btn btn-primary" wire:click="abreAgregaProducto" title="Agregar producto" wire:loading.attr="disabled" wire:target="abreAgregaProducto" data-toggle="modal" data-target="#nuevoProductoModal">
            <i class="fas fa-plus"></i>
        </a> --}}
    </div>
    @if ($showMainErrors)
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
    <div class="row" wire:ignore>  {{-- El wire:ignore en el div exterior evita que desaparezcan los selectpickers de dentro --}}
        <div class="col-md-2 mb-3">
            <label for="filtrosVentas.fechaInicial" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Fecha Inicial </label>
            <input type="date" class="form-control input-height" wire:model.live="filtrosVentas.fechaInicial" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosVentas.fechaFinal" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Fecha Final </label>
            <input type="date" class="form-control input-height" wire:model.live="filtrosVentas.fechaFinal" style="font-size:11pt;">
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosVentas.cliente" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Cliente </label>
            <input type="text" class="form-control input-height" wire:model.live="filtrosVentas.cliente" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosVentas.idUsuario" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Vendió</label>
            <select wire:model.live="filtrosVentas.idUsuario" class="selectpicker select-picker w-100" style="font-size:11pt;">
                <option value="0"> -- TODOS -- </option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}"> {{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosVentas.cancelada" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Estatus</label>
            <select wire:model.live="filtrosVentas.cancelada" class="selectpicker select-picker w-100" style="font-size:11pt;">
                <option value="0"> -- TODOS -- </option>
                <option value="1"> ACTIVA </option>
                <option value="2"> CANCELADA </option>
            </select>
        </div>        
    </div>
    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover table-hover-custom">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ID.</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FECHA/HORA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CLIENTE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TOTAL</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">VENDIÓ</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $colors = ['#3490dc', '#10d4cd', '#9561e2', '#f66d9b', '#38c172', '#6cb2eb', '#e3342f', '#ffed4a', '#4dc0b5', '#b3342f'];

                    $i = 0;
                ?>
                @foreach ($ventas as $venta)
                @php
                    $i = $i % 10;
                    $bgColor = $colors[$i];
                    $fechaOriginal = $venta->created_at;
                    // Convertir a objeto Carbon
                    $fechaCarbon = Carbon::parse($fechaOriginal);
                    // Formatear la fecha
                    $fechaFormateada = $fechaCarbon->format('d/m/Y H:i:s');

                @endphp
                <tr style="font-size: 10pt; border-left: 5px solid {{ $bgColor }}">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $venta->id }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $fechaFormateada }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($venta->cliente->disponible)
                        {{ $venta->cliente->nombre }}
                        @else
                        {{ $venta->cliente->nombre . "*" }}
                        @php
                             $hayNoDisponibles = true;
                        @endphp
                        @endif
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                       $ {{ $venta->total }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        @if ($venta->usuario->disponible)
                        {{ $venta->usuario->name }}
                        @else
                        {{ $venta->usuario->name . "*" }}
                        @php
                            $hayNoDisponibles = true;
                        @endphp
                        @endif
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $venta->cancelada ? 'CANCELADA' : 'ACTIVA' }}
                    </td>
                    <td colspan="2" class="px-3 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        <div class="row ml-1">
                            <a wire:click="invertirEstatusVenta('{{ $venta->id }}')" wire:loading.attr="disabled" wire:target="invertirEstatusVenta" style="color: dimgrey;cursor:pointer">
                                @if ($venta->cancelada)
                                <i class='fa-solid fa-square-check' style="color: dimgrey;" onmouseover="this.style.color='green'" onmouseout="this.style.color='dimgrey'" title="Activar"></i>
                                @else
                                <i class='fa-solid fa-rectangle-xmark' style="color: dimgrey;" onmouseover="this.style.color='red'" onmouseout="this.style.color='dimgrey'" title="Cancelar"></i>
                                 @endif
                            </a>
                            &nbsp; &nbsp;
                            <button class="btn col-md-1" data-toggle="collapse" data-target="#collapseDetalle{{ $venta->id }}" aria-expanded="false" aria-controls="collapseDetalle{{ $venta->id }}" wire:click="verDetalles('{{ $venta->id }}')" style="margin: 0; padding: 0; line-height: 1; outline: none;"  onclick="this.blur();" style="color:dimgrey;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'">
                                @if (isset($collapsed[$venta->id]))
                                    <i class="fa-solid fa-angles-left" title="Ocultar detalles"></i>
                                @else
                                    <i class="fa-solid fa-angles-right" title="Mostrar detalles"></i>
                                @endif
                            </button> &nbsp; &nbsp;
                            <div wire:ignore class="collapse col-md-10" style="margin:0; padding:0" id="collapseDetalle{{ $venta->id }}">
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
                                        $ventas_detalles = VentaDetalle::where('id_venta', $venta->id)->get();
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
            </tbody>
        </table>
        @if ($hayNoDisponibles)
        <div class="col-md-5">
            <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
            @if ($hayNoDisponibles)* NO DISPONIBLE @endif
            </label>
        </div>
        @endif
    </div>

    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $ventas->links('livewire.paginame') }}
        </label>
    </div> 
</div>

{{-- <script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalNuevoProducto', () => {
        document.getElementById('btnCerrarNuevoProductoModal').click();
            })
    });

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalEditarProducto', () => {
        document.getElementById('btnCerrarEditarProductoModal').click();
            })
    });

</script> --}}