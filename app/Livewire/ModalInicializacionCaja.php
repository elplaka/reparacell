<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\MovimientoCajaTrait;  //Funciones globales de MOVIMIENTOS EN CAJA

class ModalInicializacionCaja extends Component
{
    use MovimientoCajaTrait;

    public $showModal;
    public $fechaCaja, $saldoCajaActual, $formatoFechaEsp, $montoInicializacion;

    protected $rules = [
        'montoInicializacion' => 'required|numeric|min:0'
    ];
    
    protected $messages = [
        'montoInicializacion.required' => 'Por favor ingresa un MONTO VÁLIDO.',
        'montoInicializacion.min' => 'El monto debe ser un VALOR POSITIVO.',
    ];

    function obtenerFechaConDatos() {
        $this->fechaCaja = Carbon::yesterday();
    
        while (!$this->hayDatosParaFecha($this->fechaCaja)) {
            $this->fechaCaja->subDay();
        }
    
        return $this->fechaCaja;
    }
    
    function hayDatosParaFecha($fecha) {
        // Implementa la lógica para verificar si hay datos para la fecha dada
        // Por ejemplo:
        return DB::table('movimientos_caja')
                 ->whereDate('fecha', $fecha)
                 ->exists();
    }

    public function inicializaCaja()
    {
        $this->validate();

        DB::transaction(function () {
            // Crear el nuevo registro de movimiento
            $movimiento = new MovimientoCaja();
            $movimiento->referencia = $this->regresaReferencia(4, "0000");
            $movimiento->id_tipo = 4;
            $movimiento->monto = $this->calculaMonto(4, $this->montoInicializacion);
            $movimiento->saldo_caja = $this->calculaSaldoCaja(4, $this->montoInicializacion); // Asegura que el saldo_caja sea un número decimal
            $movimiento->id_usuario = Auth::id();
            $movimiento->save();
    
            $this->showModal = false;

            $this->dispatch('mostrarToast', 'La INICIALIZACIÓN de la CAJA se realizó exitosamente!!');
        });    
    }
    

    public function mount()
    {
        // Obtener la fecha de hoy
        $hoy = Carbon::today();

        $this->fechaCaja = $this->obtenerFechaConDatos();

        // Verificar si existe un registro con id_tipo = 4 y la fecha de hoy
        $caja_inicializada = MovimientoCaja::where('id_tipo', 4)
        ->whereDate('fecha', $hoy)
        ->exists();

        $ultimoMovimiento = MovimientoCaja::
        whereDate('fecha', $this->fechaCaja)->orderBy('fecha', 'desc')->first();

        $this->saldoCajaActual = $ultimoMovimiento ? $ultimoMovimiento->saldo_caja : 0;

        $this->montoInicializacion = $this->saldoCajaActual;

        $formatoFecha = $this->fechaCaja->format('d-F-Y');

        // Traducción de los nombres de los meses al español
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];

        $partesFecha = explode('-', $formatoFecha);
        $this->formatoFechaEsp = $partesFecha[0] . '-' . $meses[$partesFecha[1]] . '-' . $partesFecha[2];

        if (!$caja_inicializada)
            {
            if (Auth::check()) 
            {
                $this->showModal = true;
            }
        }
    }
    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.modal-inicializacion-caja');
    }
}
