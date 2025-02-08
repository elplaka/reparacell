@php
    use Carbon\Carbon;
@endphp
<div class="w-full font-sans text-gray-900 antialiased">
    @include('livewire.modal-agrega-movimiento-caja')
    <div class="w-100 mb-4 mt-6 d-flex justify-content-between align-items-center">
        <!-- Título y ícono -->
        <div class="d-flex align-items-center">
            <h4 class="text-2xl font-bold mb-0" wire:poll>
                <b><i class="fa-solid fa-sack-dollar"></i> Movimientos de Caja</b>  &nbsp; 
                <span class="badge badge-success" style="font-size:14pt">
                    $ {{ number_format($saldoCajaActual, 2, '.', ',') }}
                </span>
            </h4>
            &nbsp;
            <div class="ml-2">
                <span wire:loading wire:target="agregaMovimiento" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span wire:loading wire:target="agregaMovimiento">Cargando...</span>
            </div>
        </div>
        <div>
            <a wire:ignore.self id="botonAgregarMov" class="btn btn-primary" aria-controls="agregaMovimientoModal" wire:click="abreAgregaMovimiento" title="Agregar movimiento" wire:loading.attr="disabled" wire:target="abreAgregaMovimiento">
                <i class="fas fa-plus"></i>
            </a>
        </div>
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
            <label for="filtrosMovimientos.fechaInicial" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Fecha Inicial </label>
            <input type="date" class="form-control input-height" wire:model.live="filtrosMovimientos.fechaInicial" style="font-size:11pt;">
        </div>
        <div class="col-md-2 mb-3">
            <label for="filtrosMovimientos.fechaFinal" class="form-label text-gray-700" style="font-weight:500;font-size:11pt"> Fecha Final </label>
            <input type="date" class="form-control input-height" wire:model.live="filtrosMovimientos.fechaFinal" style="font-size:11pt;">
        </div>
       <div class="col-md-3 mb-3">
            <label for="filtrosMovimientos.idTipo" class="form-label text-gray-700" style="font-weight:500;font-size:11pt">Tipo </label>
            <select wire:model.live="filtrosMovimientos.idTipo" id="selectTipo" class="selectpicker select-picker w-100" multiple title="-- TODOS --">
                @foreach ($tiposMovimiento as $tipoMovimiento)
                    <option value="{{ $tipoMovimiento->id }}" data-content="{{ $tipoMovimiento->nombre }}"></option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="filtrosMovimientos.idUsuario" class="form-label  text-gray-700" style="font-weight:500;font-size:11pt">Usuario</label>
            <select wire:model.live="filtrosMovimientos.idUsuario" class="selectpicker select-picker w-100" style="font-size:11pt;">
                <option value="0"> -- TODOS -- </option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}"> {{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover table-hover-custom">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">REFERENCIA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FECHA/HORA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TIPO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">MONTO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">SALDO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">USUARIO</th>
                    {{-- <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($movimientos as $movimiento)
                @php
                    $fechaOriginal = $movimiento->fecha;
                    // Convertir a objeto Carbon
                    $fechaCarbon = Carbon::parse($fechaOriginal);
                    // Formatear la fecha
                    $fechaFormateada = $fechaCarbon->format('d/m/Y H:i:s');

                @endphp
                <tr style="font-size: 10pt;">
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $movimiento->referencia }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $fechaFormateada }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $movimiento->tipo->nombre }}
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; text-align: right;">
                        @if ($movimiento->monto > 0)
                            <i class="fas fa-circle-arrow-up" style="color: green;"></i>
                        @elseif ($movimiento->monto < 0)
                            <i class="fas fa-circle-arrow-down" style="color: red;"></i>
                        @else
                            <i class="fas fa-equals" style="color: black;"></i>
                        @endif
                        @if ($movimiento->monto >= 0)
                            $ {{ number_format($movimiento->monto, 2, '.', ',') }}
                        @else 
                            $ {{ number_format(abs($movimiento->monto), 2, '.', ',') }}
                        @endif
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle; text-align: right;">
                        <span class="badge badge-primary" style="font-size:10pt">
                            $ {{ number_format($movimiento->saldo_caja, 2, '.', ',') }}
                        </span>
                    </td>
                    <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                        {{ $movimiento->usuario->name }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $movimientos->links('livewire.paginame') }}
        </label>
    </div> 
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('abrirModalAgregaMovimiento', () => {
            $('#agregaMovimientoModal').modal('show');
        });

        Livewire.on('cerrarModalAgregaMovimiento', () => {
            $('#agregaMovimientoModal').modal('hide');
        });
    });
</script>
