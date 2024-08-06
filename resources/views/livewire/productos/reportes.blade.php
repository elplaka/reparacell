@php
    use Carbon\Carbon;
    $hayNoDisponibles = false;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
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
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-file-invoice"></i> Reportes de Productos</b></h4>
            <span wire:loading class="ml-2 spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <b> Tipo de Reporte </b>
        </div>
        @if ($reporte['tipo'] == 1 || $reporte['tipo'] == 2)
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <b> Cant.  </b>
            </div>
        @endif
        @if ($reporte['tipo'] == 3)
        <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <b> Tipo de Movimiento  </b>
        </div>
        <div class="col-md-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <b> Fecha Inicial  </b>
        </div>
        <div class="col-md-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <b> Fecha Final  </b>
        </div>
    @endif
    </div>
    <div class="row mb-3">
        <div class="col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <select wire:model.live="reporte.tipo" id="tipoReporte" class="selectpicker select-picker w-100" title='--SELECCIONA UN TIPO--'>
                    <option value="1"> INVENTARIO MÍNIMO</option>
                    <option value="2"> INVENTARIO MÁXIMO</option>
                    <option value="3"> MOVIMIENTOS DE INVENTARIO</option>
            </select>
        </div>
        @if ($reporte['tipo'] == 1)
        <div class="col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <input type="number" step="1" wire:model.live="reporte.inventarioMinimo" class="col-md-4 input-height form-control" style="font-size:11pt">
        </div>
        @endif
        @if ($reporte['tipo'] == 2)
        <div class="col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <input type="number" step="1" wire:model.live="reporte.inventarioMaximo" class="col-md-4 input-height form-control" style="font-size:11pt">
        </div>
        @endif
        @if ($reporte['tipo'] == 3)
        <div class="col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
            <select id="tipoMovimiento" wire:model.live="reporte.tipoMovimiento" class="w-100 select-height">
                <option value="0"> --SELECCIONA UN TIPO--</option>
                @foreach($tiposMovimientos as $tipoMovimiento)
                    <option value="{{ $tipoMovimiento->id }}"> {{ $tipoMovimiento->descripcion_plural }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
            <input type="date" class="form-control input-height" wire:model.live="reporte.fechaMovimientoInicio" style="font-size:11pt; color: rgb(75, 75, 75);">
        </div>
        <div class="col-md-2 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
        <input type="date" class="form-control input-height" wire:model.live="reporte.fechaMovimientoFin" style="font-size:11pt; color: rgb(75, 75, 75);">
        </div>
        @endif
    </div>
        <div class="table-responsive">
        @if (($reporte['tipo'] == 1) || ($reporte['tipo'] == 2))
        <table class="w-full table table-bordered table-hover">            
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">CÓDIGO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DESCRIPCIÓN</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">PRECIO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">INVENTARIO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">DEPARTAMENTO</th>
                </tr>
            </thead>
            <tbody>
                @if (!is_null($productos))
                @foreach ($productos as $producto)
                    <tr style="font-size: 10pt;">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            {{ $producto->codigo }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            {{ $producto->descripcion }}
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                           $ {{ $producto->precio_venta }}
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            {{ $producto->inventario }}
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if ($producto->departamento->disponible)
                                {{ $producto->departamento->nombre }}
                            @else
                                {{ $producto->departamento->nombre . "*" }}
                                @php
                                    $hayNoDisponibles = true;
                                @endphp
                            @endif
                        </td>
                    </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @elseif (($reporte['tipo'] == 3))
        <table class="w-full table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">FECHA MOV.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">TIPO MOV.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">PRODUCTO</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">INVENTARIO ANT.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">INVENTARIO MOV.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">INVENTARIO MIN. ANT.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">INVENTARIO MIN. MOV.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">PRECIO COSTO ANT.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">PRECIO COSTO MOV.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">PRECIO MAYOREO ANT.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">PRECIO MAYOREO MOV.</th>
                    <th class="px-2 py-2 bg-gray-200 text-center align-middle text-xs font-bold text-gray-700 uppercase tracking-wider">USUARIO MOV.</th>
                </tr>
            </thead>
            <tbody>
                @if (!is_null($productos))
                @foreach ($productos as $producto)
                    <tr style="font-size: 10pt;" title="COD. PROD. {{ $producto->codigo_producto }}">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            {{ $producto->created_at->format('d/m/Y H:i') }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            {{ $producto->tipoMovimiento->descripcion }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                           @if ($producto->producto->disponible)
                           {{ $producto->producto->descripcion }}
                           @else
                           {{ $producto->producto->descripcion . "*" }}
                           @php
                                $hayNoDisponibles = true;
                           @endphp
                           @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            @if ($producto->id_tipo_movimiento == 1)
                            -
                            @else
                            {{ $producto->existencia_anterior }}
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            {{ $producto->existencia_movimiento }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            @if ($producto->id_tipo_movimiento == 1)
                            -
                            @else
                            {{ $producto->existencia_minima_anterior }}
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            {{ $producto->existencia_minima_movimiento }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            @if ($producto->id_tipo_movimiento == 1)
                            -
                            @else
                           $ {{ $producto->precio_costo_anterior }}
                            @endif
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                           $ {{ $producto->precio_costo_movimiento }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            @if ($producto->id_tipo_movimiento == 1)
                            -
                            @else
                            $ {{ $producto->precio_mayoreo_anterior }} 
                            @endif 
                         </td>
                         <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                            $ {{ $producto->precio_mayoreo_movimiento }}  
                         </td>
                         <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle">
                           @if ($producto->usuario->disponible)
                           {{ $producto->usuario->name }} 
                           @else
                           {{ $producto->usuario->name . "*" }}
                           @php
                                $hayNoDisponibles = true;
                           @endphp
                           @endif
                         </td>
                    </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @endif
        @if ($hayNoDisponibles)
        <div class="col-md-5">
            <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
            @if ($hayNoDisponibles)* NO DISPONIBLE @endif
            </label>
        </div>
        @endif
    </div>
    @if (!is_null($productos))
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $productos->links('livewire.paginame') }}
        </label>
    </div>
    @endif
</div>

  
