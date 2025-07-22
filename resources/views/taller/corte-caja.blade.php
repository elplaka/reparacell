<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> REPARACELL :: Corte de caja de Taller </title>

    @php
        use Carbon\Carbon;
    @endphp

<style>
    body {
        font-family: 'Montserrat', sans-serif;
        font-size: 9pt;
    }

    .bold-text {
        font-weight: 900;
    }

    table th, table td {
        border: 0.75px solid #dee2e6;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
        background-color: #fff; 
        height: 25px;
        padding-left: .75rem;
    }

    table th{
        background-color: #e8e8e8;
        text-transform: uppercase;
    }

    table {
        width: 100%;
        background-color: #fff; 
        color: #333;
        border-spacing: 0;
    }

    h1, h2, h3, h4, h5, h6 {
        margin-top: 0;
        margin-bottom: .5rem;
        font-weight: bold;
    }

    p {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    label {
        display: inline-block;
        height: 0.75cm; /* o cualquier altura deseada */
        line-height: 0.75cm; /* igual a la altura */
        vertical-align: middle;
    }

    #footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
    }

    .page:after {
        content: counter(page);
    }

    .topage:after {
        content: counter(topage);
    }
</style>

   
</head>

<body>
    <p>
        <table>
            <thead>
                <tr>
                    @if ($corteCaja['chkAgrupar'])
                        <th style="text-align: center; width: 3.25cm">CANT.</th>
                        <th style="text-align: left;">PROD/SERV</th>
                        <th style="text-align: right; padding-right:0.25cm">SUBTOTAL</th>
                    @else
                        <th>#</th>
                        <th>ID</th>
                        <th style="width: 3.25cm">Fecha</th>
                        <th>Cliente</th>
                        <th style="width: 6.25cm">Prod/Serv</th>
                        <th>Importe</th>
                        <th>Cajero</th>   
                    @endif
                </tr>
            </thead>
            @php
                $i = 1;
                $total = 0;
                $totalTaller = 0;
                $totalVentas = 0;
                $totalEntradasManuales = 0;
                $totalSalidasManuales = 0;
                $numTaller = 0;
                $numVentas = 0;
                $numEntradasManuales = 0;
                $numSalidasManuales = 0;
                $inicializacionCaja = 0;
            @endphp
            <tbody>
                @foreach ($registros as $registro)
                @if ($registro->tipo == 'INICIALIZACION')
                    @if ($corteCaja['chkAgrupar'])
                        @php
                            $inicializacionCaja = $registro->subtotal;
                        @endphp
                    @else
                        @php
                            $inicializacionCaja = $registro->monto;
                        @endphp
                    @endif
                @else
                @if ($registro->cantidad > 0)
                    <tr>
                        @if ($corteCaja['chkAgrupar'])
                            <td style="text-align: center;"> {{  $registro->cantidad }} </td>
                            <td> {{  $registro->prod_serv }} </td>
                            <td style="text-align: right; padding-right:0.25cm"> $ {{ number_format($registro->subtotal, 2, '.', ',') }}  </td>
                            @php
                            if ($registro->tipo == "TALLER_AGRUPADO" || $registro->tipo == "ABONO_TALLER_AGRUPADO")
                            {
                                $totalTaller += $registro->subtotal;
                                $numTaller++;
                            }
                            else if($registro->tipo == 'ENTRADA_MANUAL_AGRUPADO')
                            {
                                $totalEntradasManuales += $registro->subtotal;
                                $numEntradasManuales++;
                            }
                            else if($registro->tipo == 'SALIDA_MANUAL_AGRUPADO')
                            {
                                $totalSalidasManuales += $registro->subtotal;
                                $numSalidasManuales++;
                            }
                            else 
                            {
                                $totalVentas += $registro->subtotal;
                                $numVentas++;
                            }
                            @endphp
                        @else
                        <td> {{ $i++ }}</td>
                        <td>
                            @if ($registro->tipo == "TALLER")
                                {{'T' . $registro->id }}
                            @elseif($registro->tipo == "ABONO_TALLER")
                                {{'TA' . $registro->id }}
                            @elseif($registro->tipo == "VENTA")
                                {{'V' . $registro->id }}
                            @elseif($registro->tipo == "ENTRADA_MANUAL")
                                {{'ENT' }}
                            @elseif($registro->tipo == "SALIDA_MANUAL")
                                {{'SAL' }}
                            @else
                                {{'VA' . $registro->id }}
                            @endif
                        </td>
                        <td> {{ Carbon::parse($registro->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ Str::limit($registro->nombre_cliente, 18, '...') }} </td>
                        <td>
                        @if ($registro->tipo == "TALLER")
                            REPARACIÓN EN TALLER
                        @elseif ($registro->tipo == "ABONO_TALLER")
                            ABONO DE TALLER
                        @elseif ($registro->tipo == "ABONO_VENTA")
                            ABONO DE VENTA
                        @elseif ($registro->tipo == "ENTRADA_MANUAL")
                            ENTRADA MANUAL
                        @elseif ($registro->tipo == "SALIDA_MANUAL")
                            SALIDA MANUAL
                        @else
                            @foreach ($registro->detalles as $detalle)
                                    {{ $detalle->cantidad }}
                                    @if ($detalle->productoComun)
                                    {{ Str::limit($detalle->productoComun->descripcion_producto, 25, '...') }} 
                                    @else
                                    {{ Str::limit($detalle->producto?->descripcion, 25, '...') }} 
                                    @endif
                                    @if (!$loop->last) <br> @endif
                            @endforeach
                        @endif
                        </td>
                        <td style="text-align:right"> 
                            $ {{ number_format($registro->monto, 2, '.', ',') }} 
                            @if ($registro->id_modo_pago == 1)
                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"> 
                                <path d="M512 80c0 18-14.3 34.6-38.4 48c-29.1 16.1-72.5 27.5-122.3 30.9c-3.7-1.8-7.4-3.5-11.3-5C300.6 137.4 248.2 128 192 128c-8.3 0-16.4 .2-24.5 .6l-1.1-.6C142.3 114.6 128 98 128 80c0-44.2 86-80 192-80S512 35.8 512 80zM160.7 161.1c10.2-.7 20.7-1.1 31.3-1.1c62.2 0 117.4 12.3 152.5 31.4C369.3 204.9 384 221.7 384 240c0 4-.7 7.9-2.1 11.7c-4.6 13.2-17 25.3-35 35.5c0 0 0 0 0 0c-.1 .1-.3 .1-.4 .2c0 0 0 0 0 0s0 0 0 0c-.3 .2-.6 .3-.9 .5c-35 19.4-90.8 32-153.6 32c-59.6 0-112.9-11.3-148.2-29.1c-1.9-.9-3.7-1.9-5.5-2.9C14.3 274.6 0 258 0 240c0-34.8 53.4-64.5 128-75.4c10.5-1.5 21.4-2.7 32.7-3.5zM416 240c0-21.9-10.6-39.9-24.1-53.4c28.3-4.4 54.2-11.4 76.2-20.5c16.3-6.8 31.5-15.2 43.9-25.5l0 35.4c0 19.3-16.5 37.1-43.8 50.9c-14.6 7.4-32.4 13.7-52.4 18.5c.1-1.8 .2-3.5 .2-5.3zm-32 96c0 18-14.3 34.6-38.4 48c-1.8 1-3.6 1.9-5.5 2.9C304.9 404.7 251.6 416 192 416c-62.8 0-118.6-12.6-153.6-32C14.3 370.6 0 354 0 336l0-35.4c12.5 10.3 27.6 18.7 43.9 25.5C83.4 342.6 135.8 352 192 352s108.6-9.4 148.1-25.9c7.8-3.2 15.3-6.9 22.4-10.9c6.1-3.4 11.8-7.2 17.2-11.2c1.5-1.1 2.9-2.3 4.3-3.4l0 3.4 0 5.7 0 26.3zm32 0l0-32 0-25.9c19-4.2 36.5-9.5 52.1-16c16.3-6.8 31.5-15.2 43.9-25.5l0 35.4c0 10.5-5 21-14.9 30.9c-16.3 16.3-45 29.7-81.3 38.4c.1-1.7 .2-3.5 .2-5.3zM192 448c56.2 0 108.6-9.4 148.1-25.9c16.3-6.8 31.5-15.2 43.9-25.5l0 35.4c0 44.2-86 80-192 80S0 476.2 0 432l0-35.4c12.5 10.3 27.6 18.7 43.9 25.5C83.4 438.6 135.8 448 192 448z"/>
                            </svg>
                            @elseif($registro->id_modo_pago == 2)
                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path d="M535 41c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l64 64c4.5 4.5 7 10.6 7 17s-2.5 12.5-7 17l-64 64c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l23-23L384 112c-13.3 0-24-10.7-24-24s10.7-24 24-24l174.1 0L535 41zM105 377l-23 23L256 400c13.3 0 24 10.7 24 24s-10.7 24-24 24L81.9 448l23 23c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L7 441c-4.5-4.5-7-10.6-7-17s2.5-12.5 7-17l64-64c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zM96 64l241.9 0c-3.7 7.2-5.9 15.3-5.9 24c0 28.7 23.3 52 52 52l117.4 0c-4 17 .6 35.5 13.8 48.8c20.3 20.3 53.2 20.3 73.5 0L608 169.5 608 384c0 35.3-28.7 64-64 64l-241.9 0c3.7-7.2 5.9-15.3 5.9-24c0-28.7-23.3-52-52-52l-117.4 0c4-17-.6-35.5-13.8-48.8c-20.3-20.3-53.2-20.3-73.5 0L32 342.5 32 128c0-35.3 28.7-64 64-64zm64 64l-64 0 0 64c35.3 0 64-28.7 64-64zM544 320c-35.3 0-64 28.7-64 64l64 0 0-64zM320 352a96 96 0 1 0 0-192 96 96 0 1 0 0 192z"/>
                            </svg>
                            @endif
                            &nbsp;
                        </td>
                        <td> {{ $registro->cajero }} </td>
                        @php
                            if ($registro->tipo == "TALLER" || $registro->tipo == "ABONO_TALLER")
                            {
                                $totalTaller += $registro->monto;
                                $numTaller++;
                            }
                            else if ($registro->tipo == 'ENTRADA_MANUAL')
                            {
                                $totalEntradasManuales += $registro->monto;
                                $numEntradasManuales++;
                            }
                            else if ($registro->tipo == 'SALIDA_MANUAL')
                            {
                                $totalSalidasManuales += $registro->monto;
                                $numSalidasManuales++;
                            }
                            else 
                            {
                                $totalVentas += $registro->monto;
                                $numVentas++;
                            }
                        @endphp
                        @endif
                    </tr>
                @endif
                @endif
                @endforeach
            </tbody>
        </table>
        <br>
        @php
            $total += $totalTaller + $totalVentas + $inicializacionCaja + $totalEntradasManuales + $totalSalidasManuales;
        @endphp
        <table style="page-break-inside: avoid; width: 50%; margin-left: auto; margin-right: 0; border-collapse: collapse; border: none; border-spacing: 0;">
            <tr>
                <td style="width: 4%; text-align: right; padding: 2px; border: none; vertical-align: middle; line-height: 1;">
                    <strong>
                        &nbsp; SUBTOTAL DE TALLER @if(!$corteCaja['chkAgrupar']) [ {{ $numTaller }} ] @endif :
                    </strong>
                </td>
                <td style="font-size:12pt; text-align: right; padding: 2px; width: 1%; white-space: nowrap; border: none; vertical-align: middle; line-height: 1;">
                    <strong>$ {{ number_format($totalTaller, 2, '.', ',') }}</strong>
                </td>
            </tr>
            @if ($corteCaja['incluyeVentas'] && $numVentas > 0)
            <tr>
                <td style="width: 4%; text-align: right; padding: 2px; border: none; vertical-align: middle; line-height: 1;">
                    <strong>                        
                        &nbsp; SUBTOTAL DE VENTAS @if(!$corteCaja['chkAgrupar']) [ {{ $numVentas }} ] @endif :
                    </strong>
                </td>
                <td style="font-size:12pt; text-align: right; padding: 2px; width: 1%; white-space: nowrap; border: none; vertical-align: middle; line-height: 1;">
                    <strong>$ {{ number_format($totalVentas, 2, '.', ',') }}</strong>
                </td>
            </tr>
            @endif
            <tr>
                <td style="width: 4%; text-align: right; padding: 2px; border: none; vertical-align: middle; line-height: 1;">
                    <strong>
                        &nbsp; INICIALIZACIÓN DE CAJA :
                    </strong>
                </td>
                <td style="font-size:12pt; text-align: right; padding: 2px; width: 1%; white-space: nowrap; border: none; vertical-align: middle; line-height: 1;">
                    <strong>$ {{ number_format($inicializacionCaja, 2, '.', ',') }}</strong>
                </td>
            </tr>
             @if ($numEntradasManuales > 0)
            <tr>
                <td style="width: 4%; text-align: right; padding: 2px; border: none; vertical-align: middle; line-height: 1;">
                    <strong>
                        @if ($numEntradasManuales == 1)
                            @if (!$corteCaja['chkAgrupar'])
                            &nbsp; ENTRADA MANUAL :
                            @else
                            &nbsp; ENTRADAS MANUALES :    
                            @endif
                        @else
                            &nbsp; ENTRADAS MANUALES [ {{ $numEntradasManuales }} ] :
                        @endif
                    </strong>
                </td>
                <td style="font-size:12pt; text-align: right; padding: 2px; width: 1%; white-space: nowrap; border: none; vertical-align: middle; line-height: 1;">
                    <strong>$ {{ number_format($totalEntradasManuales, 2, '.', ',') }}</strong>
                </td>
            </tr>
            @endif
             @if ($numSalidasManuales > 0)
            <tr>
                <td style="width: 4%; text-align: right; padding: 2px; border: none; vertical-align: middle; line-height: 1;">
                    <strong>
                        @if ($numSalidasManuales == 1)
                            @if (!$corteCaja['chkAgrupar'])
                            &nbsp; SALIDA MANUAL :
                            @else
                            &nbsp; SALIDAS MANUALES :    
                            @endif
                        @else
                            &nbsp; SALIDAS MANUALES [ {{ $numSalidasManuales }} ] :
                        @endif
                    </strong>
                </td>
                <td style="font-size:12pt; text-align: right; padding: 2px; width: 1%; white-space: nowrap; border: none; vertical-align: middle; line-height: 1;">
                    <strong>$ {{ number_format($totalSalidasManuales, 2, '.', ',') }}</strong>
                </td>
            </tr>
            @endif
            <tr>
                <td style="font-size:12pt; width: 4%; text-align: right; padding: 2px; border: none; vertical-align: middle; line-height: 1;">
                    <strong>TOTAL :</strong>
                </td>
                <td style="font-size:12pt; text-align: right; padding: 2px; width: 1%; white-space: nowrap; border: none; vertical-align: middle; line-height: 1;">
                    <strong>$ {{ number_format($total, 2, '.', ',') }}</strong>
                </td>
            </tr>
        </table>
    </p>
</body>

</html>
