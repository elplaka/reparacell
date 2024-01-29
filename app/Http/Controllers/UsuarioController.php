<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{

    public function index()
    {
        $usuarios = User::all();

        return view('usuarios.index', compact('usuarios'));
    }

    protected function passwordRules()
    {
        return ['required', 'string', 'min:3', 'confirmed'];
    }

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(Request $request)
    {
        $input = $request->all();
        
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);
    
        // Personalizar mensajes para reglas específicas
        $validator->setCustomMessages([
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'confirmed' => 'Las contraseñas no coinciden. Por favor, confirma la contraseña correctamente.',
            // Agrega más mensajes personalizados aquí
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user =  User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $role = Role::findByName($input['role']); 
        $user->assignRole($role);

        return redirect()->route('usuarios.index');
    }

    public function edit($id_usuario)
    {
        $user = User::find($id_usuario);
        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('usuarios.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id_usuario)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

         // Personalizar mensajes para reglas específicas
         $validator->setCustomMessages([
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'confirmed' => 'Las contraseñas no coinciden. Por favor, confirma la contraseña correctamente.',
            // Agrega más mensajes personalizados aquí
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buscar el usuario por su ID
        $user = User::find($id_usuario);

        // Verificar si el usuario existe
        if (!$user) {
            return redirect()->back()->with('error', 'El usuario no existe.'); // Puedes personalizar este mensaje de error
        }

        // Actualizar los campos del usuario
        $user->name = $input['name'];
        $user->email = $input['email'];

        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }

        // Guardar los cambios en la base de datos
        $user->save();

        $role = Role::findByName($input['role']); 
        $user->assignRole($role);

        return redirect()->route('usuarios.index');
    }

}
