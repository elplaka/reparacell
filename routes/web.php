<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EquipoTallerController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\MarcaEquipoController;
use App\Http\Controllers\FallaEquipoController;
use App\Http\Controllers\ModeloEquipoController;
use App\Http\Controllers\ProductoController;
use App\Livewire\Caja;
use Barryvdh\Snappy\Facades\SnappyPdf;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return view('home'); // Cambia 'home' por el nombre de tu vista home
    }

    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
});

Route::get('/test-pdf', function () {
    $data = [
        'title' => 'Prueba de PDF',
        'content' => '¡Hola! Este es un PDF de prueba generado con SnappyPDF en Laravel.',
    ];

    $pdf = SnappyPdf::loadView('livewire.corte-caja', $data);

    return $pdf->stream('test.pdf');
});


Route::middleware(['auth'])
->group(function () {
    Route::get('/taller/index', [EquipoTallerController::class, 'index'])->name('taller.index');
    Route::get('/taller/print/{num_orden}', [EquipoTallerController::class, 'print'])->name('taller.print');
    Route::get('/taller/print-final/{num_orden}', [EquipoTallerController::class, 'print_final'])->name('taller.print-final');
    Route::get('/caja/index', [CajaController::class, 'index'])->name('caja.index');
    Route::get('/caja/corte', [Caja::class, 'generaCorteCajaPDF'])->name('corte-caja');

    Route::get('/equipos/fallas', [FallaEquipoController::class, 'index'])->name('equipos.fallas');
    Route::get('/equipos/marcas', [MarcaEquipoController::class, 'index'])->name('equipos.marcas');
    Route::get('/equipos/modelos', [ModeloEquipoController::class, 'index'])->name('equipos.modelos');

    Route::get('/productos/index', [ProductoController::class, 'index'])->name('productos.index');
});

Route::middleware(['auth'])
->get('/storage/{archivo}', function ($archivo) {
    return Storage::disk('public')->response($archivo);
})->where('archivo', '.*');

  
// Rutas protegidas para administradores
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/register', function () {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('auth.register', compact('roles'));
    })->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/usuarios/index', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::get('/usuarios/edit/{id_usuario}', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::post('/usuarios/update/{id_usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');    
});



