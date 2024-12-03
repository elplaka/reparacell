@php
    use Carbon\Carbon;
    use App\Models\CobroTallerCreditoDetalle;

    $hayNoDisponibles = false;
    $hayInexistentes = false;
@endphp

<div class="w-full min-h-screen mt-3 font-sans text-gray-900 antialiased">
    @include('livewire.creditos.modal-taller')
    <div class="w-100 d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-2xl font-bold"><b><i class="fa-solid fa-credit-card"></i> Créditos de Taller</b></h4>
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
                <b>Fecha de Salida del Equipo</b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Cliente</b>
            </div>
            <div class="col-md-3 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;">
                <b>Estatus</b>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Fecha de Salida del Equipo</label>
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-2 mb-md-0 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">Del</label>
                        <input type="date" wire:model.live="busquedaCreditos.fechaVentaInicio" class="input-height form-control" style="font-size:11pt;">
                    </div>
                    <div class="col-12 col-md-6 d-flex align-items-center">
                        <label class="font-bold text-gray-700 mr-2 mb-0" style="font-size: 11pt;">&nbsp;&nbsp;Al</label>
                        <input type="date" wire:model.live="busquedaCreditos.fechaVentaFin" class="input-height form-control" style="font-size:11pt;">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Cliente</label>
                <input type="text" wire:model.live="busquedaCreditos.nombreCliente" class="input-height form-control" style="font-size:11pt;">
            </div>
            <div class="col-12 col-md-3 mb-2 text-xs leading-4 font-bold text-gray-700 tracking-wider" style="font-size: 11pt;" wire:ignore>
                <label class="d-block d-md-none font-bold text-gray-700" style="font-size: 11pt;">Estatus</label>
                <select wire:model.live="busquedaCreditos.idEstatus" class="selectpicker select-picker w-100" style="font-size:11pt;">
                    <option value="0">-- TODOS --</option>
                    @foreach ($estatus as $est)
                        <option value="{{ $est->id }}">{{ $est->descripcion }}</option>
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
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">NUM. ORDEN</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">FECHA SALIDA EQUIPO</th>
                    <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">EQUIPO</th>
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
                        $fecha_salida = Carbon::parse($credito->created_at);
                        $suma_abonos = CobroTallerCreditoDetalle::where('num_orden', $credito->num_orden)->sum('abono');
                        if ($credito->cobroTaller) {
                            $restante = $credito->cobroTaller->cobro_realizado - $suma_abonos;
                        } else 
                        {
                            $restante = $credito->cobroEstimado->cobro_estimado - $suma_abonos; // Cobro estimado por defecto
                        }
                        $modeloAux = $credito->equipoTaller->equipo->marca->modelos->where('id_marca',$credito->equipoTaller->equipo->id_marca)->first();

                        if($credito->equipoTaller->equipo->tipo_equipo->disponible)
                        {
                            $tipoEquipo = $credito->equipoTaller->equipo->tipo_equipo->icono;
                        }
                        else 
                        {
                            $tipoEquipo = $credito->equipoTaller->equipo->tipo_equipo->icono . "*";
                            $hayNoDisponibles = true;
                        }

                        if($credito->equipoTaller->equipo->marca->id_tipo_equipo === $credito->equipoTaller->equipo->id_tipo)
                        {                        
                            if($credito->equipoTaller->equipo->marca->disponible)
                            {
                                $nombreMarca = $credito->equipoTaller->equipo->marca->nombre;
                            }
                            else 
                            {
                                $nombreMarca = $credito->equipoTaller->equipo->marca->nombre . "*";
                                $hayNoDisponibles = true;
                            }
                        }
                        else 
                        {
                            $nombreMarca = "*****";
                            $hayInexistentes = true;
                        }

                        if($credito->equipoTaller->equipo->modelo->id_marca === $credito->equipoTaller->equipo->marca->id)
                        {
                            if ($credito->equipoTaller->equipo->modelo->disponible)
                            {
                                $nombreModelo = $credito->equipoTaller->equipo->modelo->nombre;
                            }
                            else 
                            {
                                $nombreModelo = $credito->equipoTaller->equipo->modelo->nombre . "*";
                                $hayNoDisponibles = true;
                            }
                        }
                        else 
                        {
                            $nombreModelo = "*****";
                            $hayInexistentes = true;
                        }
                        if($credito->equipoTaller->equipo->cliente->disponible)
                            {
                                $nombreCliente = $credito->equipoTaller->equipo->cliente->nombre;
                            }
                            else 
                            {
                                $nombreCliente = $credito->equipoTaller->equipo->cliente->nombre . "*";
                                $hayNoDisponibles = true;
                            }
                    @endphp
                    <tr style="font-size: 10pt;" data-toggle="tooltip" data-title="">
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align:middle" width="8%">
                            {{ $credito->num_orden }}  
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $fecha_salida }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> {!! $tipoEquipo !!}   {{ '  ' . $nombreMarca . ' :: ' .  $nombreModelo }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">{{ $nombreCliente }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            $ {{ $credito->cobroTaller ? $credito->cobroTaller->cobro_realizado : $credito->cobroEstimado->cobro_estimado }}
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            $ {{ number_format($restante, 2) }}
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> {{ $credito->equipoTaller->usuario->name }}</td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle"> 
                            {{ $credito->cobroTaller ?  $credito->estatus->descripcion : 'SIN COBRAR' }}
                        </td>
                        <td class="px-2 py-1 whitespace-no-wrap" style="vertical-align: middle">
                            @if ($credito->cobroTaller)
                            <button wire:click="abreTallerCredito({{ $credito->num_orden }})" wire:loading.remove wire:target="abreTallerCredito" class="label-button">
                                <i class="fa-solid fa-eye" style="color: dimgrey; margin-right: 10px;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='dimgrey'"  title="Ver crédito" data-toggle="modal" 
                                data-target="#cobroCreditoTallerModal" ></i>
                            </button>                                
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($hayNoDisponibles || $hayInexistentes)
        {{-- <div class="row">  --}}
            <div class="col-md-10">
                <label class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-bold text-gray-700 uppercase tracking-wider">
                    @if ($hayNoDisponibles)* NO DISPONIBLE @endif @if ($hayInexistentes) &nbsp; ***** INEXISTENTE @endif
                </label>
            </div>
        {{-- </div> --}}
        @endif
    </div>
    <div class="col-mx">
        <label class="col-form-label float-left">
            {{ $creditos->links('livewire.paginame') }}
        </label>
    </div> 
</div>