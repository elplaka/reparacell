<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\EquipoTaller;
use App\Models\TipoEquipo;
use App\Models\MarcaEquipo;
use App\Models\ModeloEquipo;
use App\Models\FallaEquipo;
use App\Models\EstatusEquipo;
use App\Models\FallaEquipoTaller;
use App\Models\CobroEstimadoTaller;
use App\Models\ImagenEquipo;
use App\Models\CobroTallerCredito;
use App\Models\CobroTallerCreditoDetalle;
use App\Models\ModoPago;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\MovimientoCajaTrait;  //Funciones globales de MOVIMIENTOS EN CAJA

class AgregaEquipoTaller extends Component
{
    use WithPagination;
    public $numberOfPaginatorsRendered = [];
    use WithFileUploads;
    use LivewireAlert;
    use MovimientoCajaTrait;

    public $cliente = [
        'estatus',   //0: AGREGAR EQUIPO, 1: NUEVO, 2: SOLO LECTURA, 3: EDITABLE
        'id',
        'nombre',              
        'telefono',  
        'direccion',
        'telefonoContacto',
        'publicoGeneral'    
    ];

    public $equipo = [
        'estatus',   //0: AGREGAR EQUIPO, 1: NUEVO, 2: SOLO LECTURA, 3: EDITABLE
        'id',
        'idTipo',
        'idMarca',
        'idModelo',
        'nombreTipo',       
        'nombreMarca',      
        'nombreModelo'     
    ];

    public $equipoTaller = [
        'numOrden',
        'idEstatus',
        'observaciones',
        'anticipo',
        'idModoPagoAnticipo',
        'totalEstimado',
        'estatus',  //0: AGREGAR EQUIPO, 1: EDITAR
        'agregaAbono'  //true: SE AGREGA ABONO AL EDITAR EL EQUIPO EN TALLER, false: NO SE AGREGA ABONO
    ];

    public $fallas = [];
    public $fallasCostos = [];
    public $numImagenes;
    public $numImagenesEdit;  
    public $showMainErrors, $showModalErrors;
    public $muestraDivAgregaEquipo;
    public $nombreClienteModal;
    public $tieneEquiposCliente;
    public $equiposClienteModal;
    public $equipoSeleccionadoModal;
    public $historialEquipoTaller;

    public $muestraHistorialClienteModal;
    public $muestraHistorialEquipoClienteModal;

    public $marcasEquiposMod, $datosCargados, $modosPago;
    public $modalBuscarClienteAbierta;

    public $imagenes = [];

    public $tipoEquipoMod = [
        'nombre',
        'cveNombre',
        'icono'
    ];

    public $marcaMod = [
        'idTipoEquipo',
        'tipoEquipo',
        'nombre'
    ];

    public $modeloMod = [
        'idMarca',
        'nombre'
    ];

    public $fallaMod = [
        'idTipoEquipo',
        'descripcion',
        'cveDescripcion',
        'costo'
    ];

    public $guardoMarcaOK;
    public $guardoModeloOK;
    public $guardoFallaOK;
    public $guardoTipoEquipoOK;
    public $idTipoEquipo;
    public $imagenIndexToDelete;
    public $mensajeToast = ''; 
    public $fallasEquipoTaller;
    public $modalSoloLectura;
    public $hayItemsNoDisponibles, $hayItemsInexistentes;

    public function mount()
    {
        $this->cliente = [
            'estatus'           => 0,
            'id'                => '',
            'nombre'            => '',
            'telefono'          => '',
            'direccion'         => '',
            'telefonoContacto' => '',
            'publicoGeneral'    => false,
        ];

        $this->equipo = [
            'estatus'           => 1,
            'id'                => null,
            'idTipo'           => 1,
            'idMarca'          => null,
            'idModelo'         => null,
            'nombreTipo'        => null,
            'nombreMarca'       => null,
            'nombreModelo'      => null
        ];

        $this->equipoTaller = [
            'numOrden'      => null,
            'estatus'       => 1,
            'observaciones' => null,
            'anticipo'      => 0,
            'totalEstimado' => 0,
            'agregaAbono'   => false,
            'idModoPagoAnticipo' => 1
        ];

        $this->marcaMod = [
            'idTipoEquipo' => 1,
            'tipoEquipo' => '',
            'nombre' => ''
        ];
    
        $this->modeloMod = [
            'idMarca' => '',
            'nombre'=> ''
        ];

        $this->fallaMod = [
            'idTipoEquipo' => 1,
            'descripcion' => '',
            'cveDescripcion' => '',
            'costo' => ''
        ];

        $this->tipoEquipoMod = [
            'nombre' => '',
            'cveNombre' => '',
            'icono' => ''
        ];

        $this->fallas = [];
        $this->fallasCostos = [];
        $this->imagenes = [];
        $this->numImagenes = 1;
        $this->showModalErrors = false;
        $this->showMainErrors = true;
        $this->muestraDivAgregaEquipo = false;
        $this->nombreClienteModal = '';
        $this->tieneEquiposCliente = false;
        $this->equipoSeleccionadoModal = true;

        $this->muestraHistorialClienteModal = false;
        $this->muestraHistorialEquipoClienteModal = false;

        $this->modalSoloLectura = false;

        $this->modalBuscarClienteAbierta = false;

        $this->marcasEquiposMod = collect();

        $this->modosPago = ModoPago::where('id', '>', 0)->get();
    }

    public function executeRender() 
    { 
        $this->render(); 
    }

    public function render()
    {
        $clientesModal = null;
        $tipos_equipos = collect();
        $marcas_equipos = null;
        $modelos_equipos = null;
        $fallas_equipos = null;
        $estatus_equipos = null;
        $historialClienteTaller = null;

        if ($this->modalBuscarClienteAbierta)
        {  
            if (strlen($this->nombreClienteModal) > 0)
            {
                $clientesModal = Cliente::where('nombre', 'like', '%' . $this->nombreClienteModal . '%')
                    ->where('telefono', '!=', '0000000000')
                    ->where('disponible', 1)
                    ->take(10) 
                    ->paginate(10);
            }

            $this->dispatch('focusTable');
        }
        else
        { 
            $tipos_equipos = TipoEquipo::where('disponible', 1)->get();
            $marcas_equipos = MarcaEquipo::where('id_tipo_equipo', $this->equipo['idTipo'])
            ->where('disponible', 1)
            ->orderBy('nombre', 'asc') // Ordenar por nombre de manera ascendente (A-Z)
            ->get();

            $modelos_equipos = ModeloEquipo::where('id_marca', $this->equipo['idMarca'])
            ->where('disponible', 1)->where('disponible', 1)->orderBy('nombre', 'asc')->get();
            $fallas_equipos = FallaEquipo::where('id_tipo_equipo', $this->equipo['idTipo'])->where('disponible', 1)->get();
            $estatus_equipos = EstatusEquipo::all();

            if ($this->muestraHistorialClienteModal) 
            {
                $historialClienteTaller = Equipo::join('equipos_taller', 'equipos_taller.id_equipo', '=', 'equipos.id')
                ->where('equipos.id_cliente', $this->cliente['id'])
                ->orderBy('equipos_taller.fecha_salida')
                ->orderBy('equipos_taller.num_orden')
                ->select('equipos.*', 'equipos_taller.num_orden','equipos_taller.id_estatus', 'equipos_taller.fecha_salida', 'equipos_taller.observaciones') 
                ->paginate(5);
            }
            else
            {
                $historialClienteTaller = null;
            }
        }

        return view('livewire.agrega-equipo-taller', compact('tipos_equipos', 
        'marcas_equipos', 'modelos_equipos', 'fallas_equipos', 'estatus_equipos', 
        'historialClienteTaller', 'clientesModal'));
    }

    public function updatedNombreClienteModal() 
    { // Actualiza la paginación cuando el nombre del cliente cambia 
        $this->resetPage(); 

        $this->dispatch('focusInput');
    }
 
    public function regresaEquipoCliente($idCliente, $idTipoEquipo, $idMarcaEquipo, $idModeloEquipo)
    {
        $equipo = Equipo::where('id_tipo', $idTipoEquipo)->where('id_marca', $idMarcaEquipo)->where('id_modelo', $idModeloEquipo)->where('id_cliente', $idCliente)->first();

        if ($equipo) return $equipo->id;
        else return null;
    }

    public function equipoEnTaller($idEquipo)
    {
        $equipoTaller = EquipoTaller::where('id_equipo', $idEquipo)
        ->whereNotIn('id_estatus', [5, 6])
        ->first();

        return $equipoTaller ? true : false;
    }

    protected function cobroRepetido($numOrden, $cobroEstimado)
    {
        $cobroEstimado = CobroEstimadoTaller::where('num_orden', $numOrden)->where('cobro_estimado', $cobroEstimado)->first();

        return $cobroEstimado ? true : false;
    }

    public function equipoEnTallerActualiza($idEquipo)
    {
        $equipoTaller = EquipoTaller::where('id_equipo', $idEquipo)
        ->where('num_orden', '!=', $this->equipoTaller['numOrden'])
        ->whereNotIn('id_estatus', [5, 6])
        ->first();

        return $equipoTaller ? true : false;
    }

    public function actualizaEquipo()
    {
        $idEquipo = $this->regresaEquipoCliente($this->cliente['id'], $this->equipo['idTipo'], $this->equipo['idMarca'], $this->equipo['idModelo']);

        if ($this->equipoEnTallerActualiza($idEquipo))
        {
            $this->dispatch('lanzaAdvertenciaEquipoTaller');
        }
        else
        {
            $this->actualizaEquipoTaller();
        }
    }

    public function obtenerCostoFalla($idFalla)
    {
        $falla = FallaEquipo::find($idFalla);
    
        if ($falla) {
            return $falla->costo;
        } else {
            return null;
        }
    }
    

    public function actualizaEquipoTaller()
    {
        $this->validate([
                'cliente.telefono' => ['required',
                'digits:10',
                'numeric',
                ValidationRule::unique('clientes', 'telefono')->ignore($this->cliente['id'], 'id')],
            'cliente.nombre' => ['required', 'string', 'max:50', 'min:1'],
            'cliente.direccion' => ['max:50'],
            'cliente.telefonoContacto' => 'required|digits:10|numeric',
            'equipo.idMarca' => 'required',
            'equipo.idModelo' => 'required',
            'equipoTaller.observaciones' => ['nullable', 'max:50'],
            // 'equipoTaller.anticipo' => ['required', 'numeric', function ($attribute, $value, $fail) {
            //     if ($value > $this->equipoTaller['totalEstimado']) {
            //         $fail('El ANTICIPO no puede ser mayor que el TOTAL.');
            //     }
            // }],
        ], [
            'cliente.telefono.required' => 'El campo <b>Teléfono</b> es obligatorio.',
            'cliente.telefono.digits' => 'El campo <b>Teléfono</b> debe ser de 10 dígitos y contener solo números.',
            'cliente.telefono.numeric' => 'El campo <b>Teléfono</b> debe contener solo números.',
            'cliente.telefono.unique' => 'El teléfono ingresado ya existe en la base de datos.',
            'cliente.nombre.required' => 'El campo <b>Nombre</b> es obligatorio.',
            'cliente.nombre.string' => 'El campo <b>Nombre</b> debe ser una cadena de texto.',
            'cliente.nombre.max' => 'El campo <b>Nombre</b> no debe exceder los 50 caracteres.',
            'cliente.nombre.min' => 'El campo <b>Nombre</b> debe tener al menos 1 caracter.',
            'cliente.direccion.max' => 'El campo <b>Dirección</b> no debe exceder los 50 caracteres.',
            'cliente.telefonoContacto.required' => 'El campo <b>Teléfono</b> es obligatorio.',
            'cliente.telefonoContacto.digits' => 'El campo <b>Teléfono</b> debe ser de 10 dígitos y contener solo números.',

            'equipo.idMarca' => 'El campo <b> Marca </b> es obligatorio.',
            'equipo.idModelo' => 'El campo <b> Modelo </b> es obligatorio.',

            'equipoTaller.observaciones.max' => 'El campo <b>Observaciones</b> no debe exceder los 50 caracteres.',
        ]);

        try {
            DB::transaction(function () {
                if ($this->cliente['estatus'] == 3) {
                    $cliente_existente = Cliente::find($this->cliente['id']);
                    if ($cliente_existente) {
                        $cliente_existente->telefono = $this->cliente['telefono'];
                        $cliente_existente->nombre = trim(mb_strtoupper($this->cliente['nombre']));
                        $cliente_existente->direccion = trim(mb_strtoupper($this->cliente['direccion']));
                        $cliente_existente->telefono_contacto = $this->cliente['telefonoContacto'];
                        $cliente_existente->disponible = 1;
                        $cliente_existente->save();
                    }
                }      

                $idEquipo = $this->regresaEquipoCliente($this->cliente['id'], $this->equipo['idTipo'], $this->equipo['idMarca'], $this->equipo['idModelo']);

                if (is_null($idEquipo))
                {
                $nuevoEquipo = new Equipo;

                $nuevoEquipo->id_tipo = $this->equipo['idTipo'];
                $nuevoEquipo->id_marca = $this->equipo['idMarca'];
                $nuevoEquipo->id_modelo = $this->equipo['idModelo'];
                $nuevoEquipo->id_cliente = $this->cliente['id'];
    
                $nuevoEquipo->save();

                $this->equipo['id'] = $nuevoEquipo->id;
                }
                else
                {
                    $this->equipo['id'] = $idEquipo;
                }

                $numOrden = $this->equipoTaller['numOrden'];

                // Guarda en EQUIPOS_TALLER
                $equipo_taller = EquipoTaller::find($this->equipoTaller['numOrden']);
                $equipo_taller->observaciones = trim(mb_strtoupper($this->equipoTaller['observaciones']));
                $equipo_taller->id_estatus = $this->equipoTaller['idEstatus'];
                $equipo_taller->id_equipo = $this->equipo['id'];
                $equipo_taller->save();

                //array_filter se utiliza para filtrar los elementos con valor true, y luego array_keys se utiliza para obtener los índices correspondientes.
                $idsFallas = array_keys(array_filter($this->fallas, function ($valor) {
                    return $valor === true;
                }));

                //Borra las fallas para insertar sólo las seleccionadas y actualizar correctamente
                $fallasEquipo = FallaEquipoTaller::where('num_orden', $this->equipoTaller['numOrden'])->delete();

                $k = 0;
                $cobroEstimado = 0;
                foreach ($idsFallas as $fallaId) {
                    $falla = new FallaEquipoTaller();
                    $falla->num_orden = $numOrden;
                    $falla->id_falla = $fallaId;
                    $falla->costo = $this->obtenerCostoFalla($fallaId);
                    $catFallas = FallaEquipo::find($fallaId);
                    $cobroEstimado += $catFallas->costo;
                    $falla->save();
                    $k++;
                }
      
                CobroEstimadoTaller::where('num_orden', $numOrden)->update(['cobro_estimado' => $cobroEstimado]);

                $imagenesEquipo = ImagenEquipo::where('num_orden', $this->equipoTaller['numOrden'])->delete();

                foreach ($this->imagenes as $key => $imagen) 
                {
                    if (gettype($imagen) === 'string') {
                        // Es una cadena, asume que es un nombre de archivo y guárdalo como tal
                        $imagenEquipo = new ImagenEquipo();
                        $imagenEquipo->num_orden = $numOrden;
                        $imagenEquipo->nombre_archivo = $imagen;
                        $imagenEquipo->save();
                    }
                    elseif ($imagen instanceof \Illuminate\Http\UploadedFile) 
                    {
                        $extension = $imagen->getClientOriginalExtension(); // Obtiene la extensión del archivo original

                        // Asegurémonos de que $numOrden tenga tres dígitos
                        $numOrdenStr = str_pad($numOrden, 3, '0', STR_PAD_LEFT);
                        $nombreUnico = $numOrdenStr . Str::random(8) . '.' . $extension; // Genera un nombre único con extensión
                        $rutaArchivo = $imagen->storeAs('public/imagenes-equipos', $nombreUnico); // Guarda la imagen con un nombre único y extensión

                        $imagenEquipo = new ImagenEquipo();
                        $imagenEquipo->num_orden = $numOrden;
                        $imagenEquipo->nombre_archivo = $nombreUnico;
                        $imagenEquipo->save();
                    }
                }

                if ($this->equipoTaller['anticipo'] > 0 && $this->equipoTaller['agregaAbono'])
                {
                    
                    $existingRecord = CobroTallerCredito::where('num_orden', $numOrden)->first();

                    if (!$existingRecord) {
                        $cobroTallerCredito = new CobroTallerCredito();
                        $cobroTallerCredito->num_orden = $numOrden;
                        $cobroTallerCredito->id_cliente = $this->cliente['id'];
                        $cobroTallerCredito->id_estatus = 1;
                        $cobroTallerCredito->save();
                    }

                    $cobroTallerCreditoDetalle = new CobroTallerCreditoDetalle();
                    $cobroTallerCreditoDetalle->num_orden = $numOrden;
                    $cobroTallerCreditoDetalle->abono = $this->equipoTaller['anticipo'];
                    $cobroTallerCreditoDetalle->id_modo_pago = $this->equipoTaller['idModoPagoAnticipo'];
                    $cobroTallerCreditoDetalle->id_usuario_cobro = Auth::id();
                    $cobroTallerCreditoDetalle->save();

                    if ($this->equipoTaller['idModoPagoAnticipo'] == 1) //Si es EFECTIVO se registra el MOVIMIENTO
                    { 
                        $idRef = $numOrden % 1000;
                        $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);

                        $movimiento = new MovimientoCaja();
                        $movimiento->referencia = $this->regresaReferencia(3, $idRef);
                        $movimiento->id_tipo = 3;
                        $movimiento->monto = $this->calculaMonto(3, $this->equipoTaller['anticipo']);
                        $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $this->equipoTaller['anticipo']); // Asegura que el saldo_caja sea un número decimal
                        $movimiento->id_usuario = Auth::id();
                        $movimiento->save();
                    }

                    $this->equipoTaller['agregaAbono'] = false;
                }

                $this->muestraDivAgregaEquipo = false;
                $this->dispatch('mostrarToast', 'Equipo actualizado con éxito!!!');
                $this->dispatch('ocultaDivAgregaEquipo');

                $this->mount();

            });
        } catch (\Exception $e) {
            // Manejo de errores si ocurre una excepción
            // Puedes agregar logs o notificaciones aquí
            dd($e);
        }
    }

    public function agregaAbono()  //ESTOY AGREGANDO UN ABONO AL DAR CLICK AL BOTÓN DE + PERO ESTE CÓDIGO ES AL GUARDAR
    {
        $this->equipoTaller['agregaAbono'] = true;        
    }

    public function cierraModalActualizarModoPago()
    {
        
    }

    public function agregaEquipoTaller()
    {
        $this->validate([
                'cliente.telefono' => ['required',
                'digits:10',
                'numeric',
                ValidationRule::unique('clientes', 'telefono')->ignore($this->cliente['id'], 'id')],
            'cliente.nombre' => ['required', 'string', 'max:50', 'min:1'],
            'cliente.direccion' => ['max:50'],
            'cliente.telefonoContacto' => 'required|digits:10|numeric',
            'equipo.idMarca' => 'required',
            'equipo.idModelo' => 'required',
            'equipoTaller.observaciones' => ['nullable', 'max:50'],
            // 'equipoTaller.anticipo' => ['required', 'numeric', function ($attribute, $value, $fail) {
            //     if ($value > $this->equipoTaller['totalEstimado']) {
            //         $fail('El ANTICIPO no puede ser mayor que el TOTAL.');
            //     }
            // }],
        ], [
            'cliente.telefono.required' => 'El campo <b>Teléfono</b> es obligatorio.',
            'cliente.telefono.digits' => 'El campo <b>Teléfono</b> debe ser de 10 dígitos y contener solo números.',
            'cliente.telefono.numeric' => 'El campo <b>Teléfono</b> debe contener solo números.',
            'cliente.telefono.unique' => 'El teléfono ingresado ya existe en la base de datos.',
            'cliente.nombre.required' => 'El campo <b>Nombre</b> es obligatorio.',
            'cliente.nombre.string' => 'El campo <b>Nombre</b> debe ser una cadena de texto.',
            'cliente.nombre.max' => 'El campo <b>Nombre</b> no debe exceder los 50 caracteres.',
            'cliente.nombre.min' => 'El campo <b>Nombre</b> debe tener al menos 1 caracter.',
            'cliente.direccion.max' => 'El campo <b>Dirección</b> no debe exceder los 50 caracteres.',
            'cliente.telefonoContacto.required' => 'El campo <b>Teléfono</b> es obligatorio.',
            'cliente.telefonoContacto.digits' => 'El campo <b>Teléfono</b> debe ser de 10 dígitos y contener solo números.',

            'equipo.idMarca' => 'El campo <b> Marca </b> es obligatorio.',
            'equipo.idModelo' => 'El campo <b> Modelo </b> es obligatorio.',

            'equipoTaller.observaciones.max' => 'El campo <b>Observaciones</b> no debe exceder los 50 caracteres.',
        ]);

        try {
            DB::transaction(function () {
                if ($this->cliente['estatus'] == 1) {
                    $cliente_existente = Cliente::find($this->cliente['id']);
        
                    if ($cliente_existente) {
                        // Actualizar cliente
                    } else {
                        $cliente = new Cliente();
                        $cliente->telefono = $this->cliente['telefono'];
                        $cliente->nombre = trim(mb_strtoupper($this->cliente['nombre']));
                        $cliente->direccion = trim(mb_strtoupper($this->cliente['direccion']));
                        $cliente->telefono_contacto = $this->cliente['telefonoContacto'];
                        $cliente->disponible = 1;
                        $cliente->save();

                        $this->cliente['id'] = $cliente->id;
                    }
                }
                elseif ($this->cliente['estatus'] == 3) {
                    $cliente_existente = Cliente::find($this->cliente['id']);

                    if ($cliente_existente) {
                        $cliente_existente->telefono = $this->cliente['telefono'];
                        $cliente_existente->nombre = trim(mb_strtoupper($this->cliente['nombre']));
                        $cliente_existente->direccion = trim(mb_strtoupper($this->cliente['direccion']));
                        $cliente_existente->telefono_contacto = $this->cliente['telefonoContacto'];
                        $cliente_existente->disponible = 1;
                        $cliente_existente->save(); 
                    }
                }

                $equipo_existente = $this->regresaEquipo($this->equipo['id']);

                if ($equipo_existente) 
                {
                    if ($this->equipo['estatus'] == 3)
                    {
                        $equipo_existente->id_tipo = $this->equipo['idTipo'];
                        $equipo_existente->id_marca = $this->equipo['idMarca'];
                        $equipo_existente->id_modelo = $this->equipo['idModelo'];
                        $equipo_existente->save();
                    }
                }
                else
                {
                    //Se busca el equipo seleccionado para ver si ya existe
                    $idEquipo = $this->regresaEquipoCliente($this->cliente['id'], $this->equipo['idTipo'], $this->equipo['idMarca'], $this->equipo['idModelo']);

                    //Si no existe el equipo se da de alta
                    if (is_null($idEquipo))
                    {
                        $equipo = new Equipo();
                        $equipo->id_cliente = $this->cliente['id'];
                        $equipo->id_tipo = $this->equipo['idTipo'];
                        $equipo->id_marca = $this->equipo['idMarca'];
                        $equipo->id_modelo = $this->equipo['idModelo'];
                        $equipo->save();

                        $this->equipo['id'] = $equipo->id;
                    }
                    else  //Si existe se asigna el idEquipo a la propiedad
                    {
                        $this->equipo['id'] = $idEquipo;
                    }
                }

                // Guarda en EQUIPOS_TALLER
                $equipo_taller = new EquipoTaller();
                $equipo_taller->id_equipo = $this->equipo['id'];
                $equipo_taller->id_usuario_recibio = Auth::id();
                $equipo_taller->id_estatus = 1;
                $equipo_taller->observaciones = strlen(trim($this->equipoTaller['observaciones'])) == 0 ? "NINGUNA" : trim(mb_strtoupper($this->equipoTaller['observaciones']));
                
                $equipo_taller->save(); 

                $numOrden = $equipo_taller->num_orden;
                $idCliente = $equipo_taller->equipo->cliente->id;

                $idsFallas = array_keys(array_filter($this->fallas, function ($valor) {
                    return $valor === true;
                }));

                $cobroEstimado = 0;
                foreach ($idsFallas as $fallaId) {
                    $falla = new FallaEquipoTaller();
                    $falla->num_orden = $numOrden;
                    $falla->id_falla = $fallaId;
                    $catFallas = FallaEquipo::find($fallaId);
                    $falla->costo = $catFallas->costo;
                    if ($catFallas) {
                        $cobroEstimado += $catFallas->costo;
                    }
                    $falla->save();
                }

                $cobroEstimadoTaller = new CobroEstimadoTaller();
                $cobroEstimadoTaller->id = 1;
                $cobroEstimadoTaller->num_orden = $numOrden;
                $cobroEstimadoTaller->cobro_estimado = $cobroEstimado;
                $cobroEstimadoTaller->save();

                $actualizaEquipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                // $actualizaEquipoTaller->id_cobro_estimado = $maxId;
                $actualizaEquipoTaller->id_cobro_estimado = 1;
                $actualizaEquipoTaller->save();

                foreach ($this->imagenes as $key => $imagen) {
                    if ($imagen) {
                        $extension = $imagen->getClientOriginalExtension(); // Obtiene la extensión del archivo original

                        // Asegurémonos de que $numOrden tenga tres dígitos
                        $numOrdenStr = str_pad($numOrden, 3, '0', STR_PAD_LEFT);
                        $nombreUnico = $numOrdenStr . Str::random(8) . '.' . $extension; // Genera un nombre único con extensión
                        $rutaArchivo = $imagen->storeAs('public/imagenes-equipos', $nombreUnico); // Guarda la imagen con un nombre único y extensión

                        $imagenEquipo = new ImagenEquipo();
                        $imagenEquipo->num_orden = $numOrden;
                        $imagenEquipo->nombre_archivo = $nombreUnico;
                        $imagenEquipo->save();
                    }
                }

                    if ($this->equipoTaller['anticipo'] > 0)
                    {
                        $cobroTallerCredito = new CobroTallerCredito();
                        $cobroTallerCredito->num_orden = $numOrden;
                        $cobroTallerCredito->id_cliente = $idCliente;
                        $cobroTallerCredito->id_estatus = 1;
                        $cobroTallerCredito->save();

                        $cobroTallerCreditoDetalle = new CobroTallerCreditoDetalle();
                        $cobroTallerCreditoDetalle->num_orden = $numOrden;
                        $cobroTallerCreditoDetalle->abono = $this->equipoTaller['anticipo'];
                        $cobroTallerCreditoDetalle->id_modo_pago = $this->equipoTaller['idModoPagoAnticipo'];
                        $cobroTallerCreditoDetalle->id_usuario_cobro = Auth::id();
                        $cobroTallerCreditoDetalle->save();

                        if ($this->equipoTaller['idModoPagoAnticipo'] == 1) //Si es EFECTIVO se registra el MOVIMIENTO
                        { 
                            $idRef = $numOrden % 1000;
                            $idRef = "R" . str_pad($idRef, 3, '0', STR_PAD_LEFT);
    
                            $movimiento = new MovimientoCaja();
                            $movimiento->referencia = $this->regresaReferencia(3, $idRef);
                            $movimiento->id_tipo = 3;
                            $movimiento->monto = $this->calculaMonto(3, $this->equipoTaller['anticipo']);
                            $movimiento->saldo_caja = $this->calculaSaldoCaja(3, $this->equipoTaller['anticipo']); // Asegura que el saldo_caja sea un número decimal
                            $movimiento->id_usuario = Auth::id();
                            $movimiento->save();
                        }
                    }
                // }
  
                $this->muestraDivAgregaEquipo = false;
                $this->dispatch('ocultaDivAgregaEquipo');
                $this->dispatch('mostrarToast', 'Equipo agregado con éxito!!!');

                $this->mount();

                // return redirect()->route('taller.print', $numOrden);
            });
        } catch (\Exception $e) {
            // Manejo de errores si ocurre una excepción
            // Puedes agregar logs o notificaciones aquí
            dd($e);
        }
    }

    public function aceptaEquipo() 
    {  
        $this->showMainErrors = true;
        $this->showModalErrors = false;

        //Si es público en general no valida si ya existe el equipo
        if ($this->cliente['telefono'] == "0000000000")  
        {
            $this->agregaEquipoTaller();
        }
        else
        {
            $idEquipo = $this->regresaEquipoCliente($this->cliente['id'], $this->equipo['idTipo'], $this->equipo['idMarca'], $this->equipo['idModelo']);

            if ($this->equipoEnTaller($idEquipo))
            {
                $this->dispatch('lanzaAdvertenciaEquipoTaller');
            }
            else
            {
                $this->agregaEquipoTaller();
            }
        }
    }

    public function nuevoEquipoCliente()
    {
        $this->equipo = [
            'estatus'           => 1,
            'id'                => null,
            'idTipo'           => 1,
            'idMarca'          => null,
            'idModelo'         => null,
            'nombreTipo'        => null,
            'nombreMarca'       => null,
            'nombreModelo'      => null
        ];

        $this->dispatch('cerrarModalEquiposCliente');
    }

    public function abrirEquiposClienteModal()
    {
        $this->datosCargados = false;

        $this->equiposClienteModal = $this->regresaEquiposCliente($this->cliente['id']);

        $this->datosCargados = true;
        $this->hayItemsNoDisponibles = false;
        $this->hayItemsInexistentes = false;     
    }

    public function regresaEquiposCliente($idCliente)
    {
        $equipos = Equipo::where('id_cliente', $idCliente)->where('disponible', 1)->get();

        // $equipos = Equipo::where('id_cliente', $idCliente)
        // ->where('disponible', 1)
        // ->with(['modelo', 'marca', 'tipo_equipo'])
        // ->get()
        // ->map(function ($equipo) {
        //     $equipo->total_marcas_inexistentes = DB::table('equipos as e')
        //         ->join('marcas_equipos as m', 'e.id_marca', '=', 'm.id')
        //         ->join('tipos_equipos as t', 'e.id_tipo', '=', 't.id')
        //         ->where('e.id_cliente', $equipo->id_cliente)
        //         ->where('e.disponible', 1)
        //         ->whereColumn('m.id_tipo_equipo', '<>', 'e.id_tipo')
        //         ->count();

        //     $equipo->total_modelos_inexistentes = DB::table('equipos as e')
        //         ->join('modelos_equipos as mo', 'e.id_modelo', '=', 'mo.id')
        //         ->join('marcas_equipos as m', 'e.id_marca', '=', 'm.id')
        //         ->where('e.id_cliente', $equipo->id_cliente)
        //         ->where('e.disponible', 1)
        //         ->whereColumn('mo.id_marca', '<>', 'm.id')
        //         ->count();

        //     $equipo->total_tipos_equipos_no_disponibles = DB::table('equipos as e')
        //         ->join('tipos_equipos as t', 'e.id_tipo', '=', 't.id')
        //         ->where('e.id_cliente', $equipo->id_cliente)
        //         ->where('e.disponible', 1)
        //         ->where('t.disponible', 0)
        //         ->distinct()
        //         ->count('t.id');

        //     $equipo->total_marcas_no_disponibles = DB::table('equipos as e')
        //         ->join('marcas_equipos as m', 'e.id_marca', '=', 'm.id')
        //         ->where('e.id_cliente', $equipo->id_cliente)
        //         ->where('e.disponible', 1)
        //         ->where('m.disponible', 0)
        //         ->distinct()
        //         ->count('m.id');
            
        //     $equipo->total_modelos_no_disponibles = DB::table('equipos as e')
        //         ->join('modelos_equipos as mo', 'e.id_modelo', '=', 'mo.id')
        //         ->where('e.id_cliente', $equipo->id_cliente)
        //         ->where('e.disponible', 1)
        //         ->where('mo.disponible', 0)
        //         ->distinct()
        //         ->count('mo.id');

        //     return $equipo;
        // });
        

        // dd($equipos);

        return $equipos;
    }

    public function hazParo()   //No hace nada pero hace que funcione updatedFallas
    {
    }

    public function updatedFallas($value, $key)
    {
        $costo = FallaEquipo::find($key)->costo;

        if ($value)
        {
            $this->fallasCostos[$key] = $costo;
        }
        else
        {
            if(isset($this->fallasCostos[$key])) unset($this->fallasCostos[$key]);
        }

        $this->equipoTaller['totalEstimado'] = array_sum($this->fallasCostos);
    }

    public function updatedClienteTelefono($value)
    {
        if(strlen($value) == 10)
        {
            $todosDigitos = $this->validarNumeros();

            if (!$todosDigitos) return false;
        }

        if (!$this->modalBuscarClienteAbierta)
        {
            if ($this->cliente['estatus'] != 3)   //Si el cliente no es editable entonces que busque clientes
            {
                if(strlen($value) == 10)
                {
                    $cliente = $this->regresaCliente($this->cliente['telefono']);

                    if (isset($cliente))   //Cliente ya existente
                    {
                        $this->cliente['id'] = $cliente->id;
                        $this->cliente['estatus'] = 2;  //Cliente solo lectura
                        $this->cliente['nombre'] = $cliente->nombre;
                        $this->cliente['direccion'] = $cliente->direccion;
                        $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;
                        $this->equiposClienteModal = $this->regresaEquiposCliente($cliente->id);
                        $this->tieneEquiposCliente = $this->equiposClienteModal->count() > 0 ? true : false;
                        $this->equipoSeleccionadoModal = false;

                        $this->cliente['estatus'] = 2;
                        $this->cliente['publicoGeneral'] = false;
                        $this->equipoTaller['estatus'] = 0;

                        if ($this->tieneEquiposCliente && $this->cliente['telefonoContacto'] != "0000000000")
                        {
                            $this->equipo['estatus'] = 0;
                            $this->hayItemsNoDisponibles = false;
                            $this->hayItemsInexistentes = false;
                            $this->dispatch('abreModalEquiposCliente');    //El listener está en layouts.main
                        }

                        if ($this->cliente['telefonoContacto'] == "0000000000")
                        {
                            $this->equipo['estatus'] = 1;
                            $this->cliente['publicoGeneral'] = true;
                            $this->cliente['estatus'] = 2;
                            // $this->equipoTaller['estatus'] = 0;
                        }
                    }
                    else   //Cliente nuevo
                    {
                        $this->cliente['id'] = -1;
                        $this->cliente['estatus'] = 1;  //Cliente nuevo
                        $this->cliente['nombre'] = '';
                        $this->cliente['direccion'] = '';
                        $this->cliente['telefonoContacto'] = $this->cliente['telefono'];
                        $this->equipo['id_tipo'] = 1;   //Valor por default (Celular)

                        $this->equipo['estatus'] = 1;
                        $this->equipo['idMarca'] = null;
                        $this->equipo['idModelo']  = null;

                        $this->equipoTaller['estatus'] = 0;
                    }

                    $this->cliente['publicoGeneral'] = $this->cliente['telefono'] == '0000000000' ? true : false;
                }
                else
                {
                    if (strlen($value) >= 2 && $value == '00')   //Público General
                    {
                        $this->cliente['telefono'] = '0000000000';
                        $cliente = $this->regresaCliente($this->cliente['telefono']);
                        if (isset($cliente))
                        {
                            $this->cliente['id'] = $cliente->id;
                            $this->cliente['estatus'] = 2;
                            $this->cliente['nombre'] = $cliente->nombre;
                            $this->cliente['direccion'] = $cliente->direccion;
                            $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;
                        }
                        $this->cliente['publicoGeneral'] = true;
                        $this->equipo['estatus'] = 1;
                        $this->equipoTaller['estatus'] = 0;
                    }
                    else  
                    {
                        if (strlen($value) <= 10) 
                        {
                            $this->cliente['publicoGeneral'] = false;
                            $this->cliente['estatus'] = 0;
                            $this->equipoTaller['estatus'] = 0;
                        }
                    }
                }
            }
        }
    }
    
    public function abreClienteHistorial()
    {
        $historialClienteTaller = Equipo::join('equipos_taller', 'equipos_taller.id_equipo', '=', 'equipos.id')
        ->where('equipos.id_cliente', $this->cliente['id'])
        ->orderBy('equipos_taller.fecha_salida')
        ->orderBy('equipos_taller.num_orden')
        ->select('equipos.*', 'equipos_taller.num_orden','equipos_taller.id_estatus', 'equipos_taller.fecha_salida', 'equipos_taller.observaciones')
        ->paginate(5);
        // ->get();

        $this->gotoPage(1);

        // dd($this->historialClienteTaller);

        $this->muestraHistorialClienteModal = true;
    }

    public function cierraModalClienteHistorial()
    {
        $this->muestraHistorialClienteModal = false;
    }

    public function cierraModalEquipoClienteHistorial()
    {

    }

    public function cierraModalEquiposCliente()
    {
        
    }

    public function cierraBuscarClienteModal()
    {
        $this->modalBuscarClienteAbierta = false;
    }
    

    public function updatedImagenes()
    {
        // $this->validate([
        //     'imagenes.*' => 'image|max:3072', // Ejemplo: solo se permiten imágenes JPEG y PNG de hasta 3MB
        // ], [
        //     'imagenes.*.max' => 'El tamaño del archivo de la IMAGEN no debe exceder los 3M',
        //     'imagenes.*.image' => 'El archivo de la IMAGEN debe ser JPG'
        // ]);

        $this->validate([
            'imagenes.*' => [
                'max:3072',
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        if ($value->getClientOriginalExtension() !== 'jpg') {
                            $fail("El archivo en la posición {$attribute} debe ser un archivo JPG.");
                        }
                    }
                }
            ],
        ], [
            'imagenes.*.max' => 'El tamaño del archivo en la posición :attribute no debe exceder los 3M',
        ]);        
    }

    public function editarCliente()
    {
        $this->cliente['estatus'] = 3;   //Estatus para editar el cliente
    }

    public function editarEquipo()
    {
        $this->equipo['estatus'] = 3;   //Estatus para editar el equipo
    }

    public function guardarCliente()
    {
        $this->validate([
            'cliente.telefono' => ['required',
            'digits:10',
            'numeric',
            ValidationRule::unique('clientes', 'telefono')->ignore($this->cliente['id'], 'id')],
            'cliente.nombre' => ['required', 'string', 'max:50', 'min:1'],
            'cliente.direccion' => ['max:50'],
            'cliente.telefonoContacto' => 'required|digits:10|numeric',
        ], [
            'cliente.telefono.required' => 'El campo <b>Teléfono</b> es obligatorio.',
            'cliente.telefono.digits' => 'El campo <b>Teléfono</b> debe ser de 10 dígitos y contener solo números.',
            'cliente.telefono.numeric' => 'El campo <b>Teléfono</b> debe contener solo números.',
            'cliente.telefono.unique' => 'El teléfono ingresado ya existe en la base de datos.',
            'cliente.nombre.required' => 'El campo <b>Nombre</b> es obligatorio.',
            'cliente.nombre.string' => 'El campo <b>Nombre</b> debe ser una cadena de texto.',
            'cliente.nombre.max' => 'El campo <b>Nombre</b> no debe exceder los 50 caracteres.',
            'cliente.nombre.min' => 'El campo <b>Nombre</b> debe tener al menos 1 caracter.',
            'cliente.direccion.max' => 'El campo <b>Dirección</b> no debe exceder los 50 caracteres.',
            'cliente.telefonoContacto.required' => 'El campo <b>Teléfono</b> es obligatorio.',
            'cliente.telefonoContacto.digits' => 'El campo <b>Teléfono</b> debe ser de 10 dígitos y contener solo números.',
        ]);


        $cliente = Cliente::find($this->cliente['id']);
        if ($cliente) {
            $cliente->telefono = $this->cliente['telefono'];
            $cliente->nombre = trim(mb_strtoupper($this->cliente['nombre']));
            $cliente->direccion = trim(mb_strtoupper($this->cliente['direccion']));
            $cliente->telefono_contacto = $this->cliente['telefonoContacto'];
            
            $cliente->save();

            $this->cliente['estatus'] = 2;   //Estatus de cliente solo lectura

            session()->flash('success', 'El CLIENTE se ha actualizado correctamente.');
        }
    }

    public function guardarEquipo()
    {
        $this->validate([
            'equipo.idMarca' =>  ['required'],
            'equipo.idModelo' =>  ['required'],
        ], [
            'equipo.idMarca.required' => 'El campo <b>Marca</b> es obligatorio.',
            'equipo.idModelo.required' => 'El campo <b>Modelo</b> es obligatorio.',
        ]);

        $this->showMainErrors = true;
        $this->showModalErrors = false;

        $equipo = Equipo::find($this->equipo['id']);
        if ($equipo) {
            $equipo->id_tipo = $this->equipo['idTipo'];
            $equipo->id_marca = $this->equipo['idMarca'];
            $equipo->id_modelo = $this->equipo['idModelo'];

            $equipo->save();

            $this->equipo['nombreTipo'] = $equipo->tipo_equipo->nombre;
            $this->equipo['nombreMarca'] = $equipo->marca->nombre;
            $this->equipo['nombreModelo'] = $equipo->modelo->nombre;

            $this->equiposClienteModal = $this->regresaEquiposCliente($this->cliente['id']);

            $this->equipo['estatus'] = 2;   //Estatus de equipo solo 
            
            session()->flash('success', 'El EQUIPO se ha actualizado correctamente.');
        }
        else
        {
            $nuevoEquipo = new Equipo;

            $nuevoEquipo->id_tipo = $this->equipo['idTipo'];
            $nuevoEquipo->id_marca = $this->equipo['idMarca'];
            $nuevoEquipo->id_modelo = $this->equipo['idModelo'];
            $nuevoEquipo->id_cliente = $this->cliente['id'];

            $nuevoEquipo->save();

            $this->equipo['id'] = $nuevoEquipo->id;
            $this->equipo['nombreTipo'] = $nuevoEquipo->tipo_equipo->nombre;
            $this->equipo['nombreMarca'] = $nuevoEquipo->marca->nombre;
            $this->equipo['nombreModelo'] = $nuevoEquipo->modelo->nombre;

            $this->equipo['estatus'] = 2;   //Estatus de equipo solo 
            
            session()->flash('success', 'El EQUIPO se ha agregado correctamente.');
        }
    }

    protected function regresaCliente($telefono)
    {
        $cliente = Cliente::where('telefono', $telefono)->first();

        return $cliente;
    }

    protected function regresaEquipo($idEquipo)
    {
        $equipo = Equipo::find($idEquipo);

        return $equipo;
    }

    public function updatedEquipoIdTipo()
    {
        $this->showMainErrors = false;
        $this->equipo['idMarca'] = '';
        $this->equipoTaller['totalEstimado'] = 0;
        $this->fallas = [];
        $this->fallasCostos = [];
    }

    public function updatedEquipoIdMarca()
    {
        $this->equipo['idModelo'] = '';
        $this->showMainErrors = false;
    }

     public function cierraModeloModal()
    {
        $this->resetValidation();
        $this->modeloMod = [
            'idMarca' => '',
            'nombre'=> ''
        ];
    }

    public function nuevoModeloModal()
    {
        $this->marcaMod['idTipoEquipo'] = $this->equipo['idTipo'];

        $this->modeloMod = [
            'idMarca' => $this->equipo['idMarca'],
            'nombre'=> ''
            ];

        $this->marcasEquiposMod = MarcaEquipo::where('id_tipo_equipo', $this->marcaMod['idTipoEquipo'])->where('disponible', 1)->orderBy('nombre', 'asc')->get();

        $this->guardoModeloOK = false;
    } 
    
    public function modeloYaExiste($idMarca, $nombreModelo)
    {
        $modelo = ModeloEquipo::where('id_marca', $idMarca)->where('nombre', $nombreModelo)->first();

        if (is_null($modelo))
        {
            return 0;   //Para indicar que el nombre del modelo no existe
        }
        else
        {
          $this->marcaMod['tipoEquipo'] = $modelo->marca->tipoEquipo->nombre;
          $this->marcaMod['nombre'] = $modelo->marca->nombre;
          return $modelo->disponible ? 1 : 2;  //Si el modelo está disponible regresa 1 si no regresa 2
        }
    }

    public function guardaModelo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'modeloMod.idMarca' => 'required|not_in:null',
            'modeloMod.nombre'  => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'modeloMod.idMarca.not_in' => 'Por favor selecciona una <b> Marca </b>',
            'modeloMod.idMarca.required' => 'El campo <b> Marca </b> es obligatorio.',
            'modeloMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'modeloMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'modeloMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        
        $estatusModelo = $this->modeloYaExiste($this->modeloMod['idMarca'], trim(mb_strtoupper($this->modeloMod['nombre'])));

        if ($estatusModelo == 1)
        {
            $this->dispatch('mostrarToastError', 'El modelo ' . trim(mb_strtoupper($this->modeloMod['nombre'])) . ' ya existe para la marca ' .  $this->marcaMod['nombre'] . '. Intenta con otro nombre.');

        }
        elseif ($estatusModelo == 2)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->modeloMod['nombre'])) . ' ya existe para la marca ' .  $this->marcaMod['nombre'] . '. Pero tiene estatus NO DISPONIBLE. Intenta con otro nombre.');
        }
        else
        { 
            $modeloEquipo = new ModeloEquipo();
            $modeloEquipo->id_marca = $this->modeloMod['idMarca'];
            $modeloEquipo->nombre = trim(mb_strtoupper($this->modeloMod['nombre']));
            $modeloEquipo->disponible = 1;
            $modeloEquipo->save();
    
            session()->flash('success', 'El MODELO se ha guardado correctamente.');
    
            $this->guardoModeloOK = true;
        }
    }
    
    public function cierraMarcaModal()
    {
        $this->resetValidation();
        $this->marcaMod = [
            'idTipoEquipo' => 1,
            'nombre' => ''
        ];
    }

    public function nuevaMarcaModal()  
    {
        $this->marcaMod = [
            'idTipoEquipo' => $this->equipo['idTipo'],
            'nombre' => ''
        ];

        $this->guardoMarcaOK = false;
    }

    public function marcaYaExiste($idTipoEquipo, $nombreMarca)
    {
        $marca = MarcaEquipo::where('id_tipo_equipo', $idTipoEquipo)->where('nombre', $nombreMarca)->first();

        if (is_null($marca))
        {
            return 0;   //Para indicar que el nombre de la marca no existe
        }
        else
        {
          $this->marcaMod['tipoEquipo'] = $marca->tipoEquipo->nombre;
          return $marca->disponible ? 1 : 2;  //Si la marca está disponible regresa 1 si no regresa 2
        }
    }

    public function guardaMarcaEnTabla()
    {
        try {
            DB::transaction(function () {
                $marcaEquipo = new MarcaEquipo();
                $marcaEquipo->id_tipo_equipo = $this->marcaMod['idTipoEquipo'];
                $marcaEquipo->nombre = trim(mb_strtoupper($this->marcaMod['nombre']));
                $marcaEquipo->disponible = 1;
                $marcaEquipo->save();

                $modeloEquipo = new ModeloEquipo();
                $modeloEquipo->id_marca = $marcaEquipo->id;
                $modeloEquipo->nombre = 'GENÉRICO';
                $modeloEquipo->disponible = 1;
                $modeloEquipo->save();
            });
        } catch (\Exception $e) {
            // Manejo de errores si ocurre una excepción
            // Puedes agregar logs o notificaciones aquí
            dd($e);
        }
    }

    public function guardaMarca()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'marcaMod.nombre' => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'marcaMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'marcaMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'marcaMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        $estatusMarca = $this->marcaYaExiste($this->marcaMod['idTipoEquipo'], trim(mb_strtoupper($this->marcaMod['nombre'])));

        if ($estatusMarca == 1)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->marcaMod['nombre'])) . ' ya existe para el tipo de equipo ' . $this->marcaMod['tipoEquipo'] . '. Intenta con otro nombre.');

        }
        elseif ($estatusMarca == 2)
        {
            $this->dispatch('mostrarToastError', 'La marca ' . trim(mb_strtoupper($this->marcaMod['nombre'])) . ' ya existe para el tipo de equipo ' . $this->marcaMod['tipoEquipo'] . '. Pero tiene estatus NO DISPONIBLE. Intenta con otro nombre.');
        }
        else
        {    
            $this->guardaMarcaEnTabla();

            session()->flash('success', 'La MARCA se ha guardado correctamente.');

            $this->guardoMarcaOK = true;
        }
    }

    // public function updatedNombreClienteModal($value)
    // {
    //     if (strlen($value) == 0) {
    //         $this->clientesModal = null;
    //     } else {
    //         $clientesPaginados = Cliente::where('nombre', 'like', '%' . $value . '%')
    //             ->where('telefono', '!=', '0000000000')
    //             ->where('disponible', 1)
    //             ->paginate(10);
    
    //         $this->clientesModal = $clientesPaginados; // Mantenemos la paginación
    //     }
    // }


    public function capturarFila($clienteId)   //Selecciona un cliente de la tabla de buscar clientes
    {
        $this->cliente['id'] = $clienteId;

        $cliente = Cliente::findOrFail($clienteId);
        $this->cliente['estatus'] = 2;  //Cliente solo lectura
        $this->cliente['telefono'] = $cliente->telefono;
        $this->cliente['nombre'] = $cliente->nombre;
        $this->cliente['direccion'] = $cliente->direccion;
        $this->cliente['telefonoContacto'] = $cliente->telefono_contacto;

        $this->equiposClienteModal = $this->regresaEquiposCliente($cliente->id);
        $this->tieneEquiposCliente = $this->equiposClienteModal->count() > 0 ? true : false;

        $this->dispatch('cerrarModalBuscarCliente');

        $this->modalBuscarClienteAbierta = false;

        if ($this->tieneEquiposCliente)
        {
            $this->hayItemsNoDisponibles = false;
            $this->hayItemsInexistentes = false;
            $this->dispatch('abreModalEquiposCliente');            
        }

        $this->cliente['publicoGeneral'] = false;
        $this->equipoSeleccionadoModal = false;
        $this->equipoTaller['estatus'] = 0;
    }

    public function capturarFilaEquiposCliente($idEquipo)
    {   
        $idTipoAnterior = $this->equipo['idTipo'];
        
        $this->equipo['id'] = $idEquipo;

        $equipo = Equipo::findOrFail($idEquipo);

        if ($equipo->marca->id_tipo_equipo != $equipo->id_tipo)  //MARCA INEXISTENTE
        {
            $this->dispatch('cerrarModalEquiposCliente');
            $this->dispatch('mostrarToastErrorRoute', 'El equipo seleccionado tiene una MARCA INEXISTENTE. Favor de verificar dicho equipo del cliente ' . $this->cliente['nombre'] . '. El sistema se redireccionará a EQUIPOS - CATÁLOGOS.', route('equipos.index'));
        }

        if($equipo->modelo->id_marca != $equipo->marca->id)  //MODELO INEXISTENTE
        {
            $this->dispatch('cerrarModalEquiposCliente');
            $this->dispatch('mostrarToastErrorRoute', 'El equipo seleccionado tiene un MODELO INEXISTENTE. Favor de verificar dicho equipo del cliente ' . $this->cliente['nombre'] . '. El sistema se redireccionará a EQUIPOS - CATÁLOGOS.', route('equipos.index'));
        }

        $this->equipo['idTipo'] = $equipo->id_tipo;
        $this->equipo['idMarca'] = $equipo->id_marca;
        $this->equipo['idModelo'] = $equipo->id_modelo;
        $this->equipo['nombreTipo'] = $equipo->tipo_equipo->nombre;

        if($equipo->marca->disponible)
        {
            $this->equipo['nombreMarca'] = $equipo->marca->nombre;
        }
        else
        {
            $this->equipo['nombreMarca'] = $equipo->marca->nombre . "*";
        }

        if($equipo->modelo->disponible)
        {
            $this->equipo['nombreModelo'] = $equipo->modelo->nombre;
        }
        else
        {
            $this->equipo['nombreModelo'] = $equipo->modelo->nombre . "*";
        }

        $this->equipoSeleccionadoModal = true;
        
        if ($this->equipo['estatus'] < 3)
        {
            if ($idTipoAnterior != $this->equipo['idTipo']) $this->fallas = [];
            $this->imagenes = [];
        }

        if ($idTipoAnterior != $this->equipo['idTipo'])
        {
            $this->equipoTaller['totalEstimado'] = 0;
            $this->fallas = [];
            $this->fallasCostos = [];
        }

        $this->equipo['estatus'] = 2;

        $this->dispatch('cerrarModalEquiposCliente');
    }

    public function abreEquipoClienteHistorial()
    {
        $this->historialEquipoTaller = EquipoTaller::where('id_equipo', $this->equipo['id'])->get();
        $this->muestraHistorialEquipoClienteModal = true;

    }

    public function abreModalBuscarCliente()
    {
        $this->nombreClienteModal = '';

        $this->modalBuscarClienteAbierta = true;

        $this->dispatch('focusInput');

    }

    public function cierraModalBuscarCliente()
    {
        $this->modalBuscarClienteAbierta = false;
    }

    public function abreDivAgregaEquipo()
    {
        $this->muestraDivAgregaEquipo = true;
    }

    public function validarNumeros()
    {
        // Obtener el valor actual del campo de entrada
        $valor = $this->cliente['telefono'];

        // Usar una expresión regular para eliminar cualquier caracter no numérico
        $valorR = preg_replace("/[^0-9]/", "", $valor);

        if (strlen($valor) > 10) {
            $valorR = substr($valor, 0, 10);
        }
        // Actualizar el valor del campo de entrada
        $this->cliente['telefono'] = $valorR;

        return ctype_digit($valor);
    }

    public function validarNumerosContacto()
    {
        // Obtener el valor actual del campo de entrada
        $valor = $this->cliente['telefonoContacto'];

        // Usar una expresión regular para eliminar cualquier caracter no numérico
        $valorR = preg_replace("/[^0-9]/", "", $valor);

        if (strlen($valor) > 10) {
            $valorR = substr($valor, 0, 10);
        }
        // Actualizar el valor del campo de entrada
        $this->cliente['telefonoContacto'] = $valorR;

        return ctype_digit($valor);
    }

    public function nuevoTipoEquipoModal()
    {
        $this->tipoEquipoMod = [
            'nombre' => '',
            'cveNombre' => '',
            'icono' => ''
        ];
    }

    public function guardaTipoEquipo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'tipoEquipoMod.nombre' => ['required', 'string', 'max:15', 'min:1'],
            'tipoEquipoMod.cveNombre' => ['required', 'string', 'max:3', 'min:1'],
            'tipoEquipoMod.icono' => ['required', 'regex:/^<i class="fa-solid (fa-[a-z-]+)"><\/i>$/']

        ], [
            'tipoEquipoMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'tipoEquipoMod.nombre.max' => 'El nombre no puede tener más de 15 caracteres.',
            'tipoEquipoMod.cveNombre.required' => 'El campo <b> Cve. Nombre </b> es obligatorio.',
            'tipoEquipoMod.cveNombre.max' => 'La Cve. Nombre no puede tener más de 15 caracteres.',
            'tipoEquipoMod.icono.required' => 'El campo <b> Ícono </b> es obligatorio.',
            'tipoEquipoMod.icono.regex' => 'El campo <b> Ícono </b> contiene información incorrecta.',
        ]);

        $tipoEquipo = new TipoEquipo();
        $tipoEquipo->nombre = trim(mb_strtoupper($this->tipoEquipoMod['nombre']));
        $tipoEquipo->cve_nombre = trim(mb_strtoupper($this->tipoEquipoMod['cveNombre']));
        $tipoEquipo->icono = $this->tipoEquipoMod['icono'];
        $tipoEquipo->disponible = 1;
        $tipoEquipo->save();

        session()->flash('success', 'El TIPO DE EQUIPO se ha guardado correctamente.');

        $this->guardoTipoEquipoOK = true;
    }
    
    public function cierraTipoEquipoModal() 
    {
        $this->resetValidation();
        $this->tipoEquipoMod = [
            'nombre' => '',
            'cveNombre' => '',
            'icono' => ''
        ];
        $this->guardoTipoEquipoOK = false;
    }


    public function cierraFallaModal()
    { 
        $this->resetValidation();
        $this->guardoFallaOK = false;

        $this->fallaMod = [
            'idTipoEquipo' => 1,
            'descripcion' => '',
            'cveDescripcion' => '',
            'costo' => ''
        ];
    }

    public function nuevaFallaModal()
    {
        $this->fallaMod = [
            'idTipoEquipo' => $this->equipo['idTipo'],
            'descripcion' => '',
            'cveDescripcion' => '',
            'costo' => ''
        ];
    }

    public function guardaFalla()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'fallaMod.descripcion'     => ['required','string','min:1', 'max:30'],
            'fallaMod.cveDescripcion'  => ['required', 'string', 'min:1', 'max:6', ValidationRule::unique('fallas_equipos', 'cve_descripcion')->where('id_tipo_equipo', $this->fallaMod['idTipoEquipo'])],
            'fallaMod.costo'           => ['required','numeric']
        ], [
            'fallaMod.descripcion.required' => 'El campo <b> Descripción </b> es obligatorio.',
            'fallaMod.descripcion.max' => 'La <b> Descripción </b> no puede tener más de 30 caracteres.',
            'fallaMod.descripcion.min' => 'La <b> Descripción </b> debe tener al menos 1 caracter.',
            'fallaMod.cveDescripcion.required' => 'El campo <b> Clave de Descripción </b> es obligatorio.',
            'fallaMod.cveDescripcion.max' => 'La <b> Clave de Descripción </b> no puede tener más de 6 caracteres.',
            'fallaMod.cveDescripcion.min' => 'La <b> Clave de Descripción </b> debe tener al menos 1 caracter.',
            'fallaMod.cveDescripcion.unique' => 'La <b>Clave de Descripción</b> ya está en uso para este tipo de equipo.',
            'fallaMod.costo.required' => 'El campo <b>Costo</b> es obligatorio.',
            'fallaMod.costo.required' => 'El campo <b> Costo </b> es obligatorio.',
            'fallaMod.costo.numeric' => 'El campo <b> Costo </b> debe ser numérico.'
        ]);

        $fallaEquipo = new FallaEquipo();
        $fallaEquipo->id_tipo_equipo = $this->fallaMod['idTipoEquipo'];
        $fallaEquipo->descripcion = trim(mb_strtoupper($this->fallaMod['descripcion']));
        $fallaEquipo->cve_descripcion = trim(mb_strtoupper($this->fallaMod['cveDescripcion']));
        $fallaEquipo->costo = $this->fallaMod['costo'];
        $fallaEquipo->disponible = 1;
        $fallaEquipo->save();

        session()->flash('success', 'La FALLA se ha guardado correctamente.');

        $this->guardoFallaOK = true;
    }

    public function eliminaImagen($i)
    {
        // Elimina el elemento del array en la posición $index
        array_splice($this->imagenes, $i, 1);

        // Reorganiza los índices del array después de eliminar un elemento
        $this->imagenes = array_values($this->imagenes);

        if ($this->numImagenes > 1)
        {
            $this->numImagenes--;
        }
    }

   public function descartaEquipo()
   {
        $this->muestraDivAgregaEquipo = false;
        $this->showModalErrors = false;
        $this->showMainErrors = true;

        $this->resetErrorBag();
        $this->dispatch('descartaEquipo');
        $this->mount();
   }

    #[On('muestraDivAgregaEquipo')] 
    public function muestraDivArriba()
    {
        $this->muestraDivAgregaEquipo = true;
        $this->datosCargados = true;
        $this->equipo['estatus'] = 0;
        $this->cliente['estatus'] = 0;
        $this->equipoTaller['estatus'] = 0;
        // $this->dispatch('refrescaSelectPickers');
    }

    // #[On('ocultaDivAgregaEquipo')] 
    // public function ocultaDivArriba()
    // {
    //     $this->muestraDivAgregaEquipo = false;
    // }

    #[On('editaEquipoTaller')] 
    public function editaEquipo($numOrden)
    {
        // if (!$this->muestraDivAgregaEquipo) 
        $this->muestraDivAgregaEquipo = true;
        
        $equipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
        $this->cliente['id'] = $equipoTaller->equipo->cliente->id;
        $this->cliente['telefono'] = $equipoTaller->equipo->cliente->telefono;
        $this->cliente['nombre'] = $equipoTaller->equipo->cliente->nombre;
        $this->cliente['direccion'] = $equipoTaller->equipo->cliente->direccion;
        $this->cliente['telefonoContacto'] = $equipoTaller->equipo->cliente->telefono_contacto;
        $this->cliente['estatus'] = 3;
        $this->cliente['publicoGeneral'] = $this->cliente['telefonoContacto'] == "0000000000" ? true : false;

        $this->equipo['id'] = $equipoTaller->id_equipo;
        $this->equipo['idTipo'] = $equipoTaller->equipo->id_tipo;

        // if($equipoTaller->equipo->marca->disponible)
        // {
        //     $this->equipo['nombreMarca'] = $equipoTaller->equipo->marca->nombre;
        // }
        // else
        // {
        //     $this->equipo['nombreMarca'] = $equipoTaller->equipo->marca->nombre . "*";
        // }

        // if($equipoTaller->equipo->modelo->disponible)
        // {
        //     $this->equipo['nombreModelo'] = $equipoTaller->equipo->modelo->nombre;
        // }
        // else
        // {
        //     $this->equipo['nombreModelo'] = $equipoTaller->equipo->modelo->nombre . "*";
        // }

        $this->equipo['nombreTipo'] = $equipoTaller->equipo->tipo_equipo->nombre;
        if($equipoTaller->equipo->modelo->disponible)
        {
            if($equipoTaller->equipo->modelo->id_marca === $equipoTaller->equipo->marca->id)
            {
                $this->equipo['idModelo'] = $equipoTaller->equipo->id_modelo;
            }
            else
            {
                $this->equipo['idModelo'] = null;
            }
        }
        else
        {
            $this->equipo['idModelo'] = null;
        }
        
        if($equipoTaller->equipo->marca->disponible)
        {
            if($equipoTaller->equipo->marca->id_tipo_equipo === $equipoTaller->equipo->id_tipo)
            {
                $this->equipo['idMarca'] = $equipoTaller->equipo->id_marca;
            }
            else
            {
                $this->equipo['idMarca'] = null;
                $this->equipo['idModelo'] = null;
            }
        }
        else
        {
            $this->equipo['idMarca'] = null;
            $this->equipo['idModelo'] = null;
        }
        $this->equipo['nombreMarca'] = $equipoTaller->equipo->marca->nombre;


        $this->equipo['nombreModelo'] = $equipoTaller->equipo->modelo->nombre;
        $this->equipo['estatus'] = 3;

        $this->equipoTaller['numOrden'] = $numOrden;
        $this->equipoTaller['idEstatus'] = $equipoTaller->id_estatus;
        $this->equipoTaller['observaciones'] = $equipoTaller->observaciones;
        $this->equipoTaller['estatus'] = 1;

        $cobroEstimado = CobroEstimadoTaller::where('num_orden', $numOrden)->first();

        if ($cobroEstimado->credito) {
            $detalle = $cobroEstimado->credito->detalles()->where('id_abono', 0)->first();
            $this->equipoTaller['anticipo'] = $detalle ? $detalle->abono : 0; //Determina si hay anticipo
            $this->equipoTaller['idModoPagoAnticipo'] = $detalle ? $detalle->id_modo_pago : 1; // Determina el modo de pago del anticipo si existe
        } else 
        {
            $this->equipoTaller['anticipo'] = 0;
        }

        $this->fallasCostos = [];

        $fallas = FallaEquipoTaller::where('num_orden', $numOrden)->get();

        foreach($fallas as $falla)
        {
            $this->fallasCostos[$falla->id_falla] = $falla->falla->costo;
        }

        $this->equipoTaller['totalEstimado'] = array_sum($this->fallasCostos);

        $fallasEquipoTaller = FallaEquipoTaller::where('num_orden', $numOrden)->get();

        $this->fallas = [];

        foreach($fallasEquipoTaller as $fallaEquipoTaller)
        {
            $this->fallas[$fallaEquipoTaller->id_falla] = true;
        }

        $this->imagenes = [];

        $imagenesEquipoTaller = ImagenEquipo::where('num_orden', $numOrden)->get();        
        $j = 0;

        foreach($imagenesEquipoTaller as $imagenEquipoTaller)
        {
            $this->imagenes[$j] = $imagenEquipoTaller->nombre_archivo;
            $j++;
        }

        $this->numImagenesEdit = $j;
    }

}




