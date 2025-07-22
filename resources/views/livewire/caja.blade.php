<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.modal-buscar-producto')
    @include('livewire.modal-buscar-cliente')
    @include('livewire.modal-corte-caja')
    @include('livewire.ventas.modal-agregar-producto-comun')
    @include('livewire.creditos.modal-venta') 
    @include('livewire.modal-cobro-cambio-caja')

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
        <div class="row mx-0">
    <div class="col-12 col-md-3 mb-3 px-0">
        <label class="d-block font-bold text-gray-700 mb-1" style="font-size: 11pt;"> <strong> Código del Producto </strong> </label>
        <div class="d-flex align-items-center">
            <div class="col-9 col-md-9 pr-1 pl-0">
                <input type="text" id="codigoProductoCapturado" wire:model.live="codigoProductoCapturado" class="input-height form-control" style="font-size: 11pt; border-top-right-radius: 0; border-bottom-right-radius: 0;" wire:keydown.enter="agregaProducto" autofocus>
            </div>
            <div class="col-3 col-md-3 pl-1">
                <button id="buscarProductoBtn" class="btn btn-secondary"
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
<br>
<div class="table-responsive">
    <table class="w-95 table table-bordered table-hover" id="carritoTable">
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
        <tbody id="carritoTableBody">
            @foreach ($carrito as $index => $item)
            <tr style="font-size: 10pt;" data-row-index="{{ $index }}" tabindex="-1"> {{-- tabindex will be managed by JS for visual purposes --}}
                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                    {{-- Usaremos wire:ignore para evitar que Livewire re-renderice este input
                         constantemente y nos permita manejar el + y - directamente aquí.
                         SIN EMBARGO, para + y - con foco en el input principal, no necesitamos wire:ignore
                         porque no vamos a manipular el valor del input directamente con JS.
                         Livewire hará el binding de vuelta. --}}
                    <input wire:ignore
                    data-row-index="{{ $index }}"
                    @if ($carrito[$index]['esProductoComun'])
                        disabled
                        style="background-color: transparent; border: none; height: 22px; width: 100%; font-size: 10pt"
                    @else
                        type="number"
                        style="background: #f8f8f8; border: none; height: 22px; width: 100%; font-size: 10pt"
                    @endif
                    value="{{ $carrito[$index]['cantidad'] ?? 1 }}">
                </td>
                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $item['producto']->codigo }}</td>
                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                    @if ($carrito[$index]['esProductoComun'])
                        {{ $carrito[$index]['descripcionProductoComun'] }}
                    @else
                        {{ $item['producto']->descripcion }}
                    @endif
                </td>
                <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                    <div class="d-flex align-items-center">
                        @if ($carrito[$index]['esProductoComun'])
                            <span class="fw-bold">$ {{ number_format($carrito[$index]['precioProductoComun'], 2, '.', ',') }}</span>
                        @else
                        <div class="d-flex align-items-center">
                            <span class="me-3 fw-bold">
                                $ {{ ($tipoPrecio[$index] ?? 1) == 1 ? $item['producto']->precio_venta : $item['producto']->precio_mayoreo }}
                            </span>
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            <div class="form-check me-2 d-flex align-items-center">
                                <input class="form-check-input me-1" type="radio" wire:model.live="tipoPrecio.{{ $index }}" id="tipoPrecio1{{ $index }}" value="1">
                                <label class="form-check-label" for="tipoPrecio1{{ $index }}">MENUDEO</label>
                            </div>
                            &nbsp;
                            &nbsp;
                            &nbsp;
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input me-1" type="radio" wire:model.live="tipoPrecio.{{ $index }}" id="tipoPrecio2{{ $index }}" value="2">
                                <label class="form-check-label" for="tipoPrecio2{{ $index }}">MAYOREO</label>
                            </div>
                        </div>
                        @endif
                    </div>
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
                    <b> Cant. Productos: </b>
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
            <div class="col-md-5">
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
            <div class="col-md-2">
                {{-- wire:click="cobrar"  --}}
                <x-button wire:click="cobrar" class="w-100 text-center" style="display: flex; justify-content: center; align-items: center;">
                    {{ __('Cobrar [ F4 ]') }}
                </x-button>
            </div>
            <div class="col-md-2 d-flex justify-content-center align-items-center">
                <div class="d-flex align-items-center w-100">
                    <select wire:model.live="idModoPagoA" id="selectModoPagoA" class="selectpicker select-picker w-100" style="margin: 0; height: auto;">
                        @foreach ($modosPagoModal as $modoPago)
                            <option value="{{ $modoPago->id }}" data-content="<i class='{{ $modoPago->icono }}'></i> &nbsp; {{ $modoPago->nombre }}"></option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('keydown', function (event) {
        // Código 115 = F4
        if (event.keyCode === 115 || event.code === 'F4') {
            event.preventDefault(); // Opcional: evita que el navegador lo intercepte

            // Abrir el modal
            $('#cobroCambioCajaModal').modal('show');
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
     
   document.addEventListener('DOMContentLoaded', function() {
   // Inicializar selectpicker

         $('#selectModoPagoA').selectpicker();

        // Añadir evento keydown al documento solo cuando la modal esté visible
        // $(document).on('keydown.modalEvent', function (e) {
        //     console.log('keydown event detected'); // Mensaje para verificar que el evento se detecta
        //     if (e.key === 'Enter') {
        //         e.preventDefault(); // Evitar el comportamiento predeterminado del formulario
        //         $('#btnAceptar').trigger('click'); // Simular un clic en el botón "Aceptar"
        //     }
        // });

        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            $('.selectpicker').selectpicker();
            succeed(({ snapshot, effect }) => {
                $('select').selectpicker('destroy');
                queueMicrotask(() => {
                    setTimeout(() => {
                        $('.selectpicker').selectpicker('refresh');
                    }, 10); //
                });
            });

            fail(() => {
                console.error('Livewire commit failed');
            });
        });

    $('#corteCajaModal').on('shown.bs.modal', function () {
        $('#selectModoPagoCorte').selectpicker();

        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            $('.selectpicker').selectpicker();
            succeed(({ snapshot, effect }) => {
                $('select').selectpicker('destroy');
                queueMicrotask(() => {
                    // Refrescar los selectpickers
                    $('.selectpicker').selectpicker('refresh');
                });
            });

            fail(() => {
                console.error('Livewire commit failed');
            });
        });
    });

    // Escuchar el evento de cierre de la ventana modal para remover el evento keydown
    $('#cobrarModal').on('hidden.bs.modal', function () {
        $(document).off('keydown.modalEvent');
    });

});

    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('abrirModalBuscarProducto', () => {
            let button = document.getElementById('buscarProductoBtn');
            if (button) {
                button.click();
            } 
        });  
    });

    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalBuscarProducto', () => {
        document.getElementById('btnCerrarBuscarProductoModal').click();
            })    
    });

    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('abreInicializarCajaModal', () => {
            $('#inicializarCajaModal').modal('show');
            console.log('gusta');
            })    
    });


    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalCorteCaja', () => {
        document.getElementById('btnCerrarCorteCajaModal').click();
            })
    });

    document.addEventListener('livewire:initialized', function () {
    Livewire.on('abrirPestanaCorteCaja', () => {
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
        Livewire.on('cerrarModalCobroCambioCaja', () => {
            console.log('molina');
            $('#cobroCambioCajaModal').modal('hide');
        });
    });

    document.addEventListener('livewire:initialized', function () {
        $('#productoComunModal').on('shown.bs.modal', function () {
            $('#descripcionProducto').trigger('focus');
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    $('#buscarProductoModal').on('shown.bs.modal', function () {
        let descripcionProductoInput = document.getElementById('descripcionProductoModal');
        if (descripcionProductoInput) {
            descripcionProductoInput.focus();
        } 
    });
});

document.addEventListener('livewire:initialized', function () {
    Livewire.on('cierraModalCobrar', (attr) => {
        $('#cobrarModal').modal('hide');
        setTimeout(() => {
            let codigoProducto = document.getElementById('codigoProductoCapturado');
            if (codigoProducto) {
                codigoProducto.focus();
            } 
        }, 900); // Ajusta el tiempo (300ms) según sea necesario
    });
});

</script>

<script>
    document.addEventListener('livewire:initialized', () => {
    let selectedRowIndex = -1;
    const codigoInput = document.getElementById('codigoProductoCapturado');
    
    // Función highlightRow (sin cambios aquí)
    function highlightRow(index) {
        const tableBody = document.getElementById('carritoTableBody');
        if (!tableBody) return;

        const rows = tableBody.children;
        Array.from(rows).forEach((row) => {
            row.classList.remove('text-primary', 'fw-bold', 'fila-resaltada');
        });

        if (index >= 0 && index < rows.length) {
            rows[index].classList.add('text-primary', 'fw-bold', 'fila-resaltada');
            selectedRowIndex = index;
            rows[index].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            selectedRowIndex = -1;
        }
    }

    // Listener para las teclas de navegación y cantidad cuando el input de código está enfocado
    codigoInput.addEventListener('keydown', (event) => {
        let ultimaActualizacion = 0;

        function intentarActualizarCantidad(cambio) {
            const tableBody = document.getElementById('carritoTableBody');
            if (!tableBody || selectedRowIndex === -1) return;

            const row = tableBody.children[selectedRowIndex];
            const quantityInput = row.querySelector('input[type="number"]');
            if (!quantityInput || quantityInput.disabled) return;

            let cantidadActual = parseInt(quantityInput.value) || 0;
            let nuevaCantidad = Math.max(0, cantidadActual + cambio);

            // 🖊️ Actualización visual
            quantityInput.value = nuevaCantidad;
            quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
            quantityInput.dispatchEvent(new Event('change', { bubbles: true }));

            // 🌩️ Dispatch manual a Livewire con index + cantidad
            const index = row.getAttribute('data-row-index');
            Livewire.dispatch('actualizaCantidadManual', { index: parseInt(index), cantidad: nuevaCantidad });

            // ✨ Efecto visual
            quantityInput.classList.add('quantity-flash');
            setTimeout(() => quantityInput.classList.remove('quantity-flash'), 10);

            if (nuevaCantidad === 0) highlightRow(-1);
            codigoInput.focus();
        }

        const tableBody = document.getElementById('carritoTableBody');
        if (!tableBody) return;
        const rows = tableBody.children;

        // Solo actuamos si el foco está realmente en el codigoInput
        if (document.activeElement === codigoInput) {
            // **PUNTO CLAVE DE MODIFICACIÓN:** Maneja '+' y '-' al inicio
            if (event.key === '+') {
                event.preventDefault();
                intentarActualizarCantidad(+1);
            }
            if (event.key === '-') {
                event.preventDefault();
                intentarActualizarCantidad(-1);
            }

            // El resto de tu lógica para otras teclas (ArrowDown, ArrowUp, Escape)
            // se mantiene como estaba, ya que su preventDefault() ya es lo primero en su bloque.
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                highlightRow(selectedRowIndex === -1 ? 0 : Math.min(selectedRowIndex + 1, rows.length - 1));
                codigoInput.focus();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                highlightRow(Math.max(selectedRowIndex - 1, 0));
                codigoInput.focus();
            } else if (event.key === 'Escape') {
                event.preventDefault();
                highlightRow(-1);
                codigoInput.focus();
            }
            // wire:keydown.enter="agregaProducto" ya maneja el Enter
        }
    });

    Livewire.hook('commit', ({ component, succeed }) => {
        succeed(() => {
            if (component.name === @json($this->getName())) {
                codigoInput.focus();

                setTimeout(() => {
                    const tableBody = document.getElementById('carritoTableBody');
                    if (!tableBody) return;

                    // 🔁 Highlight defensivo
                    if (tableBody.children.length === 0) {
                        highlightRow(-1);
                    } else if (selectedRowIndex >= 0 && selectedRowIndex < tableBody.children.length) {
                        highlightRow(selectedRowIndex);
                    } else {
                        selectedRowIndex = -1;
                    }

                    // 🔗 Re-bind con debounce y validación robusta
                    const quantityInputs = tableBody.querySelectorAll('input[type="number"]');
                    quantityInputs.forEach((input) => {
                        if (!input.dataset.listenerBound) {
                            let debounceTimer = null;

                            input.addEventListener('input', () => {
                                clearTimeout(debounceTimer);

                                debounceTimer = setTimeout(() => {
                                    const index = input.getAttribute('data-row-index');
                                    const rawValue = input.value.trim();

                                    // ⚠️ Validar que sea número válido > 0
                                    if (/^\d+$/.test(rawValue)) {
                                        const cantidad = parseInt(rawValue);
                                        Livewire.dispatch('actualizaCantidadManual', {
                                            index: parseInt(index),
                                            cantidad
                                        });

                                        input.classList.add('quantity-flash');
                                        setTimeout(() => input.classList.remove('quantity-flash'), 100);
                                    }
                                }, 120); // ⏱ tiempo entre pulsaciones
                            });

                            input.dataset.listenerBound = true;
                        }
                    });

                }, 0);
            }
        });
    });

    Livewire.on('carrito-actualizado', () => {
        selectedRowIndex = -1;
        // highlightRow(-1);
        codigoInput.focus();
    });

    const tableBody = document.getElementById('carritoTableBody');
        if (tableBody) {
            const quantityInputs = tableBody.querySelectorAll('input[type="number"]');
            quantityInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    const index = input.getAttribute('data-row-index');
                    const cantidad = parseInt(input.value) || 0;

                    Livewire.dispatch('actualizaCantidadManual', {
                        index: parseInt(index),
                        cantidad: cantidad
                    });

                    // ✨ flash visual opcional
                    input.classList.add('quantity-flash');
                    setTimeout(() => input.classList.remove('quantity-flash'), 100);
                });
            });
        }
});
</script>

<style>
    .fila-resaltada {
    background-color: rgb(194, 224, 253); 
    font-weight: 600
}
</style>



