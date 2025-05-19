<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Carbon\Carbon;

class VentaController extends Controller
{
    
    public function index()
    {
        return view('ventas.index');
    }

    public function creditos()
    {
        return view('ventas.creditos');
    }

    public function print($id)
    {
        try
        {
            $printer_name = "Ticket";
            $connector = new WindowsPrintConnector($printer_name);
            $printer = new Printer($connector);

            $venta = Venta::find($id);
            $ventaPrint['cliente'] = $venta->cliente->nombre;

            $ventaPrint['fecha'] = Carbon::parse($venta->created_at)->format('d/m/Y');
            $ventaPrint['hora'] = Carbon::parse($venta->created_at)->format('H:i:s');
            $ventaPrint['total'] = $venta->total;
            
            $ventas_detalles = $venta->detalles;
            $i = 0;
            foreach($ventas_detalles as $venta_detalle)
            {
                $ventaPrint['productos'][$i]['nombre'] = $venta_detalle->producto->descripcion;
                $ventaPrint['productos'][$i]['cantidad'] = $venta_detalle->cantidad;
                $ventaPrint['productos'][$i]['precio'] = $venta_detalle->importe / $venta_detalle->cantidad;
                $ventaPrint['productos'][$i]['subtotal'] = $venta_detalle->importe;
                $i++;
            }

            // Cargar imagen (ajusta la ruta)
            $logo = EscposImage::load(public_path('android.png'), true);

            // --- Configuración básica ---
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(false); // Desactiva negrita (opcional)

            // --- Línea 1: Imagen (izquierda) + Texto (derecha) ---
            $printer->bitImage($logo, Printer::JUSTIFY_LEFT); // Imagen a la izquierda
            
            // Texto a la derecha (3 renglones)
            $printer->setJustification(Printer::JUSTIFY_RIGHT);

            // Título centrado
            $titulo = "CIBER SOCIAL - REPARACELL\n 
            SERVICIO/REPARACIÓN DE CELULARES, TABLETS Y EQUIPOS DE CÓMPUTO\n
            ALVARO OBREGON #9   COL. CENTRO    CONCORDIA, SINALOA\n
            CEL: (694) 115-01-79\n";

            $printer->text($titulo . "\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
             $texto0 = "Cliente: " . $ventaPrint['cliente'];
             $texto1 = "  Fecha: " . $ventaPrint['fecha'];
             $texto2 = "   Hora: " . $ventaPrint['hora'];
             $texto3 =   str_pad("Cant", 5) . 
                        str_pad("Producto", 20) . 
                        str_pad("Precio", 10) . 
                        str_pad("Subtotal", 10) . "\n";
             $texto4 = "----------------------------------------\n";        

            // Imprimir los campos
            $printer->text($texto0 . "\n");
            $printer->text($texto1 . "\n");
            $printer->text($texto2 . "\n");
            $printer->text($texto3);
            $printer->text($texto4);

            $total = 0;

            // Imprimir fallas de equipo si existen
            if (!empty($ventaPrint['productos'])) 
            {
                foreach ($ventaPrint['productos'] as $producto) {
                    $printer->text(
                        str_pad($producto['cantidad'], 5) .
                        str_pad(substr($producto['nombre'], 0, 20), 20) .
                        "$ " . str_pad($producto['precio'], 10) .
                        "$ " . str_pad($producto['subtotal'], 10) .                    
                        "\n"
                    );
                    $total += $producto['subtotal'];
                }
            }


            $texto5 = "----------------------------------------\n";
            $printer->text($texto5);
            $texto6 = str_pad("Total", 35) . "$ " . str_pad($total, 10) . "\n";

            $printer->text($texto6);

            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");

            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");

            // Cortar el papel (si es una impresora térmica)
            $printer->cut();
            // $printer->pulse();

            // Finalizar la conexión con la impresora
            $printer->close();

            echo "Impresión exitosa.";

            // return redirect()->back();
            return redirect()->back()->with('success', '¡Operación exitosa!');
        }
        catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }
}
