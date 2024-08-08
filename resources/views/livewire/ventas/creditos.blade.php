@php
    use Carbon\Carbon;
    use App\Models\VentaCreditoDetalle;
    $hayNoDisponibles = false;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.creditos.modal-venta')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-credit-card"></i> Créditos de Ventas</b></h4>
        <span wire:loading style="font-weight:500">Cargando... <i class="fa fa-spinner fa-spin"></i> </span>
    </div>
    @if ($showMainErrors)
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
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
    <div class="w-100">
        <div class="row mb-2 d-none d-md-flex">
            <div class="col-md-4 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <b> Fecha de Venta </b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b> Cliente </b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b> Estatus</b>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Fecha de Venta</label>
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 d-flex align-items-center mb-2 mb-md-0">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">Del</label>
                        <input type="date" wire:model.live="busquedaCreditos.fechaVentaInicio" class="input-height form-control" style="font-size:11pt; max-width: 200px;">
                    </div>
                    <div class="col-12 col-md-6 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">&nbsp;&nbsp;Al</label>
                        <input type="date" wire:model.live="busquedaCreditos.fechaVentaFin" class="input-height form-control" style="font-size:11pt; max-width: 200px;">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-1 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Cliente</label>
                <input type="text" wire:model.live="busquedaCreditos.nombreCliente" class="input-height form-control mb-2" style="font-size:11pt">
            </div>
            <div class="col-12 col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <label class="d-block d-md-none font-bold text-gray-700 mb-1" style="font-size: 11pt;">Estatus</label>
                <select wire:model.live="busquedaCreditos.idEstatus" class="selectpicker select-picker w-100" style="font-size:11pt;">
                    <option value="0"> -- TODOS -- </option>
                    @foreach ($estatus as $est)
                        <option value="{{ $est->id }}"> {{ $est->descripcion }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <br>
    <div class="table-responsive">
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FECHA VENTA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CLIENTE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">TOTAL</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">RESTANTE</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">USUARIO VENTA</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">ESTATUS</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $equipos = 0;
                @endphp
                @foreach ($creditos as $credito)
                    @php
                        $fecha_venta = Carbon::parse($credito->created_at);
                        $suma_abonos = VentaCreditoDetalle::where('id', $credito->id)->sum('abono');
                        $restante = $credito->venta->total - $suma_abonos;
                    @endphp
                    <tr style="font-size: 10pt;" data-toggle="tooltip" data-title="">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="2%">
                            {{ $credito->id }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $fecha_venta }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if ($credito->venta->cliente->disponible)
                            {{ $credito->venta->cliente->nombre }}
                            @else
                            {{ $credito->venta->cliente->nombre . "*" }}
                            @php
                                 $hayNoDisponibles = true;
                            @endphp
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">$ {{ $credito->venta->total }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">$ {{ number_format($restante, 2) }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if ($credito->venta->usuario->disponible)
                                {{ $credito->venta->usuario->name }}
                            @else
                                {{ $credito->venta->usuario->name . "*" }}
                                @php
                                    $hayNoDisponibles = true;
                                @endphp
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> {{ $credito->estatus->descripcion }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> 
                            <button wire:click="abreVentaCredito({{ $credito->id }})" wire:loading.remove wire:target="abreVentaCredito" class="label-button">
                                <i class="fa-solid fa-eye" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"  title="Ver crédito" data-toggle="modal" 
                                data-target="#ventaCreditoModal" ></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($hayNoDisponibles)
        <div class="col-md-5">
            <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
            @if ($hayNoDisponibles)* NO DISPONIBLE @endif
            </label>
        </div>
        @endif
    </div>
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $creditos->links('livewire.paginame') }}
        </label>
    </div> 
</div>