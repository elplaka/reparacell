<div wire:ignore.self class="modal fade" id="warningEquipoTallerModal" tabindex="-1" role="dialog" aria-labelledby="warningEquipoTallerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="uppercase tracking-widest font-semibold text-xs">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Advertencia
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                El equipo seleccionado actualmente está en el taller.
                <br>
                <b> ¿Deseas ingresarlo de todas formas? </b>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="agregaEquipoTaller">Aceptar</button>
                <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
