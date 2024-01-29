@extends('layouts.main')

<?php 
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

// $user = Auth::user();
// $roles = $user->getRoleNames();

?>
@section('content')
<div class="row justify-content-center align-items-start min-vh-100 mt-5">
    <x-guest-layout>
        <div class="w-full sm:max-w-md mt-36 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold"><b>Usuarios</b></h1>
                <x-button style="padding: 6px 8px !important;"> <!-- Aplica el estilo directamente dentro del botón -->
                    <a href="{{ route('register') }}" class="text-white no-underline" style="text-decoration: none;" title="Nuevo usuario">
                        <span>+</span>
                    </a>
                </x-button>
            </div>
             <x-validation-errors class="mb-4" />
             <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-100 align-items-start">
                     <thead>
                         <tr>
                             <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Nombre</th>
                             <th class="px-6 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Correo Electrónico</th>
                             <th class="px-2 py-2 bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">Rol</th>
                             <th class="px-2 py-2  bg-gray-200 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider"><i class="fas fa-list"></i></th>
                             <!-- Agrega más columnas si es necesario -->
                         </tr>
                     </thead>
                     <tbody>
                         @foreach ($usuarios as $usuario)
                             <tr class="even:bg-gray-50 odd:bg-white">
                                 <td class="px-2 py-1 whitespace-no-wrap">{{ $usuario->name }}</td>
                                 <td class="px-6 py-1 whitespace-no-wrap">{{ $usuario->email }}</td>
                                 <td class="px-2 py-1 whitespace-no-wrap">
                                    @php $rolesUsuario = $usuario->getRoleNames(); @endphp
                                    @foreach ($rolesUsuario as $rol)
                                        {{ $rol }}
                                    @endforeach
                                </td>
                                <td class="px-2 py-0 whitespace-no-wrap">
                                    <a href="{{ route('usuarios.edit', ['id_usuario' => $usuario->id]) }}" class="btn btn-xs-verde">
                                        <i class="fas fa-edit"></i> 
                                    </a>
                                </td>
                                 <!-- Agrega más celdas si es necesario -->
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
        </div>
     </x-guest-layout>
 </div> 
@endsection


