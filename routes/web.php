<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EquipoTallerController;
use App\Http\Controllers\CajaController;

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
    return view('welcome');
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

Route::middleware(['auth'])
->group(function () {
    Route::get('/taller/index', [EquipoTallerController::class, 'index'])->name('taller.index');
    Route::get('/taller/print/{num_orden}', [EquipoTallerController::class, 'print'])->name('taller.print');
    Route::get('/taller/print-final/{num_orden}', [EquipoTallerController::class, 'print_final'])->name('taller.print-final');
    Route::get('/caja/index', [CajaController::class, 'index'])->name('caja.index');
    
    
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

