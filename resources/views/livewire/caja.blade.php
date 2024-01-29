<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-buscar-cliente')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-dollar-sign"></i> Caja</b></h4>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="col-md-12 text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;"> <strong> CÓDIGO DEL PRODUCTO </strong> </label>
            <div class="col-md-12 d-flex align-items-center">
                <input type="text" wire:model.live="codigoProductoCapturado" class="col-md-4 input-height form-control" style="font-size:11pt" wire:keydown.enter="agregaProducto()"> &nbsp;
                <span wire:loading wire:target="agregaProducto" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> &nbsp;
                <span wire:loading wire:target="agregaProducto"> Cargando... </span>
            </div>
        </div>
        <div class="col-md-5 mb-3">
            <label class="text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;"> <strong> CLIENTE </strong> </label>
            <div class="d-flex align-items-center">
                <input wire:model="cliente.nombre" type="text" class="input-height form-control mr-2" id="cliente.nombre" style="font-size: 11pt; border-top-right-radius: 0; border-bottom-right-radius: 0;" readonly>
                <button class="btn btn-secondary" data-toggle="modal" data-target="#buscarClienteModal" style="font-size: 10pt; display: flex; align-items: center;">
                    <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            
            
            
            
            {{-- <div class="col col-md-11">
                <label for="cliente.nombre" class="block font-medium text-sm-left text-gray-700" style="font-size: 11pt;"> <strong>{{ __('CLIENTE') }} </strong></label>
                <div class="input-group">
                    <input wire:model="cliente.nombre" type="text" class="input-height form-control mr-2" id="cliente.nombre" style="font-size:11pt;" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-secondary ml-2" data-toggle="modal" data-target="#buscarClienteModal" style="font-size: 10pt" wire:click="abreModalBuscarCliente">
                            <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div> --}}
                    {{-- 
                        <label for="cliente.telefono" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size:11pt;">{{ __('Teléfono') }}</label>
                        <input wire:model.live="cliente.telefono" type="text" class="input-height form-control" id="cliente.telefono" wire:keydown="validarNumeros" style="font-size: 11pt;" 
                        @if ($equipoTaller['estatus'] == 3) readonly @endif 
                        autofocus> --}}
                    {{-- @if (!$cliente['publicoGeneral'])
                        @if (strlen($cliente['telefono']) == 10 || $cliente['estatus'] == 3)
                            <div class="col col-md-8 d-flex justify-content-end">
                                @if ($cliente['estatus'] == 2)   
                                <button class="btn btn-secondary" style="font-size: 10pt" wire:click="editarCliente" title="Editar cliente">
                                    <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-edit"></i>
                                </button>
                                @elseif ($cliente['estatus'] == 3 && $equipo['estatus'] != 3)   
                                <button class="btn btn-secondary" style="font-size: 10pt" wire:click="guardarCliente" title="Guardar cliente">
                                    <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-save"></i>
                                </button>
                                @endif
                                &nbsp;
                                <button class="btn btn-secondary ml-2" style="font-size: 10pt" data-toggle="modal" data-target="#equiposClienteModal" title="Ver equipos del cliente" wire:click="abrirEquiposClienteModal">
                                    <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-mobile-screen"></i>
                                </button>  &nbsp;
                                <button class="btn btn-secondary ml-2" style="font-size: 10pt" data-toggle="modal" data-target="#clienteHistorialModal" wire:click="abreClienteHistorial" title="Ver historial del cliente">
                                    <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                            </div>
                        @endif
                    @endif --}}
            {{-- <div class="container mt-3">
                <div class="row">
                    @if (strlen($cliente['telefono']) == 10  ||  $cliente['estatus'] == 3)
                    <label for="cliente.nombre" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Nombre') }}</label>
                    <div class="col-md-3">
                        <input wire:model="cliente.nombre" type="text" class="input-height form-control" id="cliente.nombre" style="font-size:11pt;" 
                        @if($cliente['estatus'] == 2 || $equipoTaller['estatus'] == 3) readonly @endif 
                        autofocus>
                    </div>
                    <label for="cliente.direccion" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Dirección') }}</label>
                    <div class="col-md-3">
                        <input wire:model="cliente.direccion" type="text" class="input-height form-control" id="cliente.direccion" style="font-size:11pt;" @if($cliente['estatus'] == 2 || $equipoTaller['estatus'] == 3) readonly @endif autofocus>
                    </div>
                    <label for="cliente.telefonoContacto" class="col-md-1 block font-medium text-sm-right text-gray-700 pr-0" style="font-size: 11pt;">{{ __('Contacto') }}</label>
                    <div class="col-md-2 mb-2">
                        <input wire:model="cliente.telefonoContacto" type="text" class="input-height form-control" id="cliente.telefonoContacto" style="font-size:11pt;" wire:keydown="validarNumeros" 
                        @if($cliente['estatus'] == 2 || $equipoTaller['estatus'] == 3) readonly @endif 
                        autofocus>
                    </div>
                    @endif
                </div>
            </div> --}}
        </div>
    </div>
    <br>
    {{-- <div class="row"> --}}
        <div class="table-responsive">
            <table class="w-95 table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider col-md-1">CANT.</th>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DESCRIPCIÓN</th>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">PRECIO</th>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">IMPORTE</th>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($carrito as $index => $item)
                    <tr style="font-size: 10pt;">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            <input wire:model.live="carrito.{{ $index }}.cantidad" type="number" style="background: #f8f8f8; border: none; height: 22px; width: 100%; font-size: 10pt">
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $item['producto']->descripcion }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> &#36; {{ $item['producto']->precio_venta }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> &#36; {{ $carrito[$index]['subTotal'] }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            <button wire:click="eliminaDelCarrito({{ $index }})" wire:loading.remove wire:target="eliminaDelCarrito" class="label-button">
                                <i class="fa-solid fa-trash-can" style="color:black" onmouseover="this.style.color='blue'" onmouseout="this.style.color='red'"></i>
                            </button>
                            <span wire:loading wire:target="eliminaDelCarrito" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($cantidadProductosCarrito)
        <div class="row justify-content-center mx-auto" style="background-color: #e9ebf3; width: 100%;">
            <div class="col-md-8 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
              <b>  Cant. Productos: </b>
            </div>
            <div class="col-md-1 px-2 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                {{ $cantidadProductosCarrito }}
            </div>
            <div class="col-md-1 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                <b> Total: </b>
            </div>
            <div class="col-md-2 px-3 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                &#36; {{ number_format($totalCarrito, 2, '.', ',') }}
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col text-right">
                <x-button wire:click="cobrar" class="ml-4">
                    {{ __('Cobrar') }}
                </x-button>
            </div>
        </div>
        @endif
</div>

<script>
    window.addEventListener('keydown', function(event) {
        if (event.key === 'F2') {
            Livewire.dispatch('f2-pressed');
        }
    });
</script>