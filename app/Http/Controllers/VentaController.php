<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Carbon\Carbon;
use Exception; 

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
                if (substr($venta_detalle->codigo_producto, 0, 3) == "COM" && $venta_detalle->productoComun) {
                    $ventaPrint['productos'][$i]['nombre'] = $venta_detalle->productoComun->descripcion_producto;
                } 
                else 
                {
                    $ventaPrint['productos'][$i]['nombre'] = $venta_detalle->producto->descripcion;
                }
                $ventaPrint['productos'][$i]['cantidad'] = $venta_detalle->cantidad;
                $ventaPrint['productos'][$i]['precio'] = $venta_detalle->importe / $venta_detalle->cantidad;
                $ventaPrint['productos'][$i]['subtotal'] = $venta_detalle->importe;
                $i++;
            }   


             $texto0 = "Cliente: " . $ventaPrint['cliente'];
             $texto1 = "  Fecha: " . $ventaPrint['fecha'];
             $texto2 = "   Hora: " . $ventaPrint['hora'];
             $texto3 =   str_pad("Can", 4) . 
                        str_pad("Prod/Serv", 12) . 
                        str_pad("Precio", 8) . 
                        str_pad("Subtot", 8) . "\n";
             $texto4 = "- - - - - - - - - - - - - - - -\n";        

            // Imprimir los campos
            $printer->text($texto0 . "\n");
            $printer->text($texto1 . "\n");
            $printer->text($texto2 . "\n \n");
            $printer->text($texto3);
            $printer->text($texto4);

      
            $total = 0;

            if (!empty($ventaPrint['productos'])) {
                foreach ($ventaPrint['productos'] as $producto) {
                    $precio_formateado = number_format($producto['precio'], 2, '.', '');
                    $subtotal_formateado = number_format($producto['subtotal'], 2, '.', '');

                    $printer->text(sprintf("%3d %-12s %6s %8s\n",
                        $producto['cantidad'],
                        substr($producto['nombre'], 0, 11), // Limita el nombre a 12 caracteres
                        $precio_formateado,
                        $subtotal_formateado
                    ));

                    $total += $producto['subtotal'];
                }
            }

            $texto5 = "- - - - - - - - - - - - - - - - \n";
            $printer->text($texto5);


            $printer->text(sprintf("%23s %8.2f\n", "TOTAL: $", $total));
            $printer->text($texto5);


            //$printer->text($texto6);

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
            $printer->pulse();

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