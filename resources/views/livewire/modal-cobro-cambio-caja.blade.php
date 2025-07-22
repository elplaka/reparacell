<div wire:ignore.self class="modal fade" id="cobroCambioCajaModal" name="cobroCambioCajaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="dialog" >
       <div class="modal-content">
           <div class="modal-header">
               <h1 class="text-xl font-bold"><b> Cobro de Ventas</b></h1>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click='cierraCobroCambioModal'>
                   <span aria-hidden="true">&times;</span>
               </button> 
           </div>
           
            
           <div class="modal-body">
               <div class="container" style="max-width: 450px; margin: 0 auto; border: 2px solid #007bff; padding: 15px; border-radius: 8px;">
                    <div class="row mb-3">
                        <div class="col-12 text-right">
                            <span class="text-uppercase font-weight-bold" style="font-size: 16pt;">
                                Total a Cobrar: &nbsp;
                            </span>
                            <span id="totalCarrito" class="badge badge-primary d-inline-block text-right"
                                style="font-size: 18pt; width: 180px;">
                                &#36; {{ number_format($totalCarrito, 2, '.', ',') }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 text-right">
                            <span class="text-uppercase font-weight-bold" style="font-size: 16pt;">
                                Pagó con: &nbsp;
                            </span>
                            <input type="number" wire:model="pagoCon" id="pagoConInput"
                                class="form-control d-inline-block text-right"
                                style="width: 180px; height: 35px; font-size: 18pt; font-weight: bold;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-right">
                            <span class="text-uppercase font-weight-bold" style="font-size: 16pt;">
                                Cambio: &nbsp;
                            </span>
                            <span id="cambioCalculado" class="badge badge-danger d-inline-block text-right"
                                style="font-size: 18pt; width: 180px;">
                                &#36; 0.00
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer con Botón de Cierre -->
                <div class="modal-footer d-flex justify-content-center">
                    <button id="btnCobrarConTicket" class="btn btn-success uppercase tracking-widest font-semibold text-md" wire:click="cobrar">C/TICKET [F2] <i class="fa-solid fa-ticket"></i></button>
                    <button id="btnCobrarSinTicket" class="btn btn-primary uppercase tracking-widest font-semibold text-md" wire:click="cobrar(2)">S/TICKET [F3] <i class="fa-solid fa-cash-register"></i></button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-md" data-dismiss="modal" wire:click='cierraCobroCambioModal' id="btnCerrarCorteCajaModal">Cerrar</button>
                </div>
            </div>
       </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
        document.addEventListener('keydown', (event) => {
        if (event.key === 'F2' || event.key === 'F3') {
            event.preventDefault(); // 🛡️ bloquea comportamiento nativo

            if (event.key === 'F2') {
                document.getElementById('btnCobrarConTicket')?.click();
            }

            if (event.key === 'F3') {
                document.getElementById('btnCobrarSinTicket')?.click();
            }
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const pagoConInput = document.getElementById('pagoConInput');
    const cambioDisplay = document.getElementById('cambioCalculado');

         $('#cobroCambioCajaModal').on('shown.bs.modal', function () {
            document.getElementById('pagoConInput').focus();

            const input = document.getElementById('pagoConInput');
            input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Evita que se recargue el formulario si aplica
                document.querySelector('[wire\\:click="cobrar"]').click();
            }
        });
        });

    pagoConInput.addEventListener('input', function () {
    
        const totalCarrito = document.getElementById('totalCarrito').textContent;

        const totalLimpio = totalCarrito.replace(/[^\d.]/g, ''); // "100.00"
        const total = parseFloat(totalLimpio); // 100
        console.log('Total como número:', total); // 100

        const pagoCon = parseFloat(pagoConInput.value);
        const cambio = pagoCon - total;

        if (!isNaN(cambio)) {
            cambioDisplay.innerHTML = '&#36; ' + cambio.toFixed(2);
        } else {
            cambioDisplay.innerHTML = '&#36; 0.00';
        }
    });
});
</script>


