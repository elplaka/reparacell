@extends('layouts.main')

@section('content')

<x-guest-layout>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <x-authentication-card class="mt-0">
                    <h1 class="text-2xl font-bold; mb-4"><b>Editar Usuario</b></h1>
                    <x-validation-errors class="mb-4" />

                    <form method="POST" action="{{ route('usuarios.update', ['id_usuario' => $user->id]) }}">
                        @csrf

                        <div>
                            <x-label for="name" value="{{ __('Nombre') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        </div>

                        <div class="mt-4">
                            <x-label for="email" value="{{ __('Correo Electrónico') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                        </div>

                        <div class="mt-4">
                            <label for="role" class="block font-medium text-sm text-gray-700">{{ __('Rol') }}</label>
                            <select id="role" name="role" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" @if ($role->name === $user->getRoleNames()->first()) selected @endif>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mt-4">
                            <x-label for="password" value="{{ __('Contraseña') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        </div>

                        <div class="mt-4">
                            <x-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        </div>

                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="mt-4">
                                <x-label for="terms">
                                    <div class="flex items-center">
                                        <x-checkbox name="terms" id="terms" required />

                                        <div class="ml-2">
                                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                            ]) !!}
                                        </div>
                                    </div>
                                </x-label>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                                {{ __('ACTUALIZAR') }}
                            </x-button>
                        </div>
                    </form>
                </x-authentication-card>
            </div>
        </div>
    </div>
</x-guest-layout>
@endsection