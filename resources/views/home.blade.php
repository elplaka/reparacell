@extends('layouts.main')

@section('content')
@php
    Use App\Models\User;
    
    $user = User::find(1); // Reemplaza $user_id con el ID del usuario que deseas consultar.
    $permissions = $user->permissions;
    foreach ($permissions as $permission) {
    echo $permission->name . PHP_EOL;
}
@endphp
<div class="container">
    <div class="row justify-content-center">
    </div>
</div>
@endsection