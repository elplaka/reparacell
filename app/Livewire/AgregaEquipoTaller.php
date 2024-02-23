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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\On; 


class AgregaEquipoTaller extends Component
{
    use WithFileUploads;
    use LivewireAlert;

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
        'estatus'  //0: AGREGAR EQUIPO, 1: EDITAR
    ];

    public $fallas = [];
    public $fallasE = [];
    public $numImagenes;
    public $numImagenesEdit;  
    public $showMainErrors, $showModalErrors;
    public $muestraDivAgregaEquipo;
    public $nombreClienteModal;
    public $clientesModal;
    public $tieneEquiposCliente;
    public $equiposClienteModal;
    public $equipoSeleccionadoModal;
    public $historialEquipoTaller;
    public $historialClienteTaller;

    public $muestraHistorialClienteModal;
    public $muestraHistorialEquipoClienteModal;
 
    public $imagenes = [];

    public $tipoEquipoMod = [
        'nombre',
        'cveNombre',
        'icono'
    ];

    public $marcaMod = [
        'idTipoEquipo',
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

    public function mount()
    {
        $this->cliente = [
            'estatus'           => 0,
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
            'observaciones' => null
        ];

        $this->marcaMod = [
            'idTipoEquipo' => 1,
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
        $this->fallasE = [];
        $this->imagenes = [];
        $this->numImagenes = 1;
        $this->showModalErrors = false;
        $this->showMainErrors = true;
        $this->muestraDivAgregaEquipo = false;
        $this->nombreClienteModal = '';
        $this->clientesModal = null;
        $this->mostrarModalBuscarCliente = false;
        $this->tieneEquiposCliente = false;
        $this->equipoSeleccionadoModal = true;

        $this->muestraHistorialClienteModal = false;
        $this->muestraHistorialEquipoClienteModal = false;
    }

    public function render()
    {
        $tipos_equipos = TipoEquipo::all();
        $marcas_equipos = MarcaEquipo::where('id_tipo_equipo', $this->equipo['idTipo'])
        ->orderBy('nombre', 'asc') // Ordenar por nombre de manera ascendente (A-Z)
        ->get();

        $modelos_equipos = ModeloEquipo::where('id_marca', $this->equipo['idMarca']) ->orderBy('nombre', 'asc')->get();
        $fallas_equipos = FallaEquipo::where('id_tipo_equipo', $this->equipo['idTipo'])->get();
        $estatus_equipos = EstatusEquipo::all();

        return view('livewire.agrega-equipo-taller', compact('tipos_equipos', 'marcas_equipos', 'modelos_equipos', 'fallas_equipos', 'estatus_equipos'));
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
        ->whereNotIn('id_estatus', [4, 6])
        ->first();

        return $equipoTaller ? true : false;
    }

    protected function cobroRepetido($numOrden, $cobroEstimado)
    {
        $cobroEstimado = CobroEstimadoTaller::where('num_orden', $numOrden)->where('cobro_estimado', $cobroEstimado)->first();

        return $cobroEstimado ? true : false;
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
            'equipoTaller.observaciones' => ['nullable', 'max:50']
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
                else  //Si no existe el equipo se agregar
                {
                    $nuevoEquipo = new Equipo;

                    $nuevoEquipo->id_tipo = $this->equipo['idTipo'];
                    $nuevoEquipo->id_marca = $this->equipo['idMarca'];
                    $nuevoEquipo->id_modelo = $this->equipo['idModelo'];
                    $nuevoEquipo->id_cliente = $this->cliente['id'];
        
                    $nuevoEquipo->save();

                    $this->equipo['id'] = $nuevoEquipo->id;
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
                    $catFallas = FallaEquipo::find($fallaId);
                    $cobroEstimado += $catFallas->costo;
                    $falla->save();
                    $k++;
                }

                if (!$this->cobroRepetido($numOrden, $cobroEstimado))
                {
                    $ultimoRegistro = CobroEstimadoTaller::where('num_orden', $numOrden)->latest('id')->first();

                    if ($ultimoRegistro) {
                        // Si se encontró un registro previo, incrementa el valor 'id' en 1.
                        $maxId = $ultimoRegistro->id + 1;
                    } else {
                        // Si no se encontraron registros previos, establece el valor de 'id' en 1.
                        $maxId = 1;
                    }

                    $cobroEstimadoTaller = new CobroEstimadoTaller();
                    $cobroEstimadoTaller->id = $maxId;
                    $cobroEstimadoTaller->num_orden = $numOrden;
                    $cobroEstimadoTaller->cobro_estimado = $cobroEstimado;
                    $cobroEstimadoTaller->save();

                    $actualizaEquipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                    $actualizaEquipoTaller->id_cobro_estimado = $maxId;
                    $actualizaEquipoTaller->save();
                }

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
            'equipoTaller.observaciones' => ['nullable', 'max:50']
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
                $equipo_taller->observaciones = "NINGUNA";
                $equipo_taller->save();

                $numOrden = $equipo_taller->num_orden;

                //array_filter se utiliza para filtrar los elementos con valor true, y luego array_keys se utiliza para obtener los índices correspondientes.
                $idsFallas = array_keys(array_filter($this->fallas, function ($valor) {
                    return $valor === true;
                }));

                $k = 0;
                $cobroEstimado = 0;
                foreach ($this->fallas as $fallaId) {
                    $falla = new FallaEquipoTaller();
                    $falla->num_orden = $numOrden;
                    $falla->id_falla = $idsFallas[$k];
                    $catFallas = FallaEquipo::find($idsFallas[$k]);
                    $cobroEstimado += $catFallas->costo;
                    $falla->save();
                    $k++;
                }

                if (!$this->cobroRepetido($numOrden, $cobroEstimado))
                {
                    $ultimoRegistro = CobroEstimadoTaller::where('num_orden', $numOrden)->latest('id')->first();

                    if ($ultimoRegistro) {
                        // Si se encontró un registro previo, incrementa el valor 'id' en 1.
                        $maxId = $ultimoRegistro->id + 1;
                    } else {
                        // Si no se encontraron registros previos, establece el valor de 'id' en 1.
                        $maxId = 1;
                    }

                    $cobroEstimadoTaller = new CobroEstimadoTaller();
                    $cobroEstimadoTaller->id = $maxId;
                    $cobroEstimadoTaller->num_orden = $numOrden;
                    $cobroEstimadoTaller->cobro_estimado = $cobroEstimado;
                    $cobroEstimadoTaller->save();
                }

                $actualizaEquipoTaller = EquipoTaller::where('num_orden', $numOrden)->first();
                $actualizaEquipoTaller->id_cobro_estimado = $maxId;
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

                $this->muestraDivAgregaEquipo = false;
                $this->dispatch('ocultaDivAgregaEquipo');
                $this->dispatch('mostrarToast', 'Equipo agregado con éxito!!!');

                $this->mount();

                return redirect()->route('taller.print', $numOrden);
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
        $this->equiposClienteModal = $this->regresaEquiposCliente($this->cliente['id']);
    }

    public function updatedClienteTelefono($value)
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

                    if ($this->tieneEquiposCliente && $this->cliente['telefonoContacto'] != "0000000000")
                    {
                        $this->dispatch('abreModalEquiposCliente');    //El listener está en layouts.main
                    }

                    if ($this->cliente['telefonoContacto'] == "0000000000")
                    {
                        $this->equipo['estatus'] = 1;
                        $this->cliente['publicoGeneral'] = true;
                        $this->cliente['estatus'] = 2;
                    }

                    $this->equipoTaller['estatus'] = 1;
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
                }
                else  
                {
                    // $this->cliente['estatus'] = 2;
                    if (strlen($value) <= 10) 
                    {
                        $this->cliente['publicoGeneral'] = false;
                    }
                }
            }
        }
    }
    
    public function abreClienteHistorial()
    {
        $this->historialClienteTaller = Equipo::join('equipos_taller', 'equipos_taller.id_equipo', '=', 'equipos.id')
        ->where('equipos.id_cliente', $this->cliente['id'])
        ->orderBy('equipos_taller.fecha_salida')
        ->orderBy('equipos_taller.num_orden')
        ->select('equipos.*', 'equipos_taller.num_orden','equipos_taller.id_estatus', 'equipos_taller.fecha_salida', 'equipos_taller.observaciones')  
        ->get();

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

    public function regresaEquiposCliente($idCliente)
    {
        $equipos = Equipo::where('id_cliente', $idCliente)->get();

        return $equipos;
    }

    public function updatedEquipoIdTipo()
    {
        $this->showMainErrors = false;
        $this->equipo['idMarca'] = '';
        $this->fallas = [];
    }

    public function updatedEquipoIdMarca()
    {
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
        // $this->modeloMod['idMarca'] = $this->equipo['idMarca'];

        $this->marcasEquiposMod = MarcaEquipo::where('id_tipo_equipo', $this->marcaMod['idTipoEquipo'])
                                  ->orderBy('nombre', 'asc')->get();

        $this->modeloMod = [
        'idMarca' => $this->equipo['idMarca'],
        'nombre'=> ''
        ];
    }    

    public function guardaModelo()
    {
        $this->showModalErrors = true;
        $this->showMainErrors = false;

        $this->validate([
            'modeloMod.idMarca' => 'required|not_in:null',
            'modeloMod.nombre'      => ['required', 'string', 'max:20', 'min:1'],
        ], [
            'modeloMod.idMarca.not_in' => 'Por favor selecciona una <b> Marca </b>',
            'modeloMod.idMarca.required' => 'El campo <b> Marca </b> es obligatorio.',
            'modeloMod.nombre.required' => 'El campo <b> Nombre </b> es obligatorio.',
            'modeloMod.nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'modeloMod.nombre.min' => 'El nombre debe tener al menos 1 caracter.',
        ]);

        $modeloEquipo = new ModeloEquipo();
        $modeloEquipo->id_marca = $this->modeloMod['idMarca'];
        $modeloEquipo->nombre = trim(mb_strtoupper($this->modeloMod['nombre']));
        $modeloEquipo->disponible = 1;
        $modeloEquipo->save();

        session()->flash('success', 'El MODELO se ha guardado correctamente.');

        $this->guardoModeloOK = true;
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
        // $this->marcaMod['idTipoEquipo'] = $this->equipo['idTipo'];
        $this->marcaMod = [
            'idTipoEquipo' => $this->equipo['idTipo'],
            'nombre' => ''
        ];
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

        session()->flash('success', 'La MARCA se ha guardado correctamente.');

        $this->guardoMarcaOK = true;
    }

    public function updatedNombreClienteModal($value)
    {
        if (strlen($value) == 0) $this->clientesModal = null;
        else $this->clientesModal = Cliente::where('nombre', 'like', '%' . $value . '%')
        ->where('telefono', '!=', '0000000000')
        ->get();

        if (!is_null($this->clientesModal) && $this->clientesModal->count() == 0) 
        {
            $this->clientesModal = null;
        }
    }

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

        if ($this->tieneEquiposCliente)
        {
            $this->dispatch('abreModalEquiposCliente');            
        }

        $this->cliente['publicoGeneral'] = false;
        $this->equipoSeleccionadoModal = false;
    }

    public function capturarFilaEquiposCliente($idEquipo)
    {        
        $this->equipo['id'] = $idEquipo;

        $equipo = Equipo::findOrFail($idEquipo);

        $this->equipo['idTipo'] = $equipo->id_tipo;
        $this->equipo['idMarca'] = $equipo->id_marca;
        $this->equipo['idModelo'] = $equipo->id_modelo;
        $this->equipo['nombreTipo'] = $equipo->tipo_equipo->nombre;
        $this->equipo['nombreMarca'] = $equipo->marca->nombre;
        $this->equipo['nombreModelo'] = $equipo->modelo->nombre;
        
        $this->equipoSeleccionadoModal = true;
        
        $this->equipo['estatus'] = 2;
        $this->fallas = [];
        $this->imagenes = [];

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
        $this->clientesModal = null;
    }

    public function cierraModalBuscarCliente()
    {

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
        $valor = preg_replace("/[^0-9]/", "", $valor);

        if (strlen($valor) > 10) {
            $valor = substr($valor, 0, 10);
        }
        // Actualizar el valor del campo de entrada
        $this->cliente['telefono'] = $valor;

        // if (strlen($this->cliente['telefono']) < 10)  $this->cliente['estatus'] = 2;
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
        $this->equipo['estatus'] = 0;
        $this->cliente['estatus'] = 0;
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
        $this->equipo['nombreTipo'] = $equipoTaller->equipo->tipo_equipo->nombre;
        $this->equipo['idMarca'] = $equipoTaller->equipo->id_marca;
        $this->equipo['nombreMarca'] = $equipoTaller->equipo->marca->nombre;
        $this->equipo['idModelo'] = $equipoTaller->equipo->id_modelo;
        $this->equipo['nombreModelo'] = $equipoTaller->equipo->modelo->nombre;
        $this->equipo['estatus'] = 3;

        $this->equipoTaller['numOrden'] = $numOrden;
        $this->equipoTaller['idEstatus'] = $equipoTaller->id_estatus;
        $this->equipoTaller['observaciones'] = $equipoTaller->observaciones;
        $this->equipoTaller['estatus'] = 1;

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




