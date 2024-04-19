<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\EquipoTaller;
use Illuminate\Http\Request;
use App\Models\FallaEquipoTaller;
use App\Models\CobroEstimadoTaller;
use App\Models\CobroTaller;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Carbon\Carbon;

class EquipoTallerController extends Controller
{
    public $cobro;
    public $cobroFinal;

    public function index()
    {
        return view('taller.index');
    }

    public function reportesReparaciones()
    {
        return view('taller.reportes');
    }

    public function cobroEquipoTaller($numOrden)
    {
        return redirect()->route('taller.print', $numOrden);
    }

    public function cobroFinalEquipoTaller($numOrden)
    {
        return redirect()->route('taller.print-final', $numOrden);
    }

    public function print($numOrden)
    {
        // $printer_name = "usb://LAP-HP/Ticket";
        // $printer_name = "usb://LAP-HP/POS-58 11.3.0.1 ";
        //Hay que compartir la impresora
        // $printer_name = "Ticket";
        // $connector = new WindowsPrintConnector($printer_name);
        // $printer = new Printer($connector);

        
        // $printer->text("€ 9,95\n");
        // $printer->text("£ 9.95\n");
        // $printer->text("\$ 9.95\n");
        // $printer->text("¥ 9.95\n");
        // $printer->cut();
        // $printer->close();

        // dd('Ya IMP');

        //**********************HASTA ACÁ SÍ ME IMPRIMIÓ**************************************** */
        try
        {
            $printer_name = "Ticket";
            $connector = new WindowsPrintConnector($printer_name);
            $printer = new Printer($connector);

            $equipo_taller = EquipoTaller::where('num_orden', $numOrden)->first();
            $fallas_equipo_taller = FallaEquipoTaller::where('num_orden', $numOrden)->get();
            $cobro = CobroEstimadoTaller::where('num_orden', $numOrden)->latest('id')->first();
            $this->cobro['cliente'] = $equipo_taller->equipo->cliente->nombre;
            $this->cobro['fechaEntrada'] = Carbon::parse($equipo_taller->fecha_entrada)->format('d/m/Y');
            $this->cobro['tipoEquipo'] = $equipo_taller->equipo->tipo_equipo->nombre;
            $this->cobro['marcaEquipo'] = $equipo_taller->equipo->marca->nombre;
            $this->cobro['modeloEquipo'] = $equipo_taller->equipo->modelo->nombre;
            $this->cobro['totalEstimado'] = $cobro->cobro_estimado;
            $this->cobro2['fallasEquipo'] = null;
            $i = 0;
            foreach($fallas_equipo_taller as $falla)
            {
                $this->cobro2['fallasEquipo'][$i++] = $falla->falla;
            }

            // Título centrado
            $titulo = "           REPARACELL\n";

            $printer->text($titulo . "\n");

            // Resto del contenido
            $texto0 = "Cliente: " . $this->cobro['cliente'];
            $texto1 = "Fecha de Entrada: " . $this->cobro['fechaEntrada'];
            $texto2 = "Tipo de Equipo: " . $this->cobro['tipoEquipo'];
            $texto3 = "Marca del Equipo: " . $this->cobro['marcaEquipo'];
            $texto4 = "Modelo del Equipo: " . $this->cobro['modeloEquipo'];
            $texto5 = "Total Estimado: $" . $this->cobro['totalEstimado'];

            // Imprimir los campos
            $printer->text($texto0 . "\n");
            $printer->text($texto1 . "\n");
            $printer->text($texto2 . "\n");
            $printer->text($texto3 . "\n");
            $printer->text($texto4 . "\n");
            $printer->text($texto5 . "\n");

            // Imprimir fallas de equipo si existen
            if (!empty($this->cobro2['fallasEquipo'])) {
                $printer->text("Fallas del Equipo:\n");
                foreach ($this->cobro2['fallasEquipo'] as $falla) {
                    $printer->text("- " . $falla->descripcion . "\n");
                }
            }

            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");

            $printer->text("Para más información sobre tu equipo comunicarse al 6941150179\n");

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

    public function print_final($numOrden)
    {
        //**********************HASTA ACÁ SÍ ME IMPRIMIÓ**************************************** */
        try
        {
            $printer_name = "Ticket";
            $connector = new WindowsPrintConnector($printer_name);
            $printer = new Printer($connector);

            $equipo_taller = EquipoTaller::where('num_orden', $numOrden)->first();
            $fallas_equipo_taller = FallaEquipoTaller::where('num_orden', $numOrden)->get();
            $cobro = CobroTaller::where('num_orden', $numOrden)->first();

            $this->cobroFinal['numOrden'] = $numOrden;
            $this->cobroFinal['cliente'] = $cobro->equipoTaller->equipo->cliente->nombre;
            $this->cobroFinal['fecha'] = now();
            $this->cobroFinal['tipoEquipo'] = $cobro->equipoTaller->equipo->tipo_equipo->nombre;
            $this->cobroFinal['marcaEquipo'] = $cobro->equipoTaller->equipo->marca->nombre;
            $this->cobroFinal['modeloEquipo'] = $cobro->equipoTaller->equipo->modelo->nombre;
            $this->cobroFinal['cobroEstimado'] = $cobro->cobro_estimado;
            $this->cobroFinal['cobroRealizado'] = $cobro->cobro_estimado;
            $this->cobroFinal['idEstatusEquipo'] = $cobro->equipoTaller->id_estatus;
            $this->cobroFinal['fallasEquipo'][] = null;
            $this->cobroFinal['fallasEquipo'] = $cobro->equipoTaller->fallas;

            $this->cobroFinal['fallasEquipo'] = $cobro->equipoTaller->fallas->map(function ($falla) {
                return [
                    'descripcion' => $falla->falla->descripcion,
                ];
            })->toArray();


            // Título centrado
            $titulo = "           REPARACELL\n";

            $printer->text($titulo . "\n");

            // Resto del contenido
            $texto0 = "Cliente: " . $this->cobroFinal['cliente'];
            $texto1 = "Fecha: " . $this->cobroFinal['fecha'];
            $texto2 = "Tipo de Equipo: " . $this->cobroFinal['tipoEquipo'];
            $texto3 = "Marca del Equipo: " . $this->cobroFinal['marcaEquipo'];
            $texto4 = "Modelo del Equipo: " . $this->cobroFinal['modeloEquipo'];
            $texto5 = "Total: $" . $this->cobroFinal['cobroRealizado'];

            // Imprimir los campos
            $printer->text($texto0 . "\n");
            $printer->text($texto1 . "\n");
            $printer->text($texto2 . "\n");
            $printer->text($texto3 . "\n");
            $printer->text($texto4 . "\n");
            $printer->text($texto5 . "\n");

            // dd($this->cobroFinal['fallasEquipo']);

            // Imprimir fallas de equipo si existen
            if (!empty($this->cobroFinal['fallasEquipo'])) {
                $printer->text("Fallas del Equipo:\n");
                foreach ($this->cobroFinal['fallasEquipo'] as $falla) {
                    $printer->text("- " . $falla['descripcion'] . "\n");
                }
            }

            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");
            $printer->text("\n");

            // $printer->text("Para más información sobre tu equipo comunicarse al 6941150179\n");

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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipoTaller $equipoTaller)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipoTaller $equipoTaller)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EquipoTaller $equipoTaller)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipoTaller $equipoTaller)
    {
        //
    }
}
