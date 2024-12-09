<div wire:ignore.self class="modal fade" id="anotacionesModal" name="anotacionesModal" tabindex="-1" role="dialog" aria-labelledby="anotacionesModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="uppercase tracking-widest font-semibold text-s">
                    <i class="fa-solid fa-comment"></i> ANOTACIONES
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click='cierraModalAnotaciones'>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-begin" wire:loading.remove>
                <span class="badge badge custom-badge-color-{{ $anotacionesMod['estatusEquipo']}}" style="color:white">{{ $anotacionesMod['marcaEquipo'] }}</span>
                <span class="badge badge custom-badge-color-{{ $anotacionesMod['estatusEquipo']}}" style="color:white">{{ $anotacionesMod['modeloEquipo'] }}</span>
                <span class="badge custom-badge-color-{{ $anotacionesMod['estatusEquipo']}}" style="color:white">{{ $anotacionesMod['clienteEquipo'] }}</span>
               <br><br>
               <textarea wire:ignore class="w-100" id="textAnotaciones" wire:model="anotacionesMod.contenido" placeholder="Escribe las anotaciones" rows="2"></textarea>
                <div class="modal-footer">
                    <button class="btn btn-success uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="guardaAnotaciones">Aceptar</button>
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click='cierraModalAnotaciones'>Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
