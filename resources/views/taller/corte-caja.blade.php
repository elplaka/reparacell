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
        /* height: 25px; */
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
                <tr style="height: 0.75cm;">
                    <th>#</th>
                    <th>Num. Orden</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    {{-- <th>Detalles</th> --}}
                    <th>Importe</th>
                    <th>Cajero</th>
                </tr>
            </thead>
            @php
                $i = 1;
                $total = 0;
            @endphp
            <tbody>
                @foreach ($cobros as $cobro)
                <tr style="height: 0.75cm;">
                    <td> {{ $i++ }}</td>
                    <td>{{ $cobro->num_orden }}</td>
                    <td> {{ Carbon::parse($cobro->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $cobro->equipoTaller->equipo->cliente->nombre }} </td>
                    <td> $ {{ number_format($cobro->cobro_realizado, 2, '.', ',') }}</td>
                    <td> {{ $cobro->equipoTaller->usuario->name }}</td>
                    @if ($corteCaja['incluyeCredito'])
                        @if (!$cobro->credito)
                            @php
                                $total += $cobro->cobro_realizado
                            @endphp
                        @endif
                    @else
                    @php
                        $total += $cobro->cobro_realizado
                    @endphp
                    @endif
                </tr>
                @if ($corteCaja['incluyeCredito'])
                    @if ($cobro->credito)
                        <tr style="font-size:8pt; font-style: italic;">
                            <td style="border-left: none; border-bottom: none;"></td>
                            <td style="text-align:center"><b> Concepto </b></td>
                            <td style="text-align:center"><b> Monto </b></td>
                            <td style="text-align:center"><b> Fecha </b></td>
                            <td style="text-align:center"><b> Usuario Cobro </b></td>
                            <td style="border-right: none; border-bottom: none;"></td>
                        </tr>
                        @foreach ($cobro->credito->detalles as $credito)
                        <tr style="font-size:8pt; font-style: italic;">
                            <td style="border-left: none; border-top: none;"></td>
                            <td style="text-align:right">
                                @if ($credito->id_abono == 0)
                                ANTICIPO
                                @else
                                    @if ($credito->abono < 0)
                                    DEVOLUCIÓN
                                    @else
                                        @if ($credito == $cobro->credito->detalles->last() && $cobro->credito->id_estatus == 2)
                                        LIQUIDACIÓN
                                        @else
                                        ABONO
                                        @endif
                                    @endif
                                @endif
                                &nbsp; &nbsp;
                            </td>
                            <td style="text-align:right"> $ {{ number_format($credito->abono, 2, '.', ',') }} &nbsp; &nbsp;</td>
                            <td style="text-align:right"> {{ Carbon::parse($credito->created_at)->format('d/m/Y H:i:s') }} &nbsp; &nbsp;</td>
                            <td style="text-align:right">
                                {{ isset($credito->usuario->name) ? $credito->usuario->name : "-" }}  &nbsp; &nbsp;
                            </td>
                            @php
                                $total += $credito->abono
                            @endphp
                        </tr>
                        @endforeach
                    @endif
                @endif
                @endforeach
            </tbody>
        </table>
        <br>
        @if ($corteCaja['incluyeCredito'])
        TOTAL COBRADO: $ {{ number_format($total, 2, '.', ',') }}
        @else
        TOTAL ESTIMADO: $ {{ number_format($total, 2, '.', ',') }}
        @endif
    </p>
  
</body>
</html>
