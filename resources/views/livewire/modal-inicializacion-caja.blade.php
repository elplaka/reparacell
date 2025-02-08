<div>
    @if ($showModal)
        <div class="modal fade show d-block" id="inicializarCajaModal" name="inicializarCajaModal" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-md"> <!-- Agregar modal-lg para mayor tamaño -->
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h1 style="font-size:16pt;"><i class="fa-solid fa-comments-dollar"></i> Inicialización de Caja</h1>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="inicializaCaja">
                            <div class="text-center mb-3">
                                <p class="font-weight-bold" style="font-size:16pt;">
                                    SALDO de la CAJA al CIERRE del día
                                </p>                   
                            </div>
                            <div class="text-center mb-3">
                                <span class="badge badge-primary" style="font-size:16pt;">
                                    {{ $formatoFechaEsp }}
                                </span>
                            </div>
                            <div class="text-center mb-3">
                                <span class="badge badge-success" style="font-size:16pt;">
                                    <strong> $ {{ number_format($saldoCajaActual, 2, '.', ',') }} </strong>
                                </span>
                            </div>
                            <hr>
                            <div class="my-4 text-center">
                                <p class="font-weight-normal" style="font-size:13pt;">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true" style="color: #ffcc00; margin-right: 5px;"></i> <!-- Icono de advertencia -->
                                    Debes capturar el MONTO con el que la CAJA va a INICIAR el día de hoy.
                                </p>
                            </div>
                            <div class="col-md-4 mx-auto text-center">
                                <div class="form-group text-center">
                                    <label for="monto" class="text-center d-block font-weight-bold"><strong>Monto</strong></label>
                                    <input type="number" id="montoInicializacion" class="form-control text-right" 
                                           wire:model="montoInicializacion" min="1" step="1" 
                                           style="max-width: 200px; font-size:13pt;" required>
                                    @error('montoInicializacion') 
                                        <span class="text-danger d-block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center border-top-0"> <!-- Centrar el botón sin borde superior -->
                                <button type="submit" class="btn btn-primary font-weight-bold text-uppercase" 
                                        style="background-color: #007bff; color: white; border: none; padding: 10px 16px; font-size: 14pt;">
                                    Aceptar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
