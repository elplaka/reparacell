<div wire:ignore.self class="modal fade" id="nuevoModeloModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="text-xl font-bold"><b> Agregar modelo</b></h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="cierraModeloModal">
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
             <div class="modal-body" wire:loading.remove>
                <div class="container mt-3">
                    <div class="row mb-3">
                        <label for="marcaMod.idTipoEquipo" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size: 10pt;">{{ __('Tipo Eq.') }}</label>
                        <div class="col-md-9">
                            <select wire:model.live="marcaMod.idTipoEquipo" type="text" class="select-height form-control" id="marcaMod.idTipoEquipo" style="font-size:11pt;" autofocus>
                                @foreach ($tipos_equipos as $tipo_equipo)
                                    <option value="{{ $tipo_equipo->id }}">{{ $tipo_equipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="modeloMod.idMarca" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size: 10pt;">{{ __('Marca') }}</label>
                        <div class="col-md-9">
                            <select wire:model="modeloMod.idMarca" type="text" class="select-height form-control" id="modeloMod.idMarca" style="font-size:11pt;" autofocus>
                                <option value=''>-SELECCIONA-</option>
                                @foreach ($marcas_equipos as $marca_equipo)
                                    <option value="{{ $marca_equipo->id }}">{{ $marca_equipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="modeloMod.nombre" class="col-md-3 block text-sm-right text-gray-700 pr-0" style="font-size:10pt;">{{ __('Nombre') }}</label>
                        <div class="col-md-9">
                            <input wire:model="modeloMod.nombre" type="text" class="input-height form-control" id="modeloMod.nombre" style="font-size:11pt;" autofocus>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    @if (!$guardoModeloOK) 
                    <button id="botonGuardarModelo" class="btn btn-primary uppercase tracking-widest font-semibold text-xs" wire:click="guardaModelo">Guardar</button>
                    @endif
                    <button class="btn btn-secondary uppercase tracking-widest font-semibold text-xs" data-dismiss="modal" wire:click="cierraModeloModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>