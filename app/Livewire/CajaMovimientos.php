<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\MovimientoCaja;
use App\Models\TipoMovimientoCaja;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Traits\MovimientoCajaTrait;  //Funciones globales de MOVIMIENTOS EN CAJA

class CajaMovimientos extends Component
{
    use MovimientoCajaTrait;
    use WithPagination;
    public $numberOfPaginatorsRendered = [];

    public $showMainErrors;
    public $usuarios, $tiposMovimiento, $tiposMovimientoModal, $saldoCajaActual;

    public $filtrosMovimientos = 
    [
        'fechaInicial',
        'fechaFinal',
        'idTipo',
        'idUsuario'
    ];

    public $nuevoMovimiento = 
    [
        'idTipo',
        'inicialTipo',
        'monto',
    ];

    protected $rules = [
        'nuevoMovimiento.idTipo' => 'required|integer|min:1',
        'nuevoMovimiento.monto' => 'required|numeric|min:0'
    ];
    
    protected $messages = [
        'nuevoMovimiento.idTipo.required' => 'Por favor selecciona un TIPO.',
        'nuevoMovimiento.idTipo.min' => 'Por favor selecciona un TIPO.',
        'nuevoMovimiento.monto.required' => 'Por favor ingresa un MONTO VÁLIDO.',
        'nuevoMovimiento.monto.min' => 'El monto debe ser un VALOR POSITIVO.',
    ];

    public function cerrarModal()
    {
        $this->resetValidation();
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function render()
    {
        $movimientosQuery = MovimientoCaja::query();

        $movimientosQuery->whereDate('fecha', '>=', $this->filtrosMovimientos['fechaInicial'])
            ->whereDate('fecha', '<=', $this->filtrosMovimientos['fechaFinal']);
        
        // Aplicar el filtro idTipo solo si hay elementos seleccionados
        if (!empty($this->filtrosMovimientos['idTipo'])) {
            $movimientosQuery->whereIn('id_tipo', $this->filtrosMovimientos['idTipo']);
        }

        // Aplicar el filtro idUsuario solo si se selecciona un usuario específico
        if ($this->filtrosMovimientos['idUsuario'] != 0) {
            $movimientosQuery->where('id_usuario', $this->filtrosMovimientos['idUsuario']);
        }
            
        $movimientosQuery->orderBy('fecha', 'asc');// Asegúrate de encadenar el método orderBy en el objeto movimientosQuery

        $movimientos = $movimientosQuery->paginate(10);

        // Verificar si la página actual excede el número total de páginas
        if ($movimientos->currentPage() > $movimientos->lastPage()) {
            // Redirigir a la primera página
            $movimientos = $movimientosQuery->paginate(10, ['*'], 'page', 1);
        }

        $ultimoMovimiento = MovimientoCaja::orderBy('fecha', 'desc')->first();
        
        $this->saldoCajaActual = $ultimoMovimiento ? $ultimoMovimiento->saldo_caja : 0;

        return view('livewire.caja-movimientos', compact('movimientos'));
    }

    public function mount()
    {
        $this->filtrosMovimientos = [
            'fechaInicial' => now()->toDateString(),
            'fechaFinal' => now()->toDateString(),
            'idTipo' => [],
            'idUsuario' => 0,
        ];

        $this->nuevoMovimiento = [
            'idTipo' => 0,
            'inicialTipo' => '',
            'monto' => 0
        ];

        $this->resetValidation();

        $this->usuarios = User::all();
        $this->tiposMovimiento = TipoMovimientoCaja::orderBy('nombre', 'asc')->get();
        $this->tiposMovimientoModal = TipoMovimientoCaja::whereIn('id', [4, 5, 6])->orderBy('nombre', 'asc')->get();
    }

    public function abreAgregaMovimiento()
    {
        $this->nuevoMovimiento = [
            'idTipo' => 0,
            'inicialTipo' => '',
            'monto' => 0
        ];

        $this->showMainErrors = false;

        $this->dispatch('abrirModalAgregaMovimiento');
    }

    public function agregaMovimiento()
    {
        $this->validate();

        if (($this->nuevoMovimiento['idTipo'] == 5 && $this->nuevoMovimiento['monto'] == 0)
        || ($this->nuevoMovimiento['idTipo'] == 6 && $this->nuevoMovimiento['monto'] == 0))
        {
            throw ValidationException::withMessages([
                'nuevoMovimiento.monto' => 'Por favor ingresa un monto válido para este tipo.'
            ]);
        }

        if ($this->nuevoMovimiento['idTipo'] == 6)  //Validar que en una SALIDA haya suficiente efectivo
        { 
            $ultimoMovimiento = MovimientoCaja::orderBy('fecha', 'desc')->first();
            $saldoCaja = $ultimoMovimiento->saldo_caja;

            if ($this->nuevoMovimiento['monto'] > $saldoCaja) {
                throw ValidationException::withMessages([
                    'nuevoMovimiento.monto' => 'El MONTO capturado excede el SALDO de la CAJA. Intenta con un MONTO MENOR.'
                ]);
            }
        }

        DB::transaction(function () {
            // Crear el nuevo registro de movimiento
            $movimiento = new MovimientoCaja();
            $movimiento->referencia = $this->regresaReferencia($this->nuevoMovimiento['idTipo'], "0000");
            $movimiento->id_tipo = $this->nuevoMovimiento['idTipo'];
            $movimiento->monto = $this->calculaMonto($this->nuevoMovimiento['idTipo'], $this->nuevoMovimiento['monto']);
            $movimiento->saldo_caja = $this->calculaSaldoCaja($this->nuevoMovimiento['idTipo'], $this->nuevoMovimiento['monto']); // Asegura que el saldo_caja sea un número decimal
            $movimiento->id_usuario = Auth::id();
            $movimiento->save();

            $this->showMainErrors = true;

            $this->dispatch('cerrarModalAgregaMovimiento');
    
            // Mensaje de éxito
            session()->flash('success', 'El MOVIMIENTO DE CAJA se ha registrado correctamente.');
        });    
    }

    
}
