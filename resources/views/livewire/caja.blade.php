<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-buscar-producto')
    @include('livewire.modal-buscar-cliente')
    @include('livewire.modal-corte-caja')
    @include('livewire.ventas.modal-agregar-producto-comun')
    @include('livewire.creditos.modal-venta')

    <div class="w-100 mb-4 d-flex align-items-center">
        <h4 class="text-2xl font-bold mb-0">
            <b><i class="fa-solid fa-cash-register"></i> Caja</b>
        </h4>
        &nbsp;
        <div class="ml-2">
            <span wire:loading wire:target="agregaProducto" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span wire:loading wire:target="agregaProducto">Cargando...</span>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="row mx-0">
            <div class="col-12 col-md-3 mb-3 px-0">
                <label class="d-block font-bold text-gray-700 mb-1" style="font-size: 11pt;"> <strong> Código del Producto </strong> </label>
                <div class="d-flex align-items-center">
                    <div class="col-9 col-md-9 pr-1 pl-0">
                        <input type="text" wire:model.live="codigoProductoCapturado" class="input-height form-control" style="font-size: 11pt; border-top-right-radius: 0; border-bottom-right-radius: 0;" wire:keydown.enter="agregaProducto" autofocus>
                    </div>
                    <div class="col-3 col-md-3 pl-1">
                        <button class="btn btn-secondary"
                                data-toggle="modal" 
                                data-target="#buscarProductoModal" 
                                style="font-size: 10pt; height: 100%; white-space: nowrap; display: flex; justify-content: center; align-items: center;"
                                title="Buscar producto">
                                <i class="fa-solid fa-kitchen-set" style="margin-right: 2px;"></i><i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3 px-0">
                <label class="d-block font-bold text-gray-700 mb-1" style="font-size: 11pt;"> <strong> Cliente </strong> </label>
                <div class="d-flex align-items-center">
                    <div class="col-9 col-md-9 pr-1 pl-0">
                        <input wire:model="cliente.nombre" type="text" class="input-height form-control" id="cliente.nombre" style="font-size: 11pt; border-top-right-radius: 0; border-bottom-right-radius: 0;" readonly>
                    </div>
                    <div class="col-3 col-md-3 pl-1">
                        <button class="btn btn-secondary" data-toggle="modal" data-target="#buscarClienteModal" style="font-size: 10pt; height: 100%; white-space: nowrap; display: flex; justify-content: center; align-items: center;">
                            <i class="fa-solid fa-user"></i><i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row px-0">
                <x-button wire:click="abrirCaja" class="ml-md-4 align-self-center mb-2 mb-md-0" style="white-space: nowrap; display: flex; justify-content: center; align-items: center; width: 100%; max-width: 200px;">
                    <i class="fa-solid fa-coins" style="width: 20px;"></i> &nbsp; Abrir Caja [F9]
                </x-button>
                <x-button id="botonCorteCaja" data-toggle="modal" data-target="#corteCajaModal" class="ml-md-4 align-self-center" style="white-space: nowrap; display: flex; justify-content: center; align-items: center; width: 100%; max-width: 200px;">
                    <i class="fa-solid fa-file-invoice-dollar" style="width: 20px;"></i> &nbsp; Corte de Caja [F10]
                </x-button>
            </div>
        </div>
    </div>
    <br>
    {{-- <div class="row"> --}}
        <div class="table-responsive">
            <table class="w-95 table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider col-md-1">CANT.</th>
                        <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CÓDIGO</th>
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
                            <input wire:model.live="carrito.{{ $index }}.cantidad" 
                            @if ($carrito[$index]['esProductoComun'])
                                disabled
                                style="background-color: transparent; border: none; height: 22px; width: 100%; font-size: 10pt"
                            @else 
                                type="number"
                                style="background: #f8f8f8; border: none; height: 22px; width: 100%; font-size: 10pt" 
                            @endif
                            >
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $item['producto']->codigo }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if ($carrito[$index]['esProductoComun'])
                                {{ $carrito[$index]['descripcionProductoComun'] }}
                            @else
                                {{ $item['producto']->descripcion }}
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> &#36; 
                            @if ($carrito[$index]['esProductoComun'])
                                {{ number_format($carrito[$index]['precioProductoComun'], 2, '.', ',') }}
                            @else
                                {{ $item['producto']->precio_venta }}
                            @endif
                        </td>
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
        <div class="row justify-content-center align-items-center mx-auto" style="background-color: #e9ebf3; width: 100%;">
            @role('admin')
            <div class="col-md-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;">
                Cant. Productos:
                &nbsp; <b> {{ $cantidadProductosCarrito }} </b>
                </div>
                <div class="col-md-3 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;">
                    Total calculado:
                    &nbsp; <b> &#36; {{ number_format($totalCarrito, 2, '.', ',') }} </b>
                </div>
                @if (!$cliente['publicoGeneral'])
                <div class="col-md-3 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;">
                    Total a cobrar:
                </div>
                <div class="col-md-3 px-3 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;">
                    <b> $ <input type="number" step="0.5" wire:model.live="totalCarritoDescuento" style="background-color: #e9ebf3; border: none; height: 25px; width: 50%" value="{{ number_format(floatval($totalCarritoDescuento), 2, '.', ',') }}">
                    </b>
                </div>
                @endif
            @else
                <div class="col-md-6 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                    <b>  Cant. Productos: </b>
                </div>
                <div class="col-md-1 px-2 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                    {{ $cantidadProductosCarrito }}
                </div>            
                <div class="col-md-2 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                    <b> Total a cobrar: </b>
                </div>
                <div class="col-md-1 px-3 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                    &#36; {{ number_format($totalCarrito, 2, '.', ',') }}
                </div>
            @endrole     
        </div>
        <br>
        <div class="row d-flex justify-content-between align-items-center">
            <div class="col-md-7">
                @if (!$cliente['publicoGeneral'])
                @role('admin')
                <div class="row justify-content-end">
                    <button class="btn btn-success mr-2 text-xs" style="width=5%" wire:click="hazDescuento(50)"><strong> -50% </strong></button>
                    <button class="btn btn-success mr-2 text-xs" style="width=5%" wire:click="hazDescuento(25)"><strong> -25% </strong></button>
                    <button class="btn btn-success mr-2 text-xs" style="width=5%" wire:click="hazDescuento(20)"><strong> -20% </strong></button>
                    <button class="btn btn-success mr-2 text-xs" style="width=5%" wire:click="hazDescuento(15)"><strong> -15% </strong></button>
                    <button class="btn btn-success mr-2 text-xs" style="width=5%" wire:click="hazDescuento(10)"><strong> -10% </strong></button>
                    <button class="btn btn-success text-xs" style="width=5%" wire:click="hazDescuento(5)"><strong> -5% </strong></button>
                </div>
                @endrole
                @endif
            </div>
            <div class="col-md-3 text-right">
                @if (!$cliente['publicoGeneral'])
                <button wire:click="cobroCredito" class="btn btn-primary text-xs leading-4 font-medium text-white uppercase tracking-wider ml-8 p-2 px-4" style="letter-spacing: 1px;">
                    {{ __('CRÉDITO [ F3 ]') }}
                </button>
                @endif
            </div>
            <div class="col-md-2 text-right">
                <x-button wire:click="cobrar" class="ml-md-4">
                    {{ __('Cobrar [ F4 ]') }}
                </x-button>
            </div>
        </div>
        @endif
</div>

<script>
    window.addEventListener('keydown', function(event) {
        if (event.key === 'F4') {
            Livewire.dispatch('f4-pressed');
        }
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'F9') {
            Livewire.dispatch('f9-pressed');
        }
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'F10') {
            Livewire.dispatch('f10-pressed');
            var botonCorteCaja = document.getElementById('botonCorteCaja');
            botonCorteCaja.click();
        }
    });
</script>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalBuscarProducto', () => {
        document.getElementById('btnCerrarBuscarProductoModal').click();
            })
    });

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalCorteCaja', () => {
        document.getElementById('btnCerrarCorteCajaModal').click();
            })
    });

    document.addEventListener('livewire:initialized', function () {
    Livewire.on('abrirPestanaCorteCaja', () => {
        console.log('entra a la pestaña');
                    window.open('{{ url('/caja/corte') }}', '_blank');
                });
            });

    document.addEventListener('livewire:initialized', function () {
        @this.on('abrirModalProductoComun', () => {
            $('#productoComunModal').modal('show');
        });
    });

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalProductoComun', () => {
            $('#productoComunModal').modal('hide');
        });
    });

    document.addEventListener('livewire:initialized', function () {
        $('#productoComunModal').on('shown.bs.modal', function () {
            $('#descripcionProducto').trigger('focus');
        });
    });

    
</script>