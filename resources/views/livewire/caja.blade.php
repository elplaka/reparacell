<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-buscar-producto')
    @include('livewire.modal-buscar-cliente')
    @include('livewire.modal-corte-caja')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-cash-register"></i> Caja</b></h4>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="col-md-12 text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;"> <strong> CÓDIGO DEL PRODUCTO </strong> </label>
            <div class="d-flex  align-items-center">
                <div class="col-md-8" style="margin-right: -12px;">
                    <input type="text" wire:model.live="codigoProductoCapturado" class="input-height form-control" style="font-size: 11pt; padding-right: 0; margin-right: 0;" wire:keydown.enter="agregaProducto">
                    <span wire:loading wire:target="agregaProducto" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span wire:loading wire:target="agregaProducto">Cargando...</span>
                </div>
                <div class="col-md-4" style="padding-left: -5px;">                              
                    <button class="btn btn-secondary" 
                            data-toggle="modal" 
                            data-target="#buscarProductoModal" 
                            style="font-size: 10pt"
                            title="Buscar producto">
                            <i class="fa-solid fa-kitchen-set"></i>&thinsp;<i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <label class="text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 11pt;"> <strong> CLIENTE </strong> </label>
            <div class="d-flex align-items-center">
                <input wire:model="cliente.nombre" type="text" class="input-height form-control mr-2" id="cliente.nombre" style="font-size: 11pt; border-top-right-radius: 0; border-bottom-right-radius: 0;" readonly>
                <button class="btn btn-secondary" data-toggle="modal" data-target="#buscarClienteModal" style="font-size: 10pt; display: flex; align-items: center;">
                    <i class="fa-solid fa-user"></i>&thinsp;<i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        <div class="col-md-5 d-flex align-items-center justify-content-end">
            <x-button wire:click="abrirCaja" class="ml-md-4 align-self-center">
                <i class="fa-solid fa-coins"></i> &nbsp; Abrir Caja [F9]
            </x-button>
            <x-button id="botonCorteCaja" data-toggle="modal" data-target="#corteCajaModal" class="ml-md-4 align-self-center">
                <i class="fa-solid fa-file-invoice-dollar"></i> &nbsp; Corte de Caja [F10]
            </x-button>
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
        <div class="row justify-content-center align-items-center mx-auto" style="background-color: #e9ebf3; width: 100%;">
            @role('admin')
                <div class="col-md-2 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                <b>  Cant. Productos: </b>
                </div>
                <div class="col-md-1 px-2 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                    {{ $cantidadProductosCarrito }}
                </div>
                <div class="col-md-2 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                    <b> Total calculado: </b>
                </div>
                <div class="col-md-2 px-3 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                    &#36; {{ number_format($totalCarrito, 2, '.', ',') }}
                </div>
                <div class="col-md-2 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                    <b> Total a cobrar: </b>
                </div>
                <div class="col-md-3 px-3 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                    {{-- $ <input type="number" step="0.5" wire:model.live="totalCarritoDescuento" style="background-color: #e9ebf3;border: none; height: 25px; width: 50%" value="{{ number_format($totalCarritoDescuento, 2, '.', ',') }}"> --}}
                    $ <input type="number" step="0.5" wire:model.live="totalCarritoDescuento" style="background-color: #e9ebf3; border: none; height: 25px; width: 50%" value="{{ number_format(floatval($totalCarritoDescuento), 2, '.', ',') }}">
                </div>
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
            <div class="col-md-10">
                @role('admin')
                <div class="row justify-content-end">
                    {{-- <div class="col-md-1 px-2 py-2 text-right text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                        <b> Total: </b>
                    </div>
                    <div class="col-md-2 px-3 py-2 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider" style="font-size: 12pt;">
                        &#36; {{ number_format($totalCarrito, 2, '.', ',') }}
                    </div> --}}
                    <button class="btn btn-primary mr-2 text-xs" style="width=5%" wire:click="hazDescuento(50)"><strong> -50% </strong></button>
                    <button class="btn btn-primary mr-2 text-xs" style="width=5%" wire:click="hazDescuento(25)"><strong> -25% </strong></button>
                    <button class="btn btn-primary mr-2 text-xs" style="width=5%" wire:click="hazDescuento(20)"><strong> -20% </strong></button>
                    <button class="btn btn-primary mr-2 text-xs" style="width=5%" wire:click="hazDescuento(15)"><strong> -15% </strong></button>
                    <button class="btn btn-primary mr-2 text-xs" style="width=5%" wire:click="hazDescuento(10)"><strong> -10% </strong></button>
                    <button class="btn btn-primary text-xs" style="width=5%" wire:click="hazDescuento(5)"><strong> -5% </strong></button>
                </div>
                @endrole
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

</script>