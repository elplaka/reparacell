<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> REPARACELL :: Corte de caja </title>

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

    /* table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1rem;
        background-color: #fff; 
        color: #333;
    }

    table th, table td {
        border: 1px solid #dee2e6;
        padding: .75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
        background-color: #fff; 
        height: 8px; 
    }

    table th {
        font-weight: 500;
        border-bottom: 2px solid #dee2e6;
        background-color: #e9ecef;
        text-align: inherit;
    }

    table tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05); 
    } */

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
                    <th>#</th>
                    <th>Num. Venta</th>
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
                @foreach ($ventas as $venta)
                <tr>
                    <td> {{ $i++ }}</td>
                    <td>{{ $venta->id }}</td>
                    <td> {{ Carbon::parse($venta->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $venta->cliente->nombre }} </td>
                    {{-- <td style="font-size:8pt"> 
                        @foreach ($venta->detalles as $detalle)
                            {{ $detalle->producto->descripcion . ' - ' }}
                        @endforeach
                    </td> --}}
                    <td> $ {{ number_format($venta->total, 2, '.', ',') }}</td>
                    <td> {{ $venta->usuario->name }}</td>
                    @php
                        $total += $venta->total
                    @endphp
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        TOTAL DE VENTAS: $ {{ number_format($total, 2, '.', ',') }}
    </p>
  
</body>
</html>
